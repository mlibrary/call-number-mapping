<?php
require_once '/var/www/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('/var/www/templates');
$twig = new \Twig\Environment($loader, ['cache' => getenv("TWIG_CACHE"), 'autoescape' => false]);
$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db, $twig);

$vars = ['notes', 'id', 'alphaStart', 'numberStart', 'cutterStart', 'alphaEnd', 'numberEnd', 'cutterEnd', 'type', 'narrow'];
foreach ($vars as $var) {
  if (isset($_POST[$var])) {
    $$var = $_POST[$var];
  }
  elseif (isset($_GET[$var])) {
    $$var = $_GET[$var];
  }
  else {
    $$var = NULL;
  }
}

if (isset($type) && 
  (($type == 'lc' && 
    isset($id) && 
    isset($alphaStart) && 
    isset($alphaEnd)) ||
  ($type == 'dewey' &&
    isset($id) &&
    isset($numberStart) && 
    isset($numberEnd)))) {
  $hlb->update($id, $alphaStart, $numberStart, $cutterStart, $alphaEnd, $numberEnd, $cutterEnd, $notes, $type);
}

header('location: index.php?narrow='. $narrow);
