<?php
// path: /public/index.php
// typage strict
declare(strict_types=1);

use model\MyPDO;

// démarrage de la session
session_start();

// inclusion du fichier de configuration si config-prod.php existe
require_once (file_exists('../config-prod.php') ? '../config-prod.php' : '../config-dev.php');

// Autoload fonctionnel avec les namespaces personnels,
// ne fonctionne qu'en PHP Orienté Objet (fait main, on pourrait
// utiliser Composer pour y ajouter nos dépendances)
// et avec une arborescence de fichiers respectant les namespaces
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require RACINE_PATH.'/'.$class.'.php';
});

// page demandée, l'accueil par défaut
$pg = $_GET['pg'] ?? 'accueil';

// Connexion à la base de données en singleton, on ne peut pas faire de new MyPDO() car le constructeur est protégé
try {
    $db = MyPDO::getInstance();
} catch (Exception $e) {
    // base indisponible : le visiteur voit une page propre, sans message technique
    http_response_code(503);
    require RACINE_PATH.'/view/erreur.php';
    exit;
}

// les pages qui existent ; toute autre adresse affiche la page 404
$pages = ['accueil', 'recettes', 'recette', 'a-propos', 'contact', 'connection'];

if (in_array($pg, $pages)) {
    require RACINE_PATH.'/view/'.$pg.'.php';
} else {
    http_response_code(404);
    require RACINE_PATH.'/view/404.php';
}
