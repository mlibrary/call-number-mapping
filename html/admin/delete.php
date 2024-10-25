<?php
require_once '/var/www/vendor/autoload.php';

$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db);

$vars = array('id', 'type','narrow');
foreach ( $vars as $var ) {
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

if (isset($id) && isset($type) && ($type == 'lc' || $type == 'dewey')) {
  $hlb->delete($type, $id);
}

header('location: index.php?narrow='. $narrow);
