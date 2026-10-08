<?php
// path: controller/outils.php

// champ caché du jeton anti-CSRF, à mettre dans chaque formulaire POST
function champCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '" />';
}

// nom du formulaire qui vient d'échouer ('connexion', 'inscription', 'commentaire', 'contact') ou ''
function formulaireEnErreur(): string
{
    global $flash;
    return $flash['formulaire'] ?? '';
}

// message d'erreur d'un champ, seulement si c'est CE formulaire qui a échoué
function erreur(string $formulaire, string $champ): string
{
    global $flash;
    if (formulaireEnErreur() !== $formulaire) {
        return '';
    }
    return htmlspecialchars($flash['erreurs'][$champ] ?? '');
}

// valeur saisie avant l'erreur, pour ne pas obliger à tout retaper
function ancien(string $formulaire, string $champ): string
{
    global $flash;
    if (formulaireEnErreur() !== $formulaire) {
        return '';
    }
    return htmlspecialchars($flash['anciennes'][$champ] ?? '');
}

// message général d'un formulaire en erreur (ex. "Email ou mot de passe incorrect.")
function messageFormulaire(string $formulaire): string
{
    global $flash;
    if (formulaireEnErreur() !== $formulaire) {
        return '';
    }
    return htmlspecialchars($flash['message'] ?? '');
}

// Fonctions utilitaires des contrôleurs 
use model\mapping\UserMapping;


// Garde un message en session, puis redirige.
// $formulaire : formulaire à ré-afficher avec ses erreurs ('connexion', 'inscription', 'commentaire', 'contact')
// $ancre : endroit de la page où revenir (ex. 'titre-avis')
// $destination : adresse complète où aller (ex. l'accueil après une connexion) ; vide = on revient sur la page du formulaire
function retour(string $type, string $message, string $formulaire = '', array $erreurs = [], array $anciennes = [], string $ancre = '', string $destination = ''): void
{
    $_SESSION['flash'] = [
        'type' => $type,               // 'succes' ou 'erreur'
        'message' => $message,
        'formulaire' => $formulaire,
        'erreurs' => $erreurs,         // un message par champ
        'anciennes' => $anciennes,     // ce que le visiteur avait saisi (jamais un mot de passe)
    ];
    if ($destination === '') {
        // on revient sur la même page, avec seulement son chemin
        $destination = '/' . ltrim((string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    }
    header('Location: ' . $destination . ($ancre === '' ? '' : '#' . $ancre));
    exit;
}

// Refuse le formulaire si le jeton anti-CSRF est absent ou faux.
// hash_equals compare en temps constant (pas d'indice sur le bon jeton)
function verifierCsrf(): void
{
    $jeton = $_POST['csrf_token'] ?? '';
    if (!is_string($jeton) || !hash_equals($_SESSION['csrf_token'], $jeton)) {
        retour('erreur', 'Votre formulaire a expiré. Rechargez la page et réessayez.');
    }
}

// Renvoie l'utilisateur connecté, ou renvoie le visiteur vers la page de connexion avec un message
function exigerConnexion(): array
{
    if (!isset($_SESSION['user'])) {
        retour('erreur', 'Vous devez être connecté pour faire cette action.', '', [], [], '', RACINE_URL . 'connection');
    }
    return $_SESSION['user'];
}

// Lit un texte envoyé en POST, sans espaces autour.
// Un tableau (champ[]=...) ou une valeur absente donne une chaîne vide
function lireTexte(string $cle): string
{
    $valeur = $_POST[$cle] ?? '';
    return is_string($valeur) ? trim($valeur) : '';
}

// Lit un entier entre $min et $max, ou null s'il est absent ou invalide ("4.5", "abc", "0", "6" pour une note -> null)
function lireEntier(string $cle, int $min, int $max): ?int
{
    $valeur = $_POST[$cle] ?? null;
    if (!is_string($valeur)) {
        return null;
    }
    $entier = filter_var($valeur, FILTER_VALIDATE_INT, ['options' => ['min_range' => $min, 'max_range' => $max]]);
    return $entier === false ? null : $entier;
}

// Ce qu'on garde en session : jamais l'email ni le mot de passe haché
function utilisateurPourSession(UserMapping $utilisateur): array
{
    return [
        'id' => $utilisateur->getId(),
        'username' => $utilisateur->getUsername(),
        'role' => $utilisateur->getRole(),
    ];
}
