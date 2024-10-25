<?php
require_once '/var/www/vendor/autoload.php';

$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db);

foreach (['id', 'lc', 'narrow', 'dewey', 'target'] as $var) {
  if (isset($_POST[$var])) {
    $$var = $_POST[$var];
  }
  elseif (isset($_GET[$var])) {
    $$var = $_GET[$var];
  }
  else {
    $$var = '';
  }
}
if (empty($lc)) {
  $lc = [];
}
if (empty($dewey)) {
  $dewey = [];
}

if (!empty($target)) {
  foreach ($lc as $mapping) {
    $hlb->copy('lc', $target, $mapping);
  }
  foreach ($dewey as $mapping) {
    $hlb->copy('dewey', $target, $mapping);
  }
}
header('location: index.php?narrow='. rawurlencode($target));
