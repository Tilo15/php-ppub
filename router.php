<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Exclude some paths from being rewritten
if (dirname($path) == '/' && pathinfo($path, PATHINFO_EXTENSION) == 'css') {
  return false;
}
else {
  $file = 'index.php';

  // Split into two parts, the ppub and the path within the ppub
  $parts = explode("/", $path, 3);
  $_GET['ppub'] = $parts[1];
  $_GET['asset'] = $parts[2];
}

$_SERVER['SCRIPT_NAME'] = '/' . $file;
require $file;
