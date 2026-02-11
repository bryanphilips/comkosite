<?php
// index.php

// In production you can turn these OFF (or control by APP_DEBUG)
if (defined('APP_DEBUG') && APP_DEBUG) {
  ini_set('display_errors', '1');
  ini_set('display_startup_errors', '1');
  error_reporting(E_ALL);
} else {
  ini_set('display_errors', '0');
  ini_set('display_startup_errors', '0');
  error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

require __DIR__.'/config.php';
require __DIR__.'/lib/helpers.php';

$page = $_GET['p'] ?? 'home';

/**
 * Only allow these pages
 * - Removed: contact, enquiry (old)
 * - Kept: enquiry (now points to pages/contact_enquiry.php)
 */
$allowed = [
  'home','about','services','other-services','auctions',
  'products','market-reports','events','sustainability',
  'enquiry','request','private-sale'
];

if (!in_array($page, $allowed, true)) $page = 'home';

/**
 * SEO/meta map
 * - Removed: contact
 * - enquiry now represents the combined Contact + Enquiry page
 */
$metaMap = [
  'home'           => ['Home | '.APP_NAME, 'Transparent auctions, tasting and market insights.', APP_URL.'/assets/img/hero-1.jpg','/'],
  'about'          => ['About | '.APP_NAME, 'Heritage, mission, and team.', APP_URL.'/assets/img/about.jpg','/about'],
  'services'       => ['Services | '.APP_NAME, 'Brokerage services across the auction lifecycle.', APP_URL.'/assets/img/services.jpg','/services'],
  'other-services' => ['Other Services | '.APP_NAME, 'Sampling, consultancy, and social links.', APP_URL.'/assets/img/services.jpg','/other-services'],
  'auctions'       => ['Auctions | '.APP_NAME, 'Auction process and schedule.', APP_URL.'/assets/img/process.jpg','/auctions'],
  'products'       => ['Products | '.APP_NAME, 'Tea varieties we broker.', APP_URL.'/assets/img/varieties.jpg','/products'],
  'market-reports' => ['Market Reports | '.APP_NAME, 'Teas offered vs sold, weekly metrics.', APP_URL.'/assets/img/hero-2.jpg','/market-reports'],
  'events'         => ['Events | '.APP_NAME, 'Calendar of upcoming auctions and sessions.', APP_URL.'/assets/img/hero-3.jpg','/events'],
  'sustainability' => ['Sustainability | '.APP_NAME, 'Tea sustainability, farmer empowerment, climate resilience and impact.', APP_URL.'/assets/img/hero-2.jpg','/sustainability'],

  // Combined contact + enquiry page
  'enquiry'        => ['Enquiry | '.APP_NAME, 'Send an enquiry, request support, or reach our team.', APP_URL.'/assets/img/hero-2.jpg','/enquiry'],

  'private-sale'   => ['Private Sale | '.APP_NAME, 'Request a private sale or offer teas for private sale.', APP_URL.'/assets/img/hero-2.jpg','/private-sale'],
];

/**
 * Fallback meta (prevents undefined array offset if a metaMap key is missing)
 */
$defaultMeta = ['Home | '.APP_NAME, 'Tea brokerage services, auctions and market intelligence.', APP_URL.'/assets/img/hero-1.jpg','/'];

$meta = $metaMap[$page] ?? $defaultMeta;

[$t, $d, $img, $slug] = $meta;
set_meta($t, $d, $img, $slug);

$headFile   = __DIR__.'/partials/head.php';
$headerFile = __DIR__.'/partials/header.php';
$footerFile = __DIR__.'/partials/footer.php';

/**
 * Route: enquiry -> pages/contact_enquiry.php
 * All other pages -> pages/{page}.php
 */
$pageFile = ($page === 'enquiry')
  ? __DIR__."/pages/enquiry.php"
  : __DIR__."/pages/{$page}.php";

if (is_file($headFile))   include $headFile;
if (is_file($headerFile)) include $headerFile;

// Prevent fatal if the page file is missing
if (is_file($pageFile)) {
  include $pageFile;
} else {
  http_response_code(404);
  include __DIR__."/pages/home.php"; // or a custom 404 page
}

if (is_file($footerFile)) include $footerFile;