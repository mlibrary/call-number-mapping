<?php
require_once '/var/www/vendor/autoload.php';

$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db);

$vars = array('notes','id', 'alphaStart', 'numberStart', 'cutterStart', 'alphaEnd', 'numberEnd', 'cutterEnd','type');
foreach( $vars as $var ) {
    if (isset($_POST[$var]))
      $$var = $_POST[$var];
    elseif (isset($_GET[$var]))
      $$var = $_GET[$var];
    else
      $$var = NULL;
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
  $hlb->add($id, $alphaStart, $numberStart, $cutterStart, $alphaEnd, $numberEnd, $cutterEnd, $notes, $type);
}

header('location: index.php?narrow='. $id);
