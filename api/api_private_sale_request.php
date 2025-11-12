<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

function ok(array $data = []): void {
  echo json_encode(['success' => true] + $data, JSON_UNESCAPED_SLASHES);
  exit;
}
function fail(string $msg, int $code = 400, array $extra = []): void {
  http_response_code($code);
  $p = ['success' => false, 'error' => $msg];
  if (APP_ENV !== 'production' && $extra) $p['detail'] = $extra;
  echo json_encode($p, JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('Invalid request method.', 405);

  $raw = file_get_contents('php://input');
  if ($raw === false || $raw === '') fail('No payload.');
  $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

  // CSRF
  $csrf = (string)($data['csrf'] ?? '');
  if (function_exists('csrf_require')) {
    try { csrf_require($csrf); } catch (\Throwable $t) { fail('Security token mismatch.', 403); }
  } else {
    if (empty($_SESSION['csrf']) || $csrf === '' || !hash_equals($_SESSION['csrf'], $csrf)) {
      fail('Security token mismatch.', 403);
    }
  }

  // Honeypot
  if (!empty($data['hp'] ?? '')) fail('Bot detected.', 400);

  // Fields
  $name       = trim((string)($data['name'] ?? ''));
  $phone      = trim((string)($data['phone'] ?? ''));
  $email      = trim((string)($data['email'] ?? ''));
  $teaTypes   = (array)($data['teaTypes'] ?? []);
  $gardenMark = trim((string)($data['gardenMark'] ?? ''));
  $grade      = trim((string)($data['grade'] ?? ''));
  $packages   = trim((string)($data['packages'] ?? ''));
  $netWeight  = trim((string)($data['netWeight'] ?? ''));

  if ($name === '' || $phone === '' || $email === '' || $grade === '' || $netWeight === '' || empty($teaTypes)) {
    fail('Missing required fields.');
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email.');
  if (!is_numeric($netWeight) || (float)$netWeight <= 0) fail('Invalid net weight.');

  // Throttle 60s
  $now = time(); $key = 'ps_req_last'; $last = (int)($_SESSION[$key] ?? 0);
  if ($now - $last < 60) fail('Please wait a moment before sending again.', 429);

  // reCAPTCHA
  $capResp = (string)($data['g-recaptcha-response'] ?? '');
  if (RECAPTCHA_SECRET) {
    $cap = recaptcha_verify($capResp, $_SERVER['REMOTE_ADDR'] ?? '');
    if (empty($cap['ok'])) fail($cap['err'] ?? 'CAPTCHA not valid.');
  } elseif (APP_ENV === 'production') {
    fail('CAPTCHA not configured.', 500);
  }

  // Compose
  $ref     = 'PSR-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
  $dateStr = date('Y-m-d H:i:s');
  $types   = implode(', ', array_map('strval', $teaTypes));

  // Admin email (plain)
  $adminBody = implode("\r\n", [
    'Private Sale — Request',
    'Ref:    ' . $ref,
    'Date:   ' . $dateStr,
    '',
    'Name:   ' . $name,
    'Phone:  ' . $phone,
    'Email:  ' . $email,
    'Tea:    ' . $types,
    'Grade:  ' . $grade,
    'Garden: ' . ($gardenMark !== '' ? $gardenMark : '—'),
    'Pkgs:   ' . ($packages !== '' ? $packages : '—'),
    'Net kg: ' . $netWeight,
  ]);

  $okAdmin = send_mail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, "Private Sale Request from {$name} ({$grade})", $adminBody);
  if (!$okAdmin) fail('Message could not be sent. Please try later.', 500);

  // Ack to user (HTML)
  $tplPath = __DIR__ . '/../templates/email/ack_private_sale_request.html';
  $vars = [
    'name'          => $name,
    'ref'           => $ref,
    'date'          => $dateStr,
    'tea_types'     => $types,
    'garden_mark'   => ($gardenMark !== '' ? $gardenMark : '—'),
    'grade'         => $grade,
    'packages'      => ($packages !== '' ? $packages : '—'),
    'net_weight'    => $netWeight,
    'app_name'      => APP_NAME,
    'portal_url'    => APP_URL,
    'support_email' => NOTIFY_TO_EMAIL,
  ];
  $html = is_file($tplPath) ? email_template_render($tplPath, $vars) : '';

  $ackSubj = "We received your Private Sale request (Ref: {$ref})";
  $ackTxt  = implode("\r\n", [
    "Hi {$name},",
    "",
    "Thanks for your Private Sale request. We’ll respond shortly.",
    "Reference: {$ref}",
    "Date: {$dateStr}",
    "Tea Types: {$types}",
    "Grade: {$grade}",
    "Garden Mark: " . $vars['garden_mark'],
    "Packages: " . $vars['packages'],
    "Net Weight (kg): {$netWeight}",
    "",
    "— " . APP_NAME . " (" . APP_URL . ")",
  ]);

  if ($html) {
    // Use PHPMailer directly for HTML
    $ack = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
      if (SMTP_HOST && SMTP_USER && SMTP_PASS) {
        $ack->isSMTP();
        $ack->Host = SMTP_HOST; $ack->Port = SMTP_PORT; $ack->SMTPAuth = true;
        $sec = strtolower(SMTP_SECURE);
        $ack->SMTPSecure = ($sec === 'ssl') ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
                                            : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $ack->Username = SMTP_USER; $ack->Password = SMTP_PASS;
      } else {
        $ack->isMail();
      }
      $ack->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
      $ack->addAddress($email, $name);
      $ack->Subject = $ackSubj;
      $ack->isHTML(true);
      $ack->Body = $html;
      $ack->AltBody = $ackTxt;
      $ack->send();
    } catch (\Throwable $t) { /* ignore ack errors */ }
  } else {
    @send_mail($email, $name, $ackSubj, $ackTxt);
  }

  $_SESSION[$key] = $now;
  ok();
} catch (\Throwable $e) {
  if (APP_ENV !== 'production') error_log('PS REQUEST ERROR: '.$e->getMessage());
  fail('Server error. Please try again.', 500);
}