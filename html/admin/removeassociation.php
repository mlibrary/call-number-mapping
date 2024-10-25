<?php
require_once '/var/www/vendor/autoload.php';

$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db);

foreach (['child', 'parent'] as $var) {
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
if(isset($child) and strlen($child) and isset($parent) and strlen($parent)) {
  $hlb->removeAssociation($child, $parent);
}
header('location: tree.php');
