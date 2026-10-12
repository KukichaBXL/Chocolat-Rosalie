<?php
// path: /public/index.php
// typage strict
declare(strict_types=1);

use model\MyPDO;

// aucune erreur technique n'est montrée au visiteur : elles vont dans logs/erreurs.log
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', dirname(__DIR__) . '/logs/erreurs.log');

// session : le cookie est illisible en JavaScript et n'est pas envoyé par les sites étrangers
session_set_cookie_params([
    'lifetime' => 0,              // il disparaît à la fermeture du navigateur
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']),
]);
session_start();

// 30 minutes sans rien faire : on vide la session (l'utilisateur est déconnecté)
if (isset($_SESSION['derniere_activite']) && time() - $_SESSION['derniere_activite'] > 1800) {
    $_SESSION = [];
    session_regenerate_id(true);
}
$_SESSION['derniere_activite'] = time();

// jeton anti-CSRF : un par session, mis dans chaque formulaire qui modifie des données
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// inclusion du fichier de configuration si config-prod.php existe
require_once(file_exists('../config-prod.php') ? '../config-prod.php' : '../config-dev.php');

// Autoload fonctionnel avec les namespaces personnels,
spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);
    require RACINE_PATH . '/' . $class . '.php';
});

// fonctions utiles aux formulaires
require_once RACINE_PATH . '/controller/outils.php';

// message affiché une seule fois (ex. "Vous êtes déconnecté."), posé par un contrôleur avant une redirection
$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);

// l'utilisateur connecté, ou null : ['id' => ..., 'username' => ...]
$utilisateur = $_SESSION['user'] ?? null;

// les erreurs du formulaire envoyé (champ => message)
$erreurs = [];

// page demandée, l'accueil par défaut
$pg = $_GET['pg'] ?? 'accueil';

// Connexion à la base de données en singleton
try {
    $db = MyPDO::getInstance();
} catch (Exception $e) {
    // base indisponible : le visiteur voit une page propre, le détail est dans le journal
    error_log('[Maison Rosalie] ' . $e->getMessage());
    http_response_code(503);
    require RACINE_PATH . '/view/erreur.php';
    exit;
}

// le routeur choisit le contrôleur (comme dans choco-classe1)
require RACINE_PATH . '/controller/RouterController.php';
