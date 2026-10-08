<?php
require_once '/var/www/vendor/autoload.php';

$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db);

$vars = array('id', 'name');
foreach ( $vars as $var ) {
  if (isset($_POST[$var])) {
    $$var = $_POST[$var];
  }
  else {
    $$var = NULL;
  }
}

if (!empty($id) && !empty($name)) {
  $hlb->renameTopic($id, $name);
  header('location: modify.php?message=' . rawurlencode('Topic #' . $id . ' renamed to "' . $name . '"'));
}
else {
  header('location: modify.php?message=' . rawurlencode('Missing required parameters'));
}


