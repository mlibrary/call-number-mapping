<?php
require_once '/var/www/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('/var/www/templates');
$twig = new \Twig\Environment($loader, ['cache' => getenv("TWIG_CACHE"), 'autoescape' => false]);
$db = new \Umich\CallNumberMapping\Database;
$hlb = new \Umich\CallNumberMapping\Hlb($db, $twig);

$hlb->pageTitle('Tree - Call Number Mapping');
$hlb->setAuthenticated(!empty($_SERVER['REMOTE_USER']));
$hlb->addContent('navigation', ['tree_active' => TRUE]);
$hlb->addContent('tree', $hlb->getTreeParams());
print $hlb->render();
