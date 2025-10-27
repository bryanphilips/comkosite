<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?=htmlspecialchars($GLOBALS['_PAGE_META']['title'])?></title>
  <meta name="description" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['desc'])?>" />
  <link rel="icon" href="/assets/img/logo.png">

  <link rel="canonical" href="<?=htmlspecialchars(APP_URL.$GLOBALS['_PAGE_META']['slug'])?>" />
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="<?=htmlspecialchars(APP_NAME)?>">
  <meta property="og:title" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['title'])?>">
  <meta property="og:description" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['desc'])?>">
  <meta property="og:url" content="<?=htmlspecialchars(APP_URL.$GLOBALS['_PAGE_META']['slug'])?>">
  <meta property="og:image" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['image'])?>">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['title'])?>">
  <meta name="twitter:description" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['desc'])?>">
  <meta name="twitter:image" content="<?=htmlspecialchars($GLOBALS['_PAGE_META']['image'])?>">

  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "<?=htmlspecialchars(APP_NAME)?>",
    "url": "<?=htmlspecialchars(APP_URL)?>",
    "logo": "<?=htmlspecialchars(APP_URL)?>/assets/img/logo.png",
    "sameAs": [
      <?php
        $s = array_filter([SOCIAL_X,SOCIAL_LINKEDIN,SOCIAL_FACEBOOK,SOCIAL_INSTAGRAM]);
        echo '"'.implode('","', $s).'"';
      ?>
    ]
  }
  </script>

  <link rel="stylesheet" href="/assets/css/style.css" />
</head>
<body></body>