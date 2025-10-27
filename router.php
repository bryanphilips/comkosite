cat > router.php <<'PHP'
<?php
$uri  = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$path = __DIR__ . $uri;

// Serve real files (CSS/JS/img/JSON/ICS/etc.)
if ($uri !== '/' && file_exists($path) && !is_dir($path)) {
  return false;
}

// Everything else goes to index.php
require __DIR__ . '/index.php';
