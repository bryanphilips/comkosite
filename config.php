<?php
require __DIR__.'/vendor/autoload.php';
use Dotenv\Dotenv;
if (file_exists(__DIR__.'/.env')) { Dotenv::createImmutable(__DIR__)->safeLoad(); }
function envv($k,$d=null){ $v=$_ENV[$k]??$_SERVER[$k]??getenv($k); return $v!==false&&$v!==null?$v:$d; }

define('APP_NAME', envv('APP_NAME','Combrok Limited'));
define('APP_URL',  envv('APP_URL','http://localhost:8080'));

define('SMTP_HOST', envv('SMTP_HOST','smtp.gmail.com'));
define('SMTP_PORT', (int)envv('SMTP_PORT',587));
define('SMTP_USER', envv('SMTP_USER'));
define('SMTP_PASS', envv('SMTP_PASS'));
define('SMTP_SECURE', envv('SMTP_SECURE','tls'));
define('SMTP_FROM_EMAIL', envv('SMTP_FROM_EMAIL', envv('SMTP_USER')));
define('SMTP_FROM_NAME',  envv('SMTP_FROM_NAME', APP_NAME));
define('NOTIFY_TO_EMAIL', envv('NOTIFY_TO_EMAIL', envv('SMTP_USER')));
define('NOTIFY_TO_NAME',  envv('NOTIFY_TO_NAME','Admin'));

define('SOCIAL_X',        envv('SOCIAL_X',''));
define('SOCIAL_LINKEDIN', envv('SOCIAL_LINKEDIN',''));
define('SOCIAL_FACEBOOK', envv('SOCIAL_FACEBOOK',''));
define('SOCIAL_INSTAGRAM',envv('SOCIAL_INSTAGRAM',''));

$GLOBALS['_PAGE_META'] = [
  'title' => APP_NAME.' | Tea Brokerage',
  'desc'  => 'Premium tea brokerage: auctions, tasting, market intelligence.',
  'image' => APP_URL.'/assets/img/hero-1.jpg',
  'slug'  => '/'
];