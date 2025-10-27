<?php
require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config.php';
use PHPMailer\PHPMailer\PHPMailer;

if($_SERVER['REQUEST_METHOD']!=='POST'){ header('Location:/?p=contact'); exit; }
if(!empty($_POST['hp']??'')){ http_response_code(204); exit; }

$name=trim($_POST['name']??''); $email=trim($_POST['email']??'');
$subject=trim($_POST['subject']??'Website enquiry'); $msg=trim($_POST['message']??'');
if($name==='' || !filter_var($email,FILTER_VALIDATE_EMAIL) || $msg===''){ header('Location:/?p=contact&error=1'); exit; }

function sendMail($to,$toName,$sub,$body,$replyEmail=null,$replyName=null){
  $m=new PHPMailer(true); $m->isSMTP();
  $m->Host=SMTP_HOST; $m->SMTPAuth=true; $m->Username=SMTP_USER; $m->Password=SMTP_PASS;
  $m->SMTPSecure=SMTP_SECURE==='ssl'?PHPMailer::ENCRYPTION_SMTPS:PHPMailer::ENCRYPTION_STARTTLS;
  $m->Port=SMTP_PORT; $m->setFrom(SMTP_FROM_EMAIL,SMTP_FROM_NAME);
  $m->addAddress($to,$toName); if($replyEmail) $m->addReplyTo($replyEmail,$replyName??$replyEmail);
  $m->Subject=$sub; $m->Body=$body; $m->send();
}
$body="New enquiry\n\nName: $name\nEmail: $email\nSubject: $subject\n\n$msg\n";
sendMail(NOTIFY_TO_EMAIL,NOTIFY_TO_NAME,'[Website] '.$subject,$body,$email,$name);
sendMail($email,$name,'We received your message',"Hi $name,\n\nThanks for contacting ".APP_NAME.". We’ll reply shortly.\n\nRegards,\n".APP_NAME);
header('Location:/?p=contact&sent=1');