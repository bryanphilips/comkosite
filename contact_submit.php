<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location:/?p=contact'); exit;
}

/* ---------- Honeypot ---------- */
if (!empty($_POST['hp'] ?? '')) { http_response_code(204); exit; }

/* ---------- CSRF ---------- */
try {
  csrf_require($_POST['csrf'] ?? '');
} catch (Throwable $e) {
  header('Location:/?p=contact&error=csrf'); exit;
}

/* ---------- Basic validation ---------- */
$rawName  = (string)($_POST['name']    ?? '');
$rawEmail = (string)($_POST['email']   ?? '');
$rawSubj  = (string)($_POST['subject'] ?? 'Website enquiry');
$rawMsg   = (string)($_POST['message'] ?? '');

$name    = trim($rawName);
$email   = trim($rawEmail);
$subject = trim(preg_replace("/[\r\n]+/", ' ', $rawSubj)); // harden against header injection
$message = trim($rawMsg);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
  header('Location:/?p=contact&error=1'); exit;
}

/* ---------- Optional field length clamps (avoid very long payloads) ---------- */
if (mb_strlen($name) > 200 || mb_strlen($subject) > 300 || mb_strlen($message) > 8000) {
  header('Location:/?p=contact&error=toolong'); exit;
}

/* ---------- reCAPTCHA v2 ---------- */
$captcha = (string)($_POST['g-recaptcha-response'] ?? '');
if (RECAPTCHA_SECRET) {
  $cap = recaptcha_verify($captcha, $_SERVER['REMOTE_ADDR'] ?? '');
  if (empty($cap['ok'])) { header('Location:/?p=contact&error=recaptcha'); exit; }
} else {
  // In production, enforce CAPTCHA config; in dev/staging allow missing keys
  if (APP_ENV === 'production') {
    header('Location:/?p=contact&error=recaptcha_config'); exit;
  }
}

/* ---------- Simple rate-limit (per IP: 60s; per session) ---------- */
$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$now = time();

/** per-session */
$sessKey = 'last_submit_contact';
$lastSess = (int)($_SESSION[$sessKey] ?? 0);
if ($now - $lastSess < 60) { header('Location:/?p=contact&error=slowdown'); exit; }
$_SESSION[$sessKey] = $now;

/** per-IP (temp file bucket) */
$bucketDir = sys_get_temp_dir() . '/contact_buckets';
if (!is_dir($bucketDir)) @mkdir($bucketDir, 0775);
$bucket = $bucketDir . '/' . preg_replace('/[^0-9a-f:\.]/i','_', $ip) . '.json';
$burst = ['t' => $now, 'c' => 0];
if (is_file($bucket)) {
  $burst = json_decode((string)@file_get_contents($bucket), true) ?: $burst;
  if (($now - (int)$burst['t']) > 60) $burst = ['t' => $now, 'c' => 0];
}
if (++$burst['c'] > 5) { header('Location:/?p=contact&error=rate'); exit; }
@file_put_contents($bucket, json_encode($burst), LOCK_EX);

/* ---------- Compose messages ---------- */
$site = APP_NAME;
$map  = 'https://share.google/doynRddeAvUMXGVXC';

$adminBody = implode("\n", [
  'New enquiry from the website',
  '',
  'Name:    ' . $name,
  'Email:   ' . $email,
  'Subject: ' . $subject,
  '',
  'Message:',
  $message,
  '',
  'IP: ' . $ip,
  'Sent: ' . date('Y-m-d H:i:s'),
]) . "\n";

$userBody = implode("\n", [
  "Hi {$name},",
  '',
  "Thank you for contacting {$site}. We’ve received your message and will reply shortly.",
  '',
  "Regards,",
  $site,
  APP_URL,
  '',
  'Visit us:',
  'Tea House, Nyerere Road, Mombasa CBD',
  $map,
]) . "\n";

/* ---------- Send mail (uses send_mail() helper) ---------- */
$okAdmin = send_mail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, '[Website] ' . $subject, $adminBody);
if ($okAdmin) {
  // Best-effort ack to user (don’t block success if it fails)
  @send_mail($email, $name, 'We received your message', $userBody);
} else {
  header('Location:/?p=contact&error=send'); exit;
}

/* ---------- Rotate CSRF on success (reduces replay risk) ---------- */
$_SESSION['csrf'] = bin2hex(random_bytes(32));

/* ---------- Done ---------- */
header('Location:/?p=contact&sent=1');
exit;