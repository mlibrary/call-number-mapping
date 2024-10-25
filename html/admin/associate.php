<?php
require_once '/var/www/vendor/autoload.php';

$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db);

foreach (['parent', 'child'] as $var) {
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

if ($hlb->associateTopics($child, $parent)) {
  $message = "New association added.";
}
else {
  $message = "Unable to add the new association. Check with design-discovery@umich.edu on the matter.";
}

header('location: modify.php?message=' . rawurlencode($message));
