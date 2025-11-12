<?php
declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
use PHPMailer\PHPMailer\PHPMailer;

if (session_status() === PHP_SESSION_NONE) session_start();

// --- Ensure POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location:/?p=enquiry');
  exit;
}

// --- CSRF check ---
if (empty($_POST['csrf']) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
  http_response_code(403);
  exit('Security token mismatch.');
}

// --- Honeypot ---
if (!empty($_POST['hp'] ?? '')) { http_response_code(204); exit; }

// --- Fields ---
$name  = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$tel   = trim($_POST['tel'] ?? '');
$topic = trim($_POST['topic'] ?? 'General');
$msg   = trim($_POST['message'] ?? '');

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $msg === '') {
  header('Location:/?p=enquiry&error=1');
  exit;
}

// --- Throttle: simple per-session 60s ---
$now  = time();
$last = (int)($_SESSION['last_enquiry_time'] ?? 0);
if ($now - $last < 60) {
  header('Location:/?p=enquiry&error=slow');
  exit;
}
$_SESSION['last_enquiry_time'] = $now;

// --- Build mail bodies ---
$subject = "Enquiry: {$topic} — {$name}";
$bodyTxt = "New website enquiry\n\n"
         . "Name:  {$name}\n"
         . "Email: {$email}\n"
         . "Phone: {$tel}\n"
         . "Topic: {$topic}\n\n"
         . "Message:\n{$msg}\n\n"
         . "Sent at: " . date('Y-m-d H:i:s') . "\n";

function sendMailSafe(string $to, string $toName, string $sub, string $body, ?string $replyEmail = null, ?string $replyName = null, ?string $html = null): void {
  $m = new PHPMailer(true);
  try {
    $m->isSMTP();
    $m->SMTPAuth   = true;
    $m->Host       = SMTP_HOST;
    $m->Port       = SMTP_PORT;
    $m->Username   = SMTP_USER;
    $m->Password   = SMTP_PASS;
    $sec = strtolower(SMTP_SECURE);
    $m->SMTPSecure = ($sec === 'ssl')
      ? PHPMailer::ENCRYPTION_SMTPS
      : PHPMailer::ENCRYPTION_STARTTLS;

    $m->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $m->addAddress($to, $toName);
    if ($replyEmail) $m->addReplyTo($replyEmail, $replyName ?? $replyEmail);

    $m->Subject = $sub;
    if ($html) {
      $m->isHTML(true);
      $m->Body    = $html;
      $m->AltBody = $body;
    } else {
      $m->Body    = $body;
      $m->AltBody = $body;
    }
    $m->send();
  } catch (\Throwable $e) {
    if (APP_DEBUG) error_log('MAIL ERROR: '.$e->getMessage());
  }
}

// --- Send to admin ---
sendMailSafe(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, $subject, $bodyTxt, $email, $name);

// --- Optional HTML acknowledgment ---
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
    'date'          => date('Y-m-d H:i:s'),
  ]);
}

// --- Send acknowledgment (HTML if available) ---
$ackSubj = 'We received your enquiry';
$ackBody = "Hi {$name},\n\n"
         . "We’ve received your enquiry (topic: {$topic}). Our team will reply shortly.\n\n"
         . "Regards,\n" . APP_NAME . "\n" . APP_URL;

sendMailSafe($email, $name, $ackSubj, $ackBody, NOTIFY_TO_EMAIL, APP_NAME, $htmlAck ?: null);

// --- Success redirect ---
$_SESSION['csrf'] = bin2hex(random_bytes(32)); // rotate token
header('Location:/?p=enquiry&sent=1');
exit;