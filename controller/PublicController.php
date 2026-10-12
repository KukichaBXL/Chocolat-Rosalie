<?php
// path: controller/PublicController.php
// typage strict
declare(strict_types=1);

// Contrôleur public, en deux temps :
//   1. si un formulaire a été envoyé (POST) : inscription, connexion, contact
//   2. PRÉPARER les données de la page demandée avec les managers, puis inclure la vue (les vues ne font qu'afficher)


use model\manager\CategoryManager;
use model\manager\CommentManager;
use model\manager\IngredientManager;
use model\manager\MessageContactManager;
use model\manager\RatingManager;
use model\manager\RecipeManager;
use model\manager\StepManager;
use model\manager\UserManager;

// ===== 1. Un formulaire a été envoyé =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // formulaire sans jeton valide : on arrête tout
    if (!verifierCsrf()) {
        http_response_code(403);
        $erreurs['connexion'] = 'Votre formulaire a expiré. Rechargez la page et réessayez.';
    } else {
        switch (lireTexte('action')) {
            case 'inscription':
                $pseudo = lireTexte('username');
                $email = mb_strtolower(lireTexte('email'));
                // les mots de passe ne sont pas "trimés" : un espace peut en faire partie
                $motDePasse = is_string($_POST['pwd'] ?? null) ? $_POST['pwd'] : '';
                $confirmation = is_string($_POST['pwd_confirm'] ?? null) ? $_POST['pwd_confirm'] : '';

                // 1) on vérifie chaque champ
                if (preg_match('/^[\p{L}\p{N}._-]{3,30}$/u', $pseudo) !== 1) {
                    $erreurs['username'] = 'Le nom d\'utilisateur doit contenir de 3 à 30 lettres, chiffres, points, tirets ou soulignés.';
                }
                if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($email) > 120) {
                    $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
                }
                // règle du mot de passe : 8 à 72 caractères avec une majuscule, une minuscule et un chiffre
                if (preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,72}$/', $motDePasse) !== 1) {
                    $erreurs['pwd'] = 'Le mot de passe doit contenir de 8 à 72 caractères, avec une majuscule, une minuscule et un chiffre.';
                }
                if ($motDePasse !== $confirmation) {
                    $erreurs['pwd_confirm'] = 'Les deux mots de passe ne sont pas identiques.';
                }

                // 2) le nom et l'email ne doivent pas déjà exister
                $userManager = new UserManager($db);
                if (!isset($erreurs['username']) && $userManager->usernameExists($pseudo)) {
                    $erreurs['username'] = 'Ce nom d\'utilisateur est déjà pris.';
                }
                if (!isset($erreurs['email']) && $userManager->emailExists($email)) {
                    $erreurs['email'] = 'Un compte existe déjà avec cette adresse email.';
                }

                // 3) tout est bon : on crée le compte (mot de passe haché) et on connecte la personne
                if ($erreurs === []) {
                    $nouveau = $userManager->create($pseudo, $email, password_hash($motDePasse, PASSWORD_DEFAULT));
                    session_regenerate_id(true); // nouvel identifiant de session à la connexion
                    $_SESSION['user'] = ['id' => $nouveau->getId(), 'username' => $nouveau->getUsername()];
                    $_SESSION['message'] = 'Bienvenue ' . $pseudo . ', votre compte est créé.';
                    header('Location: ' . adresseDeRetour());
                    exit;
                }
                // s'il y a des erreurs, on continue : la page /connection s'affiche avec les messages
                break;

            case 'connexion':
                $email = mb_strtolower(lireTexte('email'));
                $motDePasse = is_string($_POST['pwd'] ?? null) ? $_POST['pwd'] : '';
                $ip = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';
                $userManager = new UserManager($db);

                // trop d'échecs récents (5 pour cet email ou 20 pour cette adresse IP en 15 minutes) : on bloque
                $echecs = $userManager->countRecentFailures($ip, $email, 15);
                if ($echecs['email'] >= 5 || $echecs['ip'] >= 20) {
                    $erreurs['connexion'] = 'Trop de tentatives. Réessayez dans 15 minutes.';
                    break;
                }

                $compte = $userManager->getByEmail($email);
                // le même message que l'email ou le mot de passe soit faux : on ne révèle rien
                if ($compte === null || !password_verify($motDePasse, $compte->getPassword())) {
                    $userManager->addFailure($ip, $email);
                    $erreurs['connexion'] = 'Email ou mot de passe incorrect.';
                    break;
                }

                $userManager->clearFailures($email);
                session_regenerate_id(true); // nouvel identifiant de session à la connexion
                $_SESSION['user'] = ['id' => $compte->getId(), 'username' => $compte->getUsername()];
                $_SESSION['message'] = 'Bonjour ' . $compte->getUsername() . ' !';
                header('Location: ' . adresseDeRetour());
                exit;

            case 'contact':
                // champ piège invisible pour un humain : s'il est rempli, c'est un robot.
                // On répond "merci" sans rien enregistrer, pour ne pas lui dire qu'il est repéré.
                if (lireTexte('site_web') !== '') {
                    $_SESSION['message'] = 'Merci, votre message a bien été envoyé.';
                    header('Location: ' . RACINE_URL . 'contact');
                    exit;
                }

                $nom = lireTexte('nom');
                $email = lireTexte('email');
                $sujet = lireTexte('sujet');
                $texte = lireTexte('message');

                if (preg_match('/^[\p{L}\s\'-]{2,80}$/u', $nom) !== 1) {
                    $erreurs['nom'] = 'Le nom doit contenir entre 2 et 80 lettres.';
                }
                if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($email) > 120) {
                    $erreurs['email'] = 'L\'adresse email n\'est pas valide (exemple : nom@domaine.be).';
                }
                if (mb_strlen($sujet) > 120) {
                    $erreurs['sujet'] = 'Le sujet ne doit pas dépasser 120 caractères.';
                }
                if (mb_strlen($texte) < 3 || mb_strlen($texte) > 500) {
                    $erreurs['message'] = 'Le message doit contenir entre 3 et 500 caractères.';
                }

                // anti-spam : 3 messages au maximum par tranche de 10 minutes et par visiteur
                $envois = array_filter($_SESSION['contact_envois'] ?? [], fn($moment) => $moment > time() - 600);
                if ($erreurs === [] && count($envois) >= 3) {
                    $erreurs['general'] = 'Vous avez déjà envoyé 3 messages. Réessayez dans quelques minutes.';
                }

                if ($erreurs === []) {
                    (new MessageContactManager($db))->add($nom, $email, $sujet === '' ? null : $sujet, $texte);
                    $envois[] = time();
                    $_SESSION['contact_envois'] = array_values($envois);
                    $_SESSION['message'] = 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.';
                    header('Location: ' . RACINE_URL . 'contact');
                    exit;
                }
                break;
        }
    }
}

// la liste des recettes pour le menu du header : chargée pour TOUTES les pages
$recettesMenu = (new RecipeManager($db))->getAll();

// ===== 2. Affichage de la page =====

switch ($pg) {
    case 'accueil':
        require RACINE_PATH . '/view/accueil.php';
        break;

    case 'recettes': // Pour evité de charger les recettes 2 fois sur la page recette a cause du dropdown
        $recettes = $recettesMenu;
        require RACINE_PATH . '/view/recettes.php';
        break;

    // une recette : /recette/mousse-au-chocolat
    case 'recette':
        $slug = $_GET['slug'] ?? '';
        $recette = is_string($slug) ? (new RecipeManager($db))->getOneBySlug($slug) : null;

        // recette inconnue : page 404
        if ($recette === null) {
            http_response_code(404);
            require RACINE_PATH . '/view/404.php';
            break;
        }

        $ingredients = (new IngredientManager($db))->getByRecipe($recette->getId());
        $etapes = (new StepManager($db))->getByRecipe($recette->getId());

        // la note : moyenne, nombre de votes, et la note de la personne connectée (null si elle n'a pas voté)
        $noteManager = new RatingManager($db);
        $stats = $noteManager->getStats($recette->getId());
        $maNote = $utilisateur === null ? null : $noteManager->getUserRate($utilisateur['id'], $recette->getId());

        // 10 commentaires à la fois : ?avis=2 en affiche 20, ?avis=3 en affiche 30...
        $pageAvis = isset($_GET['avis']) ? max(1, min(50, (int) $_GET['avis'])) : 1;
        $commentaires = (new CommentManager($db))->getByRecipe($recette->getId(), CommentManager::PAGE_SIZE * $pageAvis, 0);

        require RACINE_PATH . '/view/recette.php';
        break;

    // l'administration : réservée aux administrateurs
    case 'admin':
        if (!$estAdmin) {
            $_SESSION['message'] = 'Cette page est réservée aux administrateurs.';
            header('Location: ' . RACINE_URL);
            exit;
        }
        $categories = (new CategoryManager($db))->getAll();
        $recettes = (new RecipeManager($db))->getAll();
        $commentairesAdmin = (new CommentManager($db))->getAllForAdmin(50);
        require RACINE_PATH . '/view/admin.php';
        break;

    // le compte de la personne connectée (changer son mot de passe)
    case 'compte':
        if ($utilisateur === null) {
            $_SESSION['message'] = 'Connectez-vous pour accéder à votre compte.';
            header('Location: ' . RACINE_URL);
            exit;
        }
        $monCompte = (new UserManager($db))->getById($utilisateur['id']);
        require RACINE_PATH . '/view/compte.php';
        break;

    // pages sans données à préparer
    case 'a-propos':
    case 'contact':
    case 'connection':
        require RACINE_PATH . '/view/' . $pg . '.php';
        break;

    default:
        http_response_code(404);
        require RACINE_PATH . '/view/404.php';
}
