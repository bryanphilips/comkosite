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
  $gardenMark = trim((string)($data['gardenMark'] ?? ''));
  $name       = trim((string)($data['name'] ?? ''));
  $email      = trim((string)($data['email'] ?? ''));
  $phone      = trim((string)($data['phone'] ?? ''));
  $contactMe  = !empty($data['contactMe']);
  $rows       = (array)($data['rows'] ?? []);

  if ($gardenMark === '' || $name === '' || $email === '' || $phone === '') {
    fail('Missing required fields.');
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) fail('Invalid email address.');
  if (empty($rows)) fail('Please provide at least one line.');

  foreach ($rows as $i => $r) {
    $invoice     = trim((string)($r['invoice'] ?? ''));
    $grade       = trim((string)($r['grade'] ?? ''));
    $packageType = trim((string)($r['packageType'] ?? ''));
    $packages    = trim((string)($r['packages'] ?? ''));
    $gross       = trim((string)($r['gross'] ?? ''));
    $tare        = trim((string)($r['tare'] ?? ''));
    $net         = trim((string)($r['net'] ?? ''));
    if ($invoice===''||$grade===''||$packageType===''||$packages===''||$gross===''||$tare===''||$net==='') {
      fail('All table fields are required (row '.($i+1).').');
    }
    if (!is_numeric($packages) || (int)$packages <= 0) fail('Invalid packages (row '.($i+1).').');
    foreach (['gross'=>$gross,'tare'=>$tare,'net'=>$net] as $k => $v) {
      if (!is_numeric($v) || (float)$v < 0) fail("Invalid {$k} weight (row ".($i+1).").");
    }
  }

  // Throttle 60s
  $now = time(); $key = 'ps_offer_last'; $last = (int)($_SESSION[$key] ?? 0);
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
  $ref     = 'PSO-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
  $dateStr = date('Y-m-d H:i:s');

  // Admin text
  $linesTxt = [];
  foreach ($rows as $r) {
    $linesTxt[] = sprintf(
      "Inv:%s | Grade:%s | PkgType:%s | Pkgs:%s | Gross:%s | Tare:%s | Net:%s",
      $r['invoice'], $r['grade'], $r['packageType'], $r['packages'], $r['gross'], $r['tare'], $r['net']
    );
  }
  $adminBody = implode("\r\n", [
    'Private Sale — Offer',
    'Ref:    ' . $ref,
    'Date:   ' . $dateStr,
    '',
    'Garden: ' . $gardenMark,
    'Name:   ' . $name,
    'Email:  ' . $email,
    'Phone:  ' . $phone,
    'Contact Me: ' . ($contactMe ? 'Yes' : 'No'),
    '',
    'Lines:',
    implode("\r\n", $linesTxt),
  ]);

  // Send admin (UTF-8 handled in send_mail)
  $adminSubject = "Private Sale Offer from {$name} ({$gardenMark})";
  $okAdmin = send_mail(NOTIFY_TO_EMAIL, NOTIFY_TO_NAME, $adminSubject, $adminBody);
  if (!$okAdmin) fail('Message could not be sent. Please try later.', 500);

  // Build HTML table for ack
  $tableHtml = '<table style="width:100%;border-collapse:collapse;font:14px/1.4 system-ui">'
             . '<thead><tr>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Invoice</th>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Grade</th>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Package Type</th>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Packages</th>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Gross</th>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Tare</th>'
             . '<th style="border:1px solid #e5e7eb;padding:6px 8px;text-align:left">Net</th>'
             . '</tr></thead><tbody>';
  foreach ($rows as $r) {
    $tableHtml .= '<tr>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['invoice']).'</td>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['grade']).'</td>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['packageType']).'</td>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['packages']).'</td>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['gross']).'</td>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['tare']).'</td>'
      . '<td style="border:1px solid #e5e7eb;padding:6px 8px">'.htmlspecialchars((string)$r['net']).'</td>'
      . '</tr>';
  }
  $tableHtml .= '</tbody></table>';

  // Render HTML email with RAW LINES_HTML
  $tplPath = __DIR__ . '/../templates/email/ack_private_sale_offer.html';
  $vars = [
    'app_name'      => APP_NAME,
    'name'          => $name,
    'ref'           => $ref,
    'date'          => $dateStr,
    'garden_mark'   => $gardenMark,
    'lines_html'    => $tableHtml,               // RAW
    'contact_me'    => ($contactMe ? 'Yes' : 'No'),
    'portal_url'    => APP_URL,
    'support_email' => NOTIFY_TO_EMAIL,
  ];
  $html = is_file($tplPath) ? email_template_render($tplPath, $vars, ['lines_html']) : '';

  // Ack subject + plain text
  $ackSubj = "We received your Private Sale offer (Ref: {$ref})";
  $ackTxt  = implode("\r\n", [
    "Hi {$name},",
    "",
    "Thanks for your Private Sale offer. We’ve received your details.",
    "Reference: {$ref}",
    "Date: {$dateStr}",
    "Garden Mark: {$gardenMark}",
    "Contact me: " . ($contactMe ? 'Yes' : 'No'),
    "",
    "We’ll contact you shortly.",
    "— " . APP_NAME . " (" . APP_URL . ")",
  ]);

  // Send ack (UTF-8 handled by send_mail)
  if ($html) {
    send_mail($email, $name, $ackSubj, $html, $ackTxt);
  } else {
    send_mail($email, $name, $ackSubj, $ackTxt);
  }

  $_SESSION[$key] = $now;
  ok();
} catch (\Throwable $e) {
  if (APP_ENV !== 'production') error_log('PS OFFER ERROR: '.$e->getMessage());
  fail('Server error. Please try again.', 500);
}