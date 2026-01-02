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

$hlb->pageTitle('Reports - Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));

$hlb->addContent('navigation', ['reports_active' => TRUE]);
$hlb->addContent('report-form', $hlb->getReportFormParams());
print $hlb->render();
