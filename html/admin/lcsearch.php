<?php
require_once '/var/www/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('/var/www/templates');
$twig = new \Twig\Environment($loader, ['cache' => getenv("TWIG_CACHE"), 'autoescape' => false]);
$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db, $twig);

foreach (['alpha','num','num_op','cut','cut_op'] as $var) {
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
$search_params = [
  'alpha' => $alpha,
  'num' => $num,
  'num_op' => $num_op,
  'cut' => $cut,
  'cut_op' => $cut_op,
  'options' => [ '<=', '>=', '=' ],
];

$hlb->pageTitle('LC Search', 'Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));
$hlb->addContent('navigation', [ 'search_active' => 'active' ]);
$hlb->addContent('search-overview', ['lc_active' => 'active' ]);
$hlb->addContent('lc-search-form', $search_params);

if (isset($alpha)) {
  $hlb->addContent('search-results', $hlb->getLcSearchResults($search_params));
}

print $hlb->render();
