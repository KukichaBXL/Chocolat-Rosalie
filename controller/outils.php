<?php
// path: controller/outils.php
// Trois petites fonctions utilisées par les contrôleurs et les vues.

// champ caché du jeton anti-CSRF
function champCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '" />';
}

// le jeton reçu est-il celui de la session ?
function verifierCsrf(): bool
{
    $jeton = $_POST['csrf_token'] ?? '';
    return is_string($jeton) && hash_equals($_SESSION['csrf_token'], $jeton);
}

// lit un champ texte du formulaire, sans espaces autour ('' s'il est absent)
function lireTexte(string $cle): string
{
    $valeur = $_POST[$cle] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}
