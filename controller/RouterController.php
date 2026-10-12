<?php
// path: controller/RouterController.php
// typage strict
declare(strict_types=1);

// Le routeur aiguille la requête vers le bon contrôleur :
//   - PrivateController : les actions des utilisateurs connectés (déconnexion...), seulement s'il y en a un
//   - AdminController : les actions de l'administration, seulement pour un administrateur
//   - ApiController : les réponses JSON appelées par le JavaScript (adresses /api/...)
//   - PublicController : les formulaires ouverts à tous et l'affichage des pages

use model\manager\UserManager;

// $estAdmin : vrai seulement pour un administrateur.
$estAdmin = false;

if ($utilisateur !== null) {
    $compte = (new UserManager($db))->getById($utilisateur['id']);
    if ($compte === null) {
        // le compte n'existe plus (supprimé) : la session ne vaut plus rien
        unset($_SESSION['user']);
        $utilisateur = null;
    } else {
        $estAdmin = $compte->getRole() === 'admin';
        require_once RACINE_PATH . '/controller/PrivateController.php';
    }
}

// /api/... : des réponses JSON pour le JavaScript, pas de page HTML
if ($pg === 'api') {
    require_once RACINE_PATH . '/controller/ApiController.php';
    exit;
}

if ($estAdmin) {
    require_once RACINE_PATH . '/controller/AdminController.php';
}

require_once RACINE_PATH . '/controller/PublicController.php';
