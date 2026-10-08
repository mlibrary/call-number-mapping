<?php

require_once '/var/www/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('/var/www/templates');
$twig = new \Twig\Environment($loader, ['cache' => getenv("TWIG_CACHE"), 'autoescape' => false]);
$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db, $twig);

foreach (['id'] as $var) {
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


$hlb->pageTitle('Rename Topic', 'Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));
$hlb->addContent('navigation', ['modify_active' => true]);

if (empty($id)) {
  $hlb->addContent('message', ['message' => 'No topic ID specified.']);
}
else {
  $hlb->addContent('rename_topic', $hlb->getTopic($id));
}

print $hlb->render();
