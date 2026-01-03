<?php
require_once '/var/www/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('/var/www/templates');
$twig = new \Twig\Environment($loader, ['cache' => getenv("TWIG_CACHE"), 'autoescape' => false]);
$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db, $twig);

foreach (['narrow'] as $var) {
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

$hlb->pageTitle('Browse and Map', 'Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));
$hlb->addContent('navigation', ['browse_active' => 'active']);
$hlb->addContent('search', $hlb->getSearchParams($narrow));
if (!empty($narrow)) {
  $hlb->addContent('mappings', $hlb->getMappingsParams($narrow));
}

print $hlb->render();
