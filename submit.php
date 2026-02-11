<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function redirect_to(string $path): void {
  header("Location: {$path}");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect_to('/'); // or /?p=home
}

/* ---------- Honeypot ---------- */
if (!empty($_POST['hp'] ?? '')) { http_response_code(204); exit; }

/* ---------- CSRF ---------- */
try {
  if (function_exists('csrf_require')) csrf_require($_POST['csrf'] ?? '');
  else {
    if (empty($_SESSION['csrf']) || empty($_POST['csrf']) || !hash_equals($_SESSION['csrf'], (string)$_POST['csrf'])) {
      redirect_to('/?p=home&error=csrf');
    }
  }
} catch (Throwable $e) {
  redirect_to('/?p=home&error=csrf');
}

/* ---------- Decide which form ---------- */
$formType = trim((string)($_POST['form_type'] ?? ''));
if (!in_array($formType, ['contact','enquiry'], true)) {
  redirect_to('/?p=home&error=form');
}

$page = ($formType === 'contact') ? 'contact' : 'enquiry';

/* ---------- Basic shared validation ---------- */
$name  = trim((string)($_POST['name'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$msg   = trim((string)($_POST['message'] ?? ''));

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $msg === '') {
  redirect_to("/?p={$page}&error=1");
}

/* ---------- Optional fields ---------- */
$tel   = trim((string)($_POST['tel'] ?? ''));
$topic = trim((string)($_POST['topic'] ?? 'General')); // enquiry
$subject = trim((string)($_POST['subject'] ?? 'Website enquiry')); // contact

// Harden header injection
$subject = trim(preg_replace("/[\r\n]+/", ' ', $subject));
$topic   = trim(preg_replace("/[\r\n]+/", ' ', $topic));

/* ---------- Length clamps ---------- */
if (mb_strlen($name) > 200 || mb_strlen($email) > 200 || mb_strlen($msg) > 8000) {
  redirect_to("/?p={$page}&error=toolong");
}
if ($formType === 'contact' && mb_strlen($subject) > 300) {
  redirect_to("/?p={$page}&error=toolong");
}
if ($formType === 'enquiry' && mb_strlen($topic) > 80) {
  redirect_to("/?p={$page}&error=toolong");
}

/* ---------- reCAPTCHA v2 ---------- */
$captcha = (string)($_POST['g-recaptcha-response'] ?? '');
if (RECAPTCHA_SECRET) {
  $cap = recaptcha_verify($captcha, $_SERVER['REMOTE_ADDR'] ?? '');
  if (empty($cap['ok'])) redirect_to("/?p={$page}&error=recaptcha");
} else {
  if (APP_ENV === 'production') redirect_to("/?p={$page}&error=recaptcha_config");
}

/* ---------- Rate limit: per-session 60s + per-IP bucket (max 5/min) ---------- */
$ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$now = time();

// per-session
$sessKey = 'last_submit_' . $formType;
$last = (int)($_SESSION[$sessKey] ?? 0);
if ($now - $last < 60) redirect_to("/?p={$page}&error=slowdown");
$_SESSION[$sessKey] = $now;

// per-IP bucket
$bucketDir = sys_get_temp_dir() . '/form_buckets';
if (!is_dir($bucketDir)) @mkdir($bucketDir, 0775);
$bucket = $bucketDir . '/' . preg_replace('/[^0-9a-f:\.]/i','_', $ip) . '_' . $formType . '.json';

$burst = ['t' => $now, 'c' => 0];
if (is_file($bucket)) {
  $burst = json_decode((string)@file_get_contents($bucket), true) ?: $burst;
  if (($now - (int)$burst['t']) > 60) $burst = ['t' => $now, 'c' => 0];
}
if (++$burst['c'] > 5) redirect_to("/?p={$page}&error=rate");
@file_put_contents($bucket, json_encode($burst), LOCK_EX);

/* ---------- Compose mail ---------- */
$site = APP_NAME;
$dateStr = date('Y-m-d H:i:s');

if ($formType === 'contact') {
  $adminSubject = '[Website] ' . ($subject ?: 'Contact message');
  $adminBody = implode("\n", [
    'New contact form message',
    '',
    "Name:    {$name}",
    "Email:   {$email}",
    "Subject: {$subject}",
    '',
    'Message:',
    $msg,
    '',
    "IP: {$ip}",
    "Sent: {$dateStr}",
  ]) . "\n";

  $ackSubject = 'We received your message';
  $ackBody = implode("\n", [
    "Hi {$name},",
    '',
    "Thank you for contacting {$site}. We’ve received your message and will reply shortly.",
    '',
    "Regards,",
    $site,
    APP_URL,
  ]) . "\n";

} else { // enquiry
  $adminSubject = "Enquiry: {$topic} — {$name}";
  $adminBody = implode("\n", [
    'New website enquiry',
    '',
    "Name:  {$name}",
    "Email: {$email}",
    "Phone: {$tel}",
    "Topic: {$topic}",
    '',
    "Message:",
    $msg,
    '',
    "IP: {$ip}",
    "Sent: {$dateStr}",
  ]) . "\n";

  // Optional HTML ack
  $tpl = __DIR__ . '/templates/email/ack_enquiry.html';
  $htmlAck = '';
  if (is_file($tpl)) {
    $htmlAck = email_template_render($tpl, [
      'subject'       => 'Enquiry Received',
      'app_name'      => APP_NAME,
      'app_tagline'   => 'Tea Brokerage • Insights • Auction Services',
      'portal_url'    => APP_URL,
      'support_email' => NOTIFY_TO_EMAIL,
      'name'          => $name,
      'topic'         => $topic,
      'message'       => $msg,
      'date'          => $dateStr,
    ]);
  }

  $ackSubject = 'We received your enquiry';
  $ackBody = implode("\n", [
    "Hi {$name},",
    '',
    "We’ve received your enquiry (topic: {$topic}). Our team will reply shortly.",
    '',
    "Regards,",
    APP_NAME,
    APP_URL,
  ]) . "\n";
}

/* ---------- Send mail via your helper send_mail() ---------- */
$okAdmin = send_mail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, $adminSubject, $adminBody);

if (!$okAdmin) {
  redirect_to("/?p={$page}&error=send");
}

// Ack to user (best-effort)
if (!empty($htmlAck ?? '')) {
  // If your send_mail supports HTML, you can extend it later.
  // For now: send plain text; OR keep your PHPMailer HTML ack logic separately.
  @send_mail($email, $name, $ackSubject, $ackBody);
} else {
  @send_mail($email, $name, $ackSubject, $ackBody);
}

/* ---------- Rotate CSRF on success ---------- */
$_SESSION['csrf'] = bin2hex(random_bytes(32));

redirect_to("/?p={$page}&sent=1");