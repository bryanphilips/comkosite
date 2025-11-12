<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php'; // autoload, env, helpers (incl. recaptcha_verify, send_mail, email_template_render)

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

// ---------- JSON helpers ----------
function api_ok(array $data = []): void {
  echo json_encode(['success' => true] + $data, JSON_UNESCAPED_SLASHES);
  exit;
}
function api_fail(string $msg, int $code = 400, array $extra = []): void {
  http_response_code($code);
  $payload = ['success' => false, 'error' => $msg];
  if (APP_ENV !== 'production' && !empty($extra)) $payload['detail'] = $extra;
  echo json_encode($payload, JSON_UNESCAPED_SLASHES);
  exit;
}

// Preflight (if ever CORSed later)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

try {
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') api_fail('Invalid request method.', 405);

  // ----- Parse JSON
  $raw = file_get_contents('php://input');
  if ($raw === false || $raw === '') api_fail('No payload.');
  try {
    $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
  } catch (Throwable $jerr) {
    api_fail('Invalid JSON.', 400, ['json' => $jerr->getMessage()]);
  }

  // ----- CSRF
  $csrf = (string)($data['csrf'] ?? '');
  if (function_exists('csrf_require')) {
    try { csrf_require($csrf); } catch (Throwable $t) { api_fail('Security token mismatch.', 403); }
  } else {
    if (empty($_SESSION['csrf']) || $csrf === '' || !hash_equals($_SESSION['csrf'], $csrf)) {
      api_fail('Security token mismatch.', 403);
    }
  }

  // ----- Honeypot
  if (!empty($data['hp'] ?? '')) api_fail('Bot detected.', 400);

  // ----- Fields
  $name    = trim((string)($data['name'] ?? ''));
  $email   = trim((string)($data['email'] ?? ''));
  $tel     = trim((string)($data['tel'] ?? ''));
  $topic   = trim((string)($data['topic'] ?? 'General'));
  $message = trim((string)($data['message'] ?? ''));

  if ($name === '' || $email === '' || $message === '') api_fail('Missing required fields.');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) api_fail('Invalid email.');

  // ----- Throttle (session + simple burst per IP)
  $now  = time();
  $last = (int)($_SESSION['last_enquiry_time'] ?? 0);
  if ($now - $last < 60) api_fail('Please wait a moment before sending again.', 429);

  $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
  $bucketDir = sys_get_temp_dir() . '/enquiry_buckets';
  if (!is_dir($bucketDir)) @mkdir($bucketDir, 0775);
  $bucket = $bucketDir . '/' . preg_replace('/[^0-9a-f:\.]/i', '_', $ip) . '.json';
  $burst  = ['t' => $now, 'c' => 0];
  if (is_file($bucket)) {
    $burst = json_decode((string)@file_get_contents($bucket), true) ?: $burst;
    if (($now - (int)$burst['t']) > 60) $burst = ['t' => $now, 'c' => 0];
  }
  if (++$burst['c'] > 5) api_fail('Too many attempts, please try again later.', 429);
  @file_put_contents($bucket, json_encode($burst), LOCK_EX);

  // ----- reCAPTCHA (optional; enforced in production if keys present)
  $capResp = (string)($data['g-recaptcha-response'] ?? '');
  if (RECAPTCHA_SECRET) {
    $cap = recaptcha_verify($capResp, $ip);
    if (empty($cap['ok'])) api_fail($cap['err'] ?? 'CAPTCHA not valid.', 400);
  } else if (APP_ENV === 'production') {
    api_fail('CAPTCHA not configured.', 500);
  }

  // ----- Build messages
  $safeTopic = preg_replace('/[\r\n]+/', ' ', $topic);
  $subject   = 'Enquiry: ' . $safeTopic . ' — ' . $name;

  $bodyAdmin = implode("\r\n", [
    'New website enquiry',
    '',
    'Name:  ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($tel ?: '—'),
    'Topic: ' . $safeTopic,
    '',
    'Message:',
    ($message ?: '—'),
    '',
    'Sent at: ' . date('Y-m-d H:i:s'),
  ]);

  // ----- Send to admin
  $ok = send_mail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, $subject, $bodyAdmin);
  if (!$ok) api_fail('Message could not be sent. Please try later.', 500);

  // ----- Acknowledge to sender (HTML template if present)
  $tpl = __DIR__ . '/../templates/email/ack_enquiry.html';
  $htmlAck = '';
  if (is_file($tpl)) {
    $htmlAck = email_template_render($tpl, [
      'subject'       => 'Enquiry Received',
      'app_name'      => APP_NAME,
      'app_tagline'   => 'Tea Brokerage • Insights • Auction Services',
      'portal_url'    => APP_URL,
      'support_email' => NOTIFY_TO_EMAIL,
      'name'          => $name,
      'topic'         => $safeTopic,
      'message'       => $message,
      'date'          => date('Y-m-d H:i:s'),
    ]);
  }

  // Plain-text fallback (always set)
  $plainAck = implode("\r\n", [
    "Hi {$name},",
    "",
    "Thanks for contacting " . APP_NAME . ".",
    "Topic: {$safeTopic}",
    "",
    "Your message:",
    ($message ?: '—'),
    "",
    "We’ll reply shortly.",
    "— " . APP_NAME . ' (' . APP_URL . ')',
  ]);

  // Use your send_mail helper (it handles PHPMailer/mail fallback)
  if ($htmlAck) {
    // send_mail is text-only; for HTML ack use PHPMailer here directly:
    try {
      $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
      if (SMTP_HOST && SMTP_USER && SMTP_PASS) {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST; $mail->Port = SMTP_PORT;
        $mail->SMTPAuth = true; $mail->Username = SMTP_USER; $mail->Password = SMTP_PASS;
        $sec = strtolower(SMTP_SECURE);
        $mail->SMTPSecure = ($sec === 'ssl')
          ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
          : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
      } else {
        $mail->isMail();
      }
      $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
      $mail->addAddress($email, $name);
      $mail->addReplyTo(NOTIFY_TO_EMAIL, SMTP_FROM_NAME);
      $mail->Subject = 'We received your enquiry';
      $mail->isHTML(true);
      $mail->Body    = $htmlAck;
      $mail->AltBody = $plainAck;
      $mail->send();
    } catch (\Throwable $te) {
      if (APP_ENV !== 'production') error_log('ACK HTML mail error: '.$te->getMessage());
      @send_mail($email, $name, 'We received your enquiry', $plainAck);
    }
  } else {
    @send_mail($email, $name, 'We received your enquiry', $plainAck);
  }

  // Rotate throttle + (optional) rotate CSRF to shrink replay window
  $_SESSION['last_enquiry_time'] = $now;
  $_SESSION['csrf'] = bin2hex(random_bytes(32));

  api_ok();
} catch (Throwable $e) {
  if (APP_ENV !== 'production') error_log('ENQUIRY API FATAL: ' . $e->getMessage());
  api_fail('Server error. Please try again.', 500);
}