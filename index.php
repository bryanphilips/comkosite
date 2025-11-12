<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/config.php';
require __DIR__.'/lib/helpers.php';

$page = $_GET['p'] ?? 'home';
$allowed = [
  'home','about','services','other-services','auctions',
  'products','market-reports','events','contact','enquiry',
  'request','private-sale' // ← added
];
if (!in_array($page,$allowed)) $page = 'home';

$metaMap = [
  'home'           => ['Home | '.APP_NAME, 'Transparent auctions, tasting and market insights.', APP_URL.'/assets/img/hero-1.jpg','/'],
  'about'          => ['About | '.APP_NAME, 'Heritage, mission, and team.', APP_URL.'/assets/img/about.jpg','/about'],
  'services'       => ['Services | '.APP_NAME, 'Brokerage services across the auction lifecycle.', APP_URL.'/assets/img/services.jpg','/services'],
  'other-services' => ['Other Services | '.APP_NAME, 'Sampling, consultancy, and social links.', APP_URL.'/assets/img/services.jpg','/other-services'],
  'auctions'       => ['Auctions | '.APP_NAME, 'Auction process and schedule.', APP_URL.'/assets/img/process.jpg','/auctions'],
  'products'       => ['Products | '.APP_NAME, 'Tea varieties we broker.', APP_URL.'/assets/img/varieties.jpg','/products'],
  'market-reports' => ['Market Reports | '.APP_NAME, 'Teas offered vs sold, weekly metrics.', APP_URL.'/assets/img/hero-2.jpg','/market-reports'],
  'events'         => ['Events | '.APP_NAME, 'Calendar of upcoming auctions and sessions.', APP_URL.'/assets/img/hero-3.jpg','/events'],
  'contact'        => ['Contact | '.APP_NAME, 'Reach our team for enquiries.', APP_URL.'/assets/img/hero-1.jpg','/contact'],
  'enquiry'        => ['Enquiry & Feedback | '.APP_NAME, 'Share enquiries, feedback or complaints.', APP_URL.'/assets/img/hero-2.jpg','/enquiry'],
  'request'        => ['Request Data | '.APP_NAME, 'Ask for catalogues, reports, or history.', APP_URL.'/assets/img/hero-3.jpg','/request'],
  'private-sale'   => ['Request Private Sale | '.APP_NAME, 'Request a private sale for CTC or Orthodox teas.', APP_URL.'/assets/img/hero-2.jpg','/private-sale'], // ← added
];

[$t,$d,$img,$slug] = $metaMap[$page];
set_meta($t,$d,$img,$slug);

include __DIR__.'/partials/head.php';
include __DIR__.'/partials/header.php';
include __DIR__."/pages/{$page}.php";
include __DIR__.'/partials/footer.php';