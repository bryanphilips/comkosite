<?php
/**
 * config.php — App bootstrap & helpers
 *
 * - Composer autoload (PHPMailer, Dotenv, etc)
 * - .env loading (safe)
 * - ENV helpers (envv)
 * - Core constants (APP, SMTP, SOCIAL, NOTIFY)
 * - reCAPTCHA v2 checkbox (SITE_KEY/SECRET)
 * - Session + CSRF helpers
 * - JSON helpers for API endpoints
 * - reCAPTCHA verify helper
 * - send_mail() — PHPMailer if available, fallback to mail()
 *
 * Place this file at project root and `require_once` it at the top of pages
 * that need sessions/CSRF/meta constants or email sending.
 */

/* ---------- Composer autoload (TOP OF FILE) ---------- */
$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
  require $autoload;
} else {
  http_response_code(500);
  echo "Missing Composer dependencies. Run <code>composer install</code> (or enable it via .cpanel.yml).";
  exit;
}

/* ---------- .env (TOP OF FILE) ---------- */
use Dotenv\Dotenv;
if (file_exists(__DIR__.'/.env')) {
  Dotenv::createImmutable(__DIR__)->safeLoad();
}

/* ---------- Small ENV helper ---------- */
function envv(string $key, $default = null) {
  $v = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
  return ($v !== false && $v !== null && $v !== '') ? $v : $default;
}

/* ---------- App constants ---------- */
define('APP_NAME', envv('APP_NAME','Combrok Limited'));
define('APP_URL',  rtrim(envv('APP_URL','http://localhost:8080'), '/'));
define('APP_ENV',  envv('APP_ENV','production'));          // production|staging|development
define('APP_DEBUG', (bool)envv('APP_DEBUG', false));

/* ---------- SMTP / Email routing ---------- */
define('SMTP_HOST', envv('SMTP_HOST','smtp.gmail.com'));
define('SMTP_PORT', (int)envv('SMTP_PORT',587));
define('SMTP_USER', envv('SMTP_USER'));
define('SMTP_PASS', envv('SMTP_PASS'));
define('SMTP_SECURE', envv('SMTP_SECURE','tls'));           // tls|ssl|STARTTLS (PHPMailer will normalize)
define('SMTP_FROM_EMAIL', envv('SMTP_FROM_EMAIL', envv('SMTP_USER', 'no-reply@combrok.co.ke')));
define('SMTP_FROM_NAME',  envv('SMTP_FROM_NAME',  APP_NAME));
define('NOTIFY_TO_EMAIL', envv('NOTIFY_TO_EMAIL', envv('SMTP_USER', 'info@combrok.co.ke')));
define('NOTIFY_TO_NAME',  envv('NOTIFY_TO_NAME','Admin'));

/* ---------- Social links ---------- */
define('SOCIAL_X',         envv('SOCIAL_X',''));
define('SOCIAL_LINKEDIN',  envv('SOCIAL_LINKEDIN',''));
define('SOCIAL_FACEBOOK',  envv('SOCIAL_FACEBOOK',''));
define('SOCIAL_INSTAGRAM', envv('SOCIAL_INSTAGRAM',''));

/* ---------- reCAPTCHA v2 (checkbox) ---------- */
define('RECAPTCHA_SITE_KEY', envv('RECAPTCHA_SITE_KEY',''));  // used in markup
define('RECAPTCHA_SECRET',   envv('RECAPTCHA_SECRET',''));    // used server-side

/* ---------- Default page meta ---------- */
$GLOBALS['_PAGE_META'] = [
  'title' => APP_NAME.' | Tea Brokerage',
  'desc'  => 'Premium tea brokerage: auctions, tasting, market intelligence.',
  'image' => APP_URL.'/assets/img/hero-1.jpg',
  'slug'  => '/'
];

/* ---------- Session & CSRF ---------- */
if (session_status() === PHP_SESSION_NONE) {
  // Secure session flags (best-effort; some hosts ignore)
  ini_set('session.cookie_httponly', '1');
  ini_set('session.use_strict_mode', '1');
  if (!empty($_SERVER['HTTPS']) || envv('FORCE_HTTPS', '0') === '1') {
    ini_set('session.cookie_secure', '1');
  }
  session_start();
}
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
/** Get current CSRF token (for hidden input) */
function csrf_token(): string { return $_SESSION['csrf'] ?? ''; }

/** Validate a provided CSRF token (throws on fail) */
function csrf_require(string $token): void {
  if (empty($_SESSION['csrf']) || empty($token) || !hash_equals($_SESSION['csrf'], $token)) {
    http_response_code(403);
    throw new RuntimeException('Security token mismatch.');
  }
}

/* ---------- JSON response helpers (for /api/*) ---------- */
function json_ok(array $data = []): void {
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['success' => true] + $data, JSON_UNESCAPED_SLASHES);
  exit;
}

/* ---------- reCAPTCHA verify helper ---------- */
function recaptcha_verify(string $response, string $remoteIp = ''): array {
  if (!RECAPTCHA_SECRET) {
    // If you intentionally run without CAPTCHA in dev
    if (APP_ENV !== 'production') return ['ok' => true];
    return ['ok' => false, 'err' => 'CAPTCHA server key not configured.'];
  }
  if ($response === '') return ['ok' => false, 'err' => 'CAPTCHA response missing.'];

  $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => [
      'secret'   => RECAPTCHA_SECRET,
      'response' => $response,
      'remoteip' => $remoteIp,
    ],
    CURLOPT_TIMEOUT        => 10,
  ]);
  $raw = curl_exec($ch);
  if ($raw === false) {
    $e = curl_error($ch);
    curl_close($ch);
    return ['ok' => false, 'err' => 'CAPTCHA verify error: '.$e];
  }
  $res = json_decode($raw, true);
  curl_close($ch);

  if (!empty($res['success'])) return ['ok' => true];
  return ['ok' => false, 'err' => 'CAPTCHA not valid'];
}

/* ---------- Mail helper (PHPMailer if available, else mail()) ---------- */
function email_template_render(string $path, array $vars, array $rawKeys = []): string {
  $html = @file_get_contents($path);
  if ($html === false) return '';

  $repl = [];
  foreach ($vars as $k => $v) {
    $placeholder = '{{'.strtoupper($k).'}}';

    // treat *_html keys as RAW automatically, or if explicitly listed
    $isRaw = in_array($k, $rawKeys, true) || str_ends_with((string)$k, '_html');

    if (is_string($v)) {
      $repl[$placeholder] = $isRaw
        ? $v
        : nl2br(htmlspecialchars($v, ENT_QUOTES, 'UTF-8'));
    } else {
      $str = (string)$v;
      $repl[$placeholder] = $isRaw
        ? $str
        : nl2br(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
    }
  }

  return strtr($html, $repl);
}
function send_mail(string $toEmail, string $toName, string $subject, string $body, ?string $altBody = null): bool {
  if (class_exists(\PHPMailer\PHPMailer\PHPMailer::class)) {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    try {
      if (SMTP_HOST && SMTP_USER && SMTP_PASS) {
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->Port       = SMTP_PORT;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $sec = strtolower((string)SMTP_SECURE);
        $mail->SMTPSecure = ($sec === 'ssl')
          ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS
          : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
      } else {
        $mail->isMail();
      }

      // ✅ enforce UTF-8 + base64
      $mail->CharSet  = 'UTF-8';
      $mail->Encoding = 'base64';

      $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
      $mail->addAddress($toEmail, $toName ?: $toEmail);
      $mail->addReplyTo(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

      $mail->Subject = $subject;

      // If body looks like HTML, send as HTML; else plain
      if (strip_tags($body) !== $body || str_contains($body, '<')) {
        $mail->isHTML(true);
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: strip_tags($body);
      } else {
        $mail->isHTML(false);
        $mail->Body    = $body;
        $mail->AltBody = $altBody ?: $body;
      }

      return $mail->send();
    } catch (\Throwable $e) {
      error_log('send_mail PHPMailer error: '.$e->getMessage());
      return false;
    }
  }

  // Fallback to native mail() (still set UTF-8 headers)
  $headers = [
    'From: '.SMTP_FROM_NAME.' <'.SMTP_FROM_EMAIL.'>',
    'Reply-To: '.SMTP_FROM_EMAIL,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',
  ];
  $subject = '=?UTF-8?B?'.base64_encode($subject).'?=';
  $body    = base64_encode($body);

  return @mail($toEmail, $subject, $body, implode("\r\n", $headers));
}
