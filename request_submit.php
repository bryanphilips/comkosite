<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';
use PHPMailer\PHPMailer\PHPMailer;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location:/?p=request');
  exit;
}
if (!empty($_POST['hp'] ?? '')) {
  http_response_code(204);
  exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$company = trim($_POST['company'] ?? '');
$type = trim($_POST['data_type'] ?? '');
$year = trim($_POST['year'] ?? '');
$week = trim($_POST['week'] ?? '');
$details = trim($_POST['details'] ?? '');
if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $company === '' || $type === '' || $year === '') {
  header('Location:/?p=request&error=1');
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
$body = "New data request\n\nName: $name\nEmail: $email\nCompany: $company\nType: $type\nYear: $year\nWeek: $week\n\nDetails:\n$details\n";
sendMail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, "[Request] $type (Y$year" . ($week ? "/W$week" : "") . ")", $body, $email, $name);
sendMail($email, $name, 'Your data request was received', "Hi $name,\n\nWe’ve received your request for $type (Year $year" . ($week ? ", Week $week" : "") . ").\nWe’ll reply with the data or next steps.\n\nRegards,\n" . APP_NAME);
header('Location:/?p=request&sent=1');