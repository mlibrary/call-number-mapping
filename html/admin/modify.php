<?php
require_once '/var/www/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('/var/www/templates');
$twig = new \Twig\Environment($loader, ['cache' => getenv("TWIG_CACHE"), 'autoescape' => false]);
$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db, $twig);

foreach (['id', 'search', 'message'] as $var) {
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

$hlb->pageTitle('Modify Topics', 'Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));
$hlb->addContent('navigation', ['modify_active' => true]);
$hlb->addContent('message', ['message' => $message]);
$hlb->addContent('create', $hlb->getCreateParams());
$hlb->addContent('associate', $hlb->getAssociateParams());
$hlb->addContent('category-search', ['search' => $search]);
if (isset($search) && !empty($search)) {
  $hlb->addContent('category-search-results', $hlb->getCategorySearchResults($search));
}
print $hlb->render();
