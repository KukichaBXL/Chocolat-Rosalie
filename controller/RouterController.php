<?php
// path: controller/RouterController.php
// typage strict
declare(strict_types=1);

// Le routeur aiguille la requête vers le bon contrôleur :
// PrivateController : les actions des utilisateurs connectés (déconnexion...), seulement s'il y en a un
// AdminController : les actions de l'administration, seulement pour un administrateur
// PublicController : les formulaires ouverts à tous et l'affichage des pages


use model\manager\UserManager;

// $estAdmin : vrai seulement pour un administrateur.
$estAdmin = false;

if ($utilisateur !== null) {
    $compte = (new UserManager($db))->getById($utilisateur['id']);
    $estAdmin = $compte !== null && $compte->getRole() === 'admin';
    require_once RACINE_PATH . '/controller/PrivateController.php';
}

if ($estAdmin) {
    require_once RACINE_PATH . '/controller/AdminController.php';
}

require_once RACINE_PATH . '/controller/PublicController.php';
