<?php
// path: controller/PrivateController.php
// typage strict
declare(strict_types=1);

// Contrôleur des utilisateurs CONNECTÉS : il n'est inclus par le routeur que si quelqu'un est connecté.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && lireTexte('action') === 'deconnexion') {
    if (verifierCsrf()) {
        $_SESSION = [];                  // on vide la session...
        session_regenerate_id(true);     // ...et l'ancien identifiant ne marche plus
        $_SESSION['message'] = 'Vous êtes déconnecté.';
    }
    header('Location: ' . RACINE_URL);
    exit;
}
