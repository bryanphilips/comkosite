<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location:/?p=enquiry');
  exit;
}
if (!empty($_POST['hp'] ?? '')) {
  http_response_code(204);
  exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$etype = trim($_POST['etype'] ?? 'Enquiry');
$rating = trim($_POST['rating'] ?? '');
$subject = trim($_POST['subject'] ?? ("$etype from website"));
$msg = trim($_POST['message'] ?? '');
$consent = !empty($_POST['consent']);

if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $msg === '' || !$consent) {
  header('Location:/?p=enquiry&error=1');
  exit;
}

function sendMail($to, $toName, $sub, $body, $replyEmail = null, $replyName = null) {
  $m = new PHPMailer(true);
  try {
    $m->isSMTP();
    $m->SMTPAuth   = true;

    // TEMP: enable debug so we can see STARTTLS in your terminal
    $m->SMTPDebug  = 2;
    $m->Debugoutput = function($str, $level) { error_log("SMTP($level): $str"); };

    // Force STARTTLS on 587 (preferred)
    $m->Host        = 'smtp.gmail.com';
    $m->Port        = 587;
    $m->SMTPSecure  = PHPMailer::ENCRYPTION_STARTTLS; // <- makes PHPMailer issue STARTTLS first
    $m->SMTPAutoTLS = true;                           // <- auto-upgrade to TLS if allowed

    // If that still fails, comment the three lines above and uncomment this SMTPS block:
    // $m->Host        = 'smtp.gmail.com';
    // $m->Port        = 465;
    // $m->SMTPSecure  = PHPMailer::ENCRYPTION_SMTPS;
    // $m->SMTPAutoTLS = false;

    // Auth method and credentials
    $m->AuthType   = 'LOGIN';                         // explicit (Gmail supports LOGIN)
    $m->Username   = SMTP_USER;
    $m->Password   = SMTP_PASS;

    // From must equal Gmail account
    $m->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $m->addAddress($to, $toName);
    if ($replyEmail) {
      $m->addReplyTo($replyEmail, $replyName ?? $replyEmail);
    }

    $m->Subject = $sub;
    $m->Body    = $body;
    error_log('SMTP as '.SMTP_USER.' / '.(SMTP_PASS ? '***'.strlen(SMTP_PASS).'***' : 'EMPTY'));
    error_log('SMTP as '.SMTP_USER.' / len='.strlen(SMTP_PASS));
    $m->send();
  } catch (\Throwable $e) {
    error_log('MAIL ERROR: '.$e->getMessage());
    // Don't crash UX
  }
}
$body = "New {$etype}\n\nName: {$name}\nEmail: {$email}\nType: {$etype}\nRating: {$rating}\nSubject: {$subject}\n\nMessage:\n{$msg}\n";
sendMail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, "[{$etype}] {$subject}", $body, $email, $name);
sendMail($email, $name, "We received your {$etype}", "Hi {$name},\n\nWe have received your {$etype}. Thank you for your input.\n\nRegards,\n" . APP_NAME);
header('Location:/?p=enquiry&sent=1');