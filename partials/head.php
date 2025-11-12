<!-- partials/head.php -->
<?php
// Use your helpers if they exist (from set_meta), otherwise fall back nicely.
$title = function_exists('meta_title') ? meta_title() : (APP_NAME ?? 'Combrok Limited');
$desc  = function_exists('meta_desc')  ? meta_desc()  : 'Transparent auctions, sampling, and market intelligence.';
$img   = function_exists('meta_image') ? meta_image() : (defined('APP_URL') ? APP_URL.'/assets/img/hero-1.jpg' : '/assets/img/hero-1.jpg');
$url   = function_exists('meta_url')   ? meta_url()   : (defined('APP_URL') ? APP_URL : '/');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta
    name="viewport"
    content="width=device-width, initial-scale=1, viewport-fit=cover"
  >
  <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Canonical -->
  <link rel="canonical" href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Open Graph -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:description" content="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:image" content="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>">
  <meta property="og:url" content="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Twitter Cards -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:description" content="<?= htmlspecialchars($desc, ENT_QUOTES, 'UTF-8') ?>">
  <meta name="twitter:image" content="<?= htmlspecialchars($img, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Theme color (matches mini ribbon / footer) -->
  <meta name="theme-color" content="#0A4E73">
  <meta name="color-scheme" content="light">

  <!-- Favicons (adjust paths as needed) -->
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" type="image/svg+xml" href="/assets/img/favicon.svg">
  <link rel="apple-touch-icon" href="/assets/img/apple-touch-icon.png">


  <!-- Preload logo (nice little UX touch) -->
  <link rel="preload" as="image" href="/assets/img/logo.png" imagesrcset="/assets/img/logo.png 1x">

  <!-- Bootstrap 5.3 CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous"
  >

  <!-- Site CSS (all premium styles live here) -->
   <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/style.css?v=15">

  <!-- JS (deferred): Bootstrap bundle + your main UI glue -->
  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
    defer
  ></script>
  <script src="/assets/js/main.js?v=15" defer></script>
</head>
<body>