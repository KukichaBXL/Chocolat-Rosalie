<?php
// path: controller/PrivateController.php
// typage strict
declare(strict_types=1);

// Contrôleur des utilisateurs CONNECTÉS : il n'est inclus par le routeur que si quelqu'un est connecté.
// Variables disponibles en plus : $compte (le compte lu dans la base par RouterController).

use model\manager\UserManager;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && lireTexte('action') === 'deconnexion') {
    if (verifierCsrf()) {
        $_SESSION = [];                  // on vide la session...
        session_regenerate_id(true);     // ...et l'ancien identifiant ne marche plus
        $_SESSION['message'] = 'Vous êtes déconnecté.';
    }
    header('Location: ' . RACINE_URL);
    exit;
}

// changer son mot de passe (formulaire de la page /compte)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && lireTexte('action') === 'changer-mot-de-passe' && verifierCsrf()) {
    // les mots de passe ne sont pas "trimés" : un espace peut en faire partie
    $ancien = is_string($_POST['ancien_pwd'] ?? null) ? $_POST['ancien_pwd'] : '';
    $nouveau = is_string($_POST['nouveau_pwd'] ?? null) ? $_POST['nouveau_pwd'] : '';
    $confirmation = is_string($_POST['confirmation_pwd'] ?? null) ? $_POST['confirmation_pwd'] : '';

    $userManager = new UserManager($db);
    $email = (string) $compte->getEmail();
    // $compte (RouterController) n'a pas le mot de passe : on relit le compte complet pour le vérifier
    $motDePasseActuel = (string) $userManager->getByEmail($email)?->getPassword();
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';

    // même protection que la connexion : trop d'essais ratés, on bloque 15 minutes
    $echecs = $userManager->countRecentFailures($ip, $email, 15);
    if ($echecs['email'] >= 5 || $echecs['ip'] >= 20) {
        $erreurs['general'] = 'Trop de tentatives. Réessayez dans 15 minutes.';
    } elseif (!password_verify($ancien, $motDePasseActuel)) {
        $userManager->addFailure($ip, $email);
        $erreurs['ancien_pwd'] = 'Le mot de passe actuel est incorrect.';
    } else {
        // même règle qu'à l'inscription : 8 à 72 caractères avec une majuscule, une minuscule et un chiffre
        if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,72}$/', $nouveau) !== 1) {
            $erreurs['nouveau_pwd'] = 'Le mot de passe doit contenir de 8 à 72 caractères, avec une majuscule, une minuscule et un chiffre.';
        } elseif ($nouveau === $ancien) {
            $erreurs['nouveau_pwd'] = 'Le nouveau mot de passe doit être différent de l\'ancien.';
        }
        if ($nouveau !== $confirmation) {
            $erreurs['confirmation_pwd'] = 'Les deux mots de passe ne sont pas identiques.';
        }
    }

    // tout est bon : on enregistre le mot de passe haché
    if ($erreurs === []) {
        $userManager->updatePassword($utilisateur['id'], password_hash($nouveau, PASSWORD_DEFAULT));
        $userManager->clearFailures($email);
        session_regenerate_id(true);
        $_SESSION['message'] = 'Votre mot de passe a été modifié.';
        header('Location: ' . RACINE_URL . 'compte');
        exit;
    }
    // sinon on continue : la page /compte s'affiche avec les messages d'erreur
}
