<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';

// ✅ Ensure PHPMailer is available (Composer)
$autoload = __DIR__ . '/../vendor/autoload.php';
if (is_file($autoload)) {
  require_once $autoload;
}

if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json; charset=utf-8');

function api_ok(array $data = []): void {
  echo json_encode(['success' => true] + $data, JSON_UNESCAPED_SLASHES);
  exit;
}
function api_fail(string $msg, int $code = 400, array $extra = []): void {
  http_response_code($code);
  $payload = ['success' => false, 'error' => $msg];
  if (defined('APP_ENV') && APP_ENV !== 'production' && !empty($extra)) $payload['detail'] = $extra;
  echo json_encode($payload, JSON_UNESCAPED_SLASHES);
  exit;
}

// ✅ Safer string length (mbstring may be missing)
function str_len_safe(string $s): int {
  if (function_exists('mb_strlen')) return (int)mb_strlen($s, 'UTF-8');
  return strlen($s);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') { http_response_code(204); exit; }

try {
  if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') api_fail('Invalid request method.', 405);

  $raw = file_get_contents('php://input');
  if ($raw === false || trim($raw) === '') api_fail('No payload.');

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
    if (empty($_SESSION['csrf']) || $csrf === '' || !hash_equals((string)$_SESSION['csrf'], $csrf)) {
      api_fail('Security token mismatch.', 403);
    }
  }

  // ----- Honeypot
  if (!empty($data['hp'] ?? '')) api_fail('Bot detected.', 400);

  // ----- Fields (normalize + limits)
  $name    = trim((string)($data['name'] ?? ''));
  $email   = trim((string)($data['email'] ?? ''));
  $tel     = trim((string)($data['tel'] ?? ''));
  $topic   = trim((string)($data['topic'] ?? 'General'));
  $message = trim((string)($data['message'] ?? ''));

  // basic max lengths to prevent abuse
  if (str_len_safe($name) > 120) api_fail('Name too long.');
  if (str_len_safe($email) > 190) api_fail('Email too long.');
  if (str_len_safe($tel) > 60) api_fail('Phone too long.');
  if (str_len_safe($topic) > 120) api_fail('Topic too long.');
  if (str_len_safe($message) > 4000) api_fail('Message too long (max 4000 characters).');

  if ($name === '' || $email === '' || $message === '') api_fail('Missing required fields.');
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) api_fail('Invalid email.');

  // Prevent header injection in subject/topic
  $safeTopic = preg_replace('/[\r\n]+/', ' ', $topic) ?: 'General';
  $safeTopic = trim($safeTopic);

  // ----- Throttle (session)
  $now  = time();
  $last = (int)($_SESSION['last_enquiry_time'] ?? 0);
  if ($now - $last < 20) api_fail('Please wait a moment before sending again.', 429);

  // ----- Throttle burst per IP (file bucket)
  $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

  $bucketDir = null;
  $tryDirs = [
    sys_get_temp_dir() . '/enquiry_buckets',
    __DIR__ . '/../cache/enquiry_buckets',
  ];
  foreach ($tryDirs as $d) {
    if (!is_dir($d)) @mkdir($d, 0775, true);
    if (is_dir($d) && is_writable($d)) { $bucketDir = $d; break; }
  }

  if ($bucketDir) {
    $bucket = $bucketDir . '/' . preg_replace('/[^0-9a-f:\.]/i', '_', $ip) . '.json';
    $burst  = ['t' => $now, 'c' => 0];

    if (is_file($bucket)) {
      $burst = json_decode((string)@file_get_contents($bucket), true) ?: $burst;
      if (($now - (int)($burst['t'] ?? 0)) > 60) $burst = ['t' => $now, 'c' => 0];
    }

    $burst['c'] = (int)($burst['c'] ?? 0) + 1;
    if ($burst['c'] > 5) api_fail('Too many attempts, please try again later.', 429);
    @file_put_contents($bucket, json_encode($burst), LOCK_EX);
  }

  // ----- reCAPTCHA
  $capResp = (string)($data['g-recaptcha-response'] ?? '');
  if (defined('RECAPTCHA_SECRET') && RECAPTCHA_SECRET) {
    if ($capResp === '') api_fail('Please complete the reCAPTCHA.', 400);
    $cap = recaptcha_verify($capResp, $ip);
    if (empty($cap['ok'])) api_fail($cap['err'] ?? 'CAPTCHA not valid.', 400);
  } elseif (defined('APP_ENV') && APP_ENV === 'production') {
    api_fail('CAPTCHA not configured.', 500);
  }

  // ----- Email subject/body
  $subject = preg_replace('/[\r\n]+/', ' ', "Enquiry: {$safeTopic} — {$name}");

  $bodyAdmin = implode("\r\n", [
    'New website enquiry',
    '',
    'Name:  ' . $name,
    'Email: ' . $email,
    'Phone: ' . ($tel ?: '—'),
    'Topic: ' . $safeTopic,
    '',
    'Message:',
    $message,
    '',
    'Sent at: ' . date('Y-m-d H:i:s'),
    'IP: ' . $ip,
  ]);

  // ----- Send ADMIN email
  $okAdmin = false;
  $mailErr = null;

  try {
    if (!class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
      throw new RuntimeException('PHPMailer not loaded. Ensure vendor/autoload.php exists on server.');
    }

    $m = new \PHPMailer\PHPMailer\PHPMailer(true);
    $m->CharSet = 'UTF-8';

    // Optional debug in non-prod
    if (defined('APP_ENV') && APP_ENV !== 'production') {
      $m->SMTPDebug = 0; // change to 2 if you want verbose output in logs
    }

    if (defined('SMTP_HOST') && SMTP_HOST && defined('SMTP_USER') && SMTP_USER && defined('SMTP_PASS') && SMTP_PASS) {
      $m->isSMTP();
      $m->Host = SMTP_HOST;
      $m->Port = (int)(SMTP_PORT ?? 587);
      $m->SMTPAuth = true;
      $m->Username = SMTP_USER;
      $m->Password = SMTP_PASS;

      $sec = strtolower((string)(SMTP_SECURE ?? 'tls'));
      $m->SMTPSecure = ($sec === 'ssl')
        ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
        : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
      $m->isMail();
    }

    $m->setFrom((string)SMTP_FROM_EMAIL, (string)SMTP_FROM_NAME);
    $m->addAddress((string)NOTIFY_TO_EMAIL, (string)NOTIFY_TO_NAME);
    $m->addReplyTo($email, $name);
    $m->Subject = $subject;
    $m->Body = $bodyAdmin;
    $m->AltBody = $bodyAdmin;

    $okAdmin = $m->send();
  } catch (Throwable $te) {
    $mailErr = $te->getMessage();
    if (defined('APP_ENV') && APP_ENV !== 'production') error_log('ADMIN MAIL ERROR: ' . $mailErr);

    // fallback helper
    if (function_exists('send_mail')) {
      $okAdmin = (bool)@send_mail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, $subject, $bodyAdmin);
    }
  }

  if (!$okAdmin) {
    api_fail('Message could not be sent. Please try later.', 500, $mailErr ? ['mail' => $mailErr] : []);
  }

  // ----- ACK to sender (optional)
  $tpl = __DIR__ . '/../templates/email/ack_enquiry.html';
  $htmlAck = '';
  if (is_file($tpl) && function_exists('email_template_render')) {
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

  $plainAck = implode("\r\n", [
    "Hi {$name},",
    "",
    "Thanks for contacting " . APP_NAME . ".",
    "Topic: {$safeTopic}",
    "",
    "Your message:",
    $message,
    "",
    "We’ll reply shortly.",
    "— " . APP_NAME . " (" . APP_URL . ")",
  ]);

  try {
    if (class_exists('\PHPMailer\PHPMailer\PHPMailer')) {
      $ack = new \PHPMailer\PHPMailer\PHPMailer(true);
      $ack->CharSet = 'UTF-8';

      if (defined('SMTP_HOST') && SMTP_HOST && defined('SMTP_USER') && SMTP_USER && defined('SMTP_PASS') && SMTP_PASS) {
        $ack->isSMTP();
        $ack->Host = SMTP_HOST;
        $ack->Port = (int)(SMTP_PORT ?? 587);
        $ack->SMTPAuth = true;
        $ack->Username = SMTP_USER;
        $ack->Password = SMTP_PASS;

        $sec = strtolower((string)(SMTP_SECURE ?? 'tls'));
        $ack->SMTPSecure = ($sec === 'ssl')
          ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
          : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
      } else {
        $ack->isMail();
      }

      $ack->setFrom((string)SMTP_FROM_EMAIL, (string)SMTP_FROM_NAME);
      $ack->addAddress($email, $name);
      $ack->addReplyTo((string)NOTIFY_TO_EMAIL, (string)SMTP_FROM_NAME);
      $ack->Subject = 'We received your enquiry';

      if ($htmlAck !== '') {
        $ack->isHTML(true);
        $ack->Body = $htmlAck;
        $ack->AltBody = $plainAck;
      } else {
        $ack->Body = $plainAck;
        $ack->AltBody = $plainAck;
      }

      $ack->send();
    }
  } catch (Throwable $te) {
    if (defined('APP_ENV') && APP_ENV !== 'production') error_log('ACK MAIL ERROR: '.$te->getMessage());
  }

  $_SESSION['last_enquiry_time'] = $now;

  // ✅ Don’t silently rotate CSRF unless your UI knows.
  // Instead return a fresh token and let frontend update it.
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
  api_ok(['csrf' => $_SESSION['csrf']]);

} catch (Throwable $e) {
  if (defined('APP_ENV') && APP_ENV !== 'production') error_log('ENQUIRY API FATAL: ' . $e->getMessage());
  api_fail('Server error. Please try again.', 500);
}