<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config.php';
use PHPMailer\PHPMailer\PHPMailer;

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

function fail(string $msg, int $code = 400, array $extra = []): void {
  http_response_code($code);
  $payload = ['success' => false, 'error' => $msg];
  if (!empty($extra) && (($_ENV['APP_ENV'] ?? 'production') !== 'production')) {
    $payload['detail'] = $extra;
  }
  echo json_encode($payload, JSON_UNESCAPED_SLASHES);
  exit;
}

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') fail('Invalid request method.', 405);
  $data = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);

  // --- CSRF ---
  $csrf = $data['csrf'] ?? '';
  if (empty($csrf) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $csrf)) {
    fail('Security token mismatch.', 403);
  }

  // --- Honeypot ---
  if (!empty($data['hp'])) fail('Bot detected.', 400);

  // --- Fields ---
  $company = trim((string)($data['company'] ?? ''));
  $name    = trim((string)($data['name'] ?? $company));   // ✅ added name
  $email   = trim((string)($data['email'] ?? ''));
  $phone   = trim((string)($data['phone'] ?? ''));
  $type    = trim((string)($data['type'] ?? ''));
  $notes   = trim((string)($data['notes'] ?? ''));

  if ($company === '' || $email === '' || $phone === '' || $type === '') fail('Missing required fields.');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.');

  // --- reCAPTCHA ---
  $capResp = (string)($data['g-recaptcha-response'] ?? '');
  if (RECAPTCHA_SITE_KEY && RECAPTCHA_SECRET) {
    $res = recaptcha_verify($capResp, $_SERVER['REMOTE_ADDR'] ?? '');
    if (empty($res['ok'])) fail($res['err'] ?? 'CAPTCHA verification failed.');
  }

  // --- Throttle ---
  $now  = time();
  $last = (int)($_SESSION['last_request_time'] ?? 0);
  if ($now - $last < 60) fail('Please wait a moment before sending again.', 429);

  // --- Labels ---
  $labels = [
    'general-data'       => 'General Data',
    'weekly-catalogues'  => 'Weekly Catalogues',
    'historical-prices'  => 'Historical Prices',
    'market-reports'     => 'Market Reports',
  ];
  $dataset = $labels[$type] ?? ucfirst(str_replace('-', ' ', $type));
  $ref     = 'RQ-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
  $dateStr = date('Y-m-d H:i:s');

  // --- Notify admin (plain) ---
  $bodyAdmin = implode("\r\n", [
    'New data request received',
    'Ref:    ' . $ref,
    'Date:   ' . $dateStr,
    '',
    'Company: ' . $company,
    'Contact: ' . $name,          // ✅ include contact name
    'Email:   ' . $email,
    'Phone:   ' . $phone,
    'Dataset: ' . $dataset,
    '',
    'Notes:',
    ($notes ?: '—'),
  ]);

  $mail = new PHPMailer(true);
  $mail->isSMTP();
  $mail->Host       = SMTP_HOST;
  $mail->Port       = SMTP_PORT;
  $mail->SMTPAuth   = true;
  $mail->Username   = SMTP_USER;
  $mail->Password   = SMTP_PASS;
  $mail->SMTPSecure = (strtolower(SMTP_SECURE) === 'ssl')
    ? PHPMailer::ENCRYPTION_SMTPS
    : PHPMailer::ENCRYPTION_STARTTLS;
  $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
  $mail->addAddress(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME);
  $mail->addReplyTo($email, $name);
  // ✅ updated subject text
  $mail->Subject = "Data Request from {$company} ({$dataset})";
  $mail->Body    = $bodyAdmin;
  $mail->AltBody = $bodyAdmin;
  $mail->send();

  // --- Acknowledge to user (HTML template) ---
  $ack = new PHPMailer(true);
  $ack->isSMTP();
  $ack->Host       = SMTP_HOST;
  $ack->Port       = SMTP_PORT;
  $ack->SMTPAuth   = true;
  $ack->Username   = SMTP_USER;
  $ack->Password   = SMTP_PASS;
  $ack->SMTPSecure = (strtolower(SMTP_SECURE) === 'ssl')
    ? PHPMailer::ENCRYPTION_SMTPS
    : PHPMailer::ENCRYPTION_STARTTLS;
  $ack->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
  $ack->addAddress($email, $name ?: $company);
  // ✅ updated subject text
  $ack->Subject = "We received your data request (Ref: {$ref})";

  // --- HTML Template ---
  $tpl = __DIR__ . '/../templates/email/ack_request.html';
  $html = '';
  if (is_file($tpl)) {
    $html = email_template_render($tpl, [
      'name'          => $name,            // ✅ added name
      'ref'           => $ref,
      'date'          => $dateStr,
      'company'       => $company,
      'email'         => $email,
      'phone'         => $phone,
      'dataset'       => $dataset,
      'notes'         => $notes ?: '—',
      'app_name'      => APP_NAME,
      'app_url'       => APP_URL,
      'app_tagline'   => 'Tea Brokerage • Insights • Auction Services',
      'support_email' => NOTIFY_TO_EMAIL,
    ]);
  }

  if ($html) {
    $ack->isHTML(true);
    $ack->Body = $html;
  }

  // --- AltBody (text fallback) ---
  $ack->AltBody = implode("\r\n", [
    "Hi {$name},",
    "",
    "Thank you for your data request.",
    "Reference: {$ref}",
    "Date: {$dateStr}",
    "Dataset: {$dataset}",
    "",
    "Company: {$company}",
    "Email: {$email}",
    "Phone: {$phone}",
    "",
    "Notes:",
    ($notes ?: '—'),
    "",
    "We’ll get back to you shortly.",
    "— " . APP_NAME . " (" . APP_URL . ")",
  ]);

  $ack->send();

  $_SESSION['last_request_time'] = $now;
  echo json_encode(['success' => true], JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
  error_log('REQUEST API ERROR: ' . $e->getMessage());
  fail('Server error. Please try again.', 500, ['fatal' => $e->getMessage()]);
}