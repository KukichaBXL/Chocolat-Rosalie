<?php
// path: controller/PublicController.php
// typage strict
declare(strict_types=1);

// Contrôleur public : il traite les formulaires ouverts à tous (inscription, connexion, contact),
// PRÉPARE les données de chaque page (avec les managers), puis inclut la vue.
// Variables disponibles : $db, $pg et $utilisateur (null pour un visiteur), créés dans public/index.php.

use model\manager\CommentManager;
use model\manager\IngredientManager;
use model\manager\MessageContactManager;
use model\manager\RecipeManager;
use model\manager\StepManager;
use model\manager\UserManager;

// --- Limites choisies par l'équipe 
const LOGIN_MAX_ECHECS_EMAIL = 5;       // 5 échecs sur un même compte
const LOGIN_MAX_ECHECS_IP = 20;         // ou 20 depuis une même adresse IP
const LOGIN_FENETRE_MINUTES = 15;       // en 15 minutes : connexion bloquée
const CONTACT_MAX = 3;                  // 3 messages de contact maximum
const CONTACT_FENETRE_SECONDES = 600;   // par tranche de 10 minutes et par
visiteur

const REGEX_MOT_DE_PASSE = '/^(?=.*\p{Ll})(?=.*\p{Lu})(?=.*\d).{10,72}$/u';
// Nom d'utilisateur : 3 à 30 lettres, chiffres, points, tirets ou soulignés
const REGEX_PSEUDO = '/^[\p{L}\p{N}._-]{3,30}$/u';
// Nom dans le formulaire de contact : lettres, espaces, apostrophes et tirets
const REGEX_NOM = '/^[\p{L}\s\'’-]{2,80}$/u';

//  Un formulaire a été envoyé en POST 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // le jeton anti-CSRF est vérifié pour TOUS les formulaires
    verifierCsrf();

    switch (lireTexte('action')) {
        case 'inscription':
            $pseudo = lireTexte('username');
            $email = mb_strtolower(lireTexte('email'));
            // les mots de passe ne sont pas "trimés" : un espace peut en faire partie
            $motDePasse = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
            $confirmation = is_string($_POST['password_confirm'] ?? null) ? $_POST['password_confirm'] : '';
            $anciennes = ['username' => $pseudo, 'email' => $email];

            $erreurs = [];
            if (preg_match(REGEX_PSEUDO, $pseudo) !== 1) {
                $erreurs['username'] = 'Le nom d\'utilisateur doit contenir de 3 à 30 lettres, chiffres, points, tirets ou soulignés.';
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($email) > 120) {
                $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
            }
            if (preg_match(REGEX_MOT_DE_PASSE, $motDePasse) !== 1) {
                $erreurs['password'] = 'Le mot de passe doit contenir de 10 à 72 caractères, dont une minuscule, une majuscule et un chiffre.';
            }
            if ($motDePasse !== $confirmation) {
                $erreurs['password_confirm'] = 'Les deux mots de passe ne sont pas identiques.';
            }
            if ($erreurs !== []) {
                retour('erreur', 'Certains champs sont invalides.', 'inscription', $erreurs, $anciennes);
            }

            // unicité vérifiée ici pour un message clair ; la base la garantit aussi (index UNIQUE)
            $userManager = new UserManager($db);
            if ($userManager->usernameExists($pseudo)) {
                $erreurs['username'] = 'Ce nom d\'utilisateur est déjà pris.';
            }
            if ($userManager->emailExists($email)) {
                $erreurs['email'] = 'Un compte existe déjà avec cette adresse email.';
            }
            if ($erreurs !== []) {
                retour('erreur', 'Ce compte existe déjà.', 'inscription', $erreurs, $anciennes);
            }

            // mot de passe haché avec l'algorithme recommandé par PHP (bcrypt aujourd'hui)
            $nouvelUtilisateur = $userManager->create($pseudo, $email, password_hash($motDePasse, PASSWORD_DEFAULT));

            // connexion directe après l'inscription, avec un nouvel identifiant de session
            session_regenerate_id(true);
            $_SESSION['user'] = utilisateurPourSession($nouvelUtilisateur);
            retour('succes', 'Bienvenue ' . $pseudo . ', votre compte est créé.', '', [], [], '', RACINE_URL);
            break;

        // Connexion

        case 'connexion':
            $email = mb_strtolower(lireTexte('email'));
            $motDePasse = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'inconnue';

            if ($email === '' || $motDePasse === '') {
                retour('erreur', 'Merci de remplir l\'email et le mot de passe.', 'connexion', [], ['email' => $email]);
            }

            // trop d'échecs récents : on bloque avant même de vérifier le mot de passe
            $userManager = new UserManager($db);
            $echecs = $userManager->countRecentFailures($ip, $email, LOGIN_FENETRE_MINUTES);
            if ($echecs['email'] >= LOGIN_MAX_ECHECS_EMAIL || $echecs['ip'] >= LOGIN_MAX_ECHECS_IP) {
                retour('erreur', 'Trop de tentatives de connexion. Réessayez dans ' . LOGIN_FENETRE_MINUTES . ' minutes.', 'connexion', [], ['email' => $email]);
            }

            $utilisateurTrouve = $userManager->getByEmail($email);
            // si l'email n'existe pas, on vérifie quand même un faux hachage
            $hachage = $utilisateurTrouve?->getPassword() ?? '$2y$10$1F0bBcbLD46KeVG1ofj7cOGi.VrKXWmU/PyBqKMjK.KjAAW0kH2jO';
            if (!password_verify($motDePasse, $hachage) || $utilisateurTrouve === null) {
                $userManager->addFailure($ip, $email);
                // message identique que l'email ou le mot de passe soit faux
                retour('erreur', 'Email ou mot de passe incorrect.', 'connexion', [], ['email' => $email]);
            }

            $userManager->clearFailures($email);
            session_regenerate_id(true); // nouvel identifiant à la connexion
            $_SESSION['user'] = utilisateurPourSession($utilisateurTrouve);
            retour('succes', 'Bonjour ' . $utilisateurTrouve->getUsername() . ' !', '', [], [], '', RACINE_URL);
            break;

        // Déconnexion : la session est vidée et son identifiant change

        case 'contact':
            // anti-spam champ piège invisible pour les humains, rempli par les robots
            // On répond "succès" pour ne pas leur indiquer qu'ils ont été repérés, sans rien enregistrer
            if (lireTexte('site_web') !== '') {
                retour('succes', 'Merci, votre message a bien été envoyé.');
            }

            // anti-spam 3 messages maximum par tranche de 10 minutes et par visiteur
            $envoisRecents = array_filter(
                $_SESSION['contact_envois'] ?? [],
                fn ($moment) => $moment > time() - CONTACT_FENETRE_SECONDES
            );
            $nom = lireTexte('nom');
            $email = lireTexte('email');
            $sujet = lireTexte('sujet');
            $message = lireTexte('message');
            $anciennes = ['nom' => $nom, 'email' => $email, 'sujet' => $sujet, 'message' => $message];

            if (count($envoisRecents) >= CONTACT_MAX) {
                retour('erreur', 'Vous avez déjà envoyé plusieurs messages. Réessayez dans quelques minutes.', 'contact', [], $anciennes);
            }

            $erreurs = [];
            if (preg_match(REGEX_NOM, $nom) !== 1) {
                $erreurs['nom'] = 'Le nom doit contenir entre 2 et 80 lettres (espaces, tirets et apostrophes autorisés).';
            }
            if (filter_var($email, FILTER_VALIDATE_EMAIL) === false || mb_strlen($email) > 120) {
                $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
            }
            if (mb_strlen($sujet) > 120) {
                $erreurs['sujet'] = 'Le sujet ne doit pas dépasser 120 caractères.';
            }
            if (mb_strlen($message) < 3 || mb_strlen($message) > 500) {
                $erreurs['message'] = 'Le message doit contenir entre 3 et 500 caractères.';
            }
            if ($erreurs !== []) {
                retour('erreur', 'Merci de corriger les champs indiqués.', 'contact', $erreurs, $anciennes);
            }

            (new MessageContactManager($db))->add($nom, $email, $sujet === '' ? null : $sujet, $message);
            $envoisRecents[] = time();
            $_SESSION['contact_envois'] = array_values($envoisRecents);
            retour('succes', 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.');
            break;

        // actions réservées aux connectés (traitées par PrivateController) : un visiteur est renvoyé vers la page de connexion
        case 'deconnexion':
            exigerConnexion();
            break;

        default:
            retour('erreur', 'Action inconnue.');
    }
}

// Affichage de la page
switch ($pg) {
    // page d'accueil
    case 'accueil':
        require RACINE_PATH.'/view/accueil.php';
        break;

    // liste des recettes
    case 'recettes':
        $recettes = (new RecipeManager($db))->getAll();
        require RACINE_PATH.'/view/recettes.php';
        break;

    // une recette 
    case 'recette':
        // le slug vient de l'adresse
        $slug = $_GET['slug'] ?? '';
        $recette = is_string($slug) ? (new RecipeManager($db))->getOneBySlug($slug) : null;

        // recette inconnue 
        if ($recette === null) {
            http_response_code(404);
            require RACINE_PATH.'/view/404.php';
            break;
        }

        // les ingrédients et les étapes de CETTE recette
        $ingredients = (new IngredientManager($db))->getByRecipe($recette->getId());
        $etapes = (new StepManager($db))->getByRecipe($recette->getId());

        // les commentaires, du plus récent au plus ancien, 10 à la fois
        $pageAvis = isset($_GET['avis']) ? max(1, min(50, (int) $_GET['avis'])) : 1;
        $commentaires = (new CommentManager($db))->getByRecipe($recette->getId(), CommentManager::PAGE_SIZE * $pageAvis, 0);
        require RACINE_PATH.'/view/recette.php';
        break;

    // pages sans données à préparer
    case 'a-propos':
    case 'contact':
    case 'connection':
        require RACINE_PATH.'/view/'.$pg.'.php';
        break;

    // toute autre adresse : page 404
    default:
        http_response_code(404);
        require RACINE_PATH.'/view/404.php';
}
