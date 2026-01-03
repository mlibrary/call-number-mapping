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
  'num' => $num,
  'num_op' => $num_op,
  'options' => [ '<=', '>=', '=' ],
];

$hlb->pageTitle('Dewey Search', 'Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));
$hlb->addContent('navigation', [ 'search_active' => 'active' ]);
$hlb->addContent('search-overview', ['dewey_active' => 'active' ]);
$hlb->addContent('dewey-search-form', $search_params);

if (isset($num)) {
  $hlb->addContent('search-results', $hlb->getDeweySearchResults($search_params));
}
print $hlb->render();
