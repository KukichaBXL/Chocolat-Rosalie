<?php
// path: controller/PrivateController.php
// typage strict
declare(strict_types=1);

// Contrôleur des utilisateurs CONNECTÉS : il traite les actions qui leur sont réservées.
// Il n'est inclus par RouterController que si $utilisateur n'est pas null.
// Après chaque action, retour() enregistre un message puis redirige : le reste du code n'est pas exécuté.

use model\manager\UserManager;

$actionsPrivees = ['deconnexion'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array(lireTexte('action'), $actionsPrivees, true)) {
    verifierCsrf();

    switch (lireTexte('action')) {
        case 'deconnexion':
            $_SESSION = [];
            session_regenerate_id(true);
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['derniere_activite'] = time();
            retour('succes', 'Vous êtes déconnecté.');
            break;

        // Noter une recette
    }
}
