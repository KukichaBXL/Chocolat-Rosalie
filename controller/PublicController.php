<?php
// path: controller/PublicController.php
// typage strict
declare(strict_types=1);

// Contrôleur public, en deux temps :
//   1. si un formulaire a été envoyé (POST) : inscription, connexion, contact, newsletter, désinscription
//   2. préparer les données de la page demandée avec les managers, puis inclure la vue (les vues ne font qu'afficher)

use model\manager\CategoryManager;
use model\manager\CommentManager;
use model\manager\IngredientManager;
use model\Mailer;
use model\manager\MessageContactManager;
use model\manager\NewsletterManager;
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
                $motDePasse = lireMotDePasse('pwd');
                $confirmation = lireMotDePasse('pwd_confirm');

                // 1) on vérifie chaque champ
                if (preg_match('/^[\p{L}\p{N}._-]{3,30}$/u', $pseudo) !== 1) {
                    $erreurs['username'] = 'Le nom d\'utilisateur doit contenir de 3 à 30 lettres, chiffres, points, tirets ou soulignés.';
                }
                if (!emailValide($email)) {
                    $erreurs['email'] = 'L\'adresse email n\'est pas valide.';
                }
                if (!motDePasseValide($motDePasse)) {
                    $erreurs['pwd'] = 'Le mot de passe doit contenir de 8 à 72 caractères, avec une majuscule, une minuscule et un chiffre.';
                }
                if ($motDePasse !== $confirmation) {
                    $erreurs['pwd_confirm'] = 'Les deux mots de passe ne sont pas identiques.';
                }
                // consentement RGPD obligatoire
                if (($_POST['rgpd'] ?? '') !== '1') {
                    $erreurs['rgpd'] = 'Vous devez accepter la politique de confidentialité pour créer un compte.';
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
                    $verification = verificationEmailActive();
                    $nouveau = $userManager->create($pseudo, $email, password_hash($motDePasse, PASSWORD_DEFAULT), $verification ? 0 : 1);

                    if ($verification) {
                        // le compte est créé mais inactif : il faut cliquer sur le lien reçu par e-mail
                        $lien = lienVerification($nouveau->getId(), $email);
                        Mailer::envoyer(
                            $email,
                            'Confirmez votre adresse e-mail - Maison Rosalie',
                            'Bonjour ' . $pseudo . ",\n\nBienvenue à la Maison Rosalie ! Pour activer votre compte, confirmez votre adresse e-mail :\n" . $lien . "\n\nSi vous n'êtes pas à l'origine de cette inscription, ignorez simplement ce message.\n\nLa Maison Rosalie\n",
                            '<h2>Bienvenue chez Maison Rosalie !</h2><p>Bonjour ' . htmlspecialchars($pseudo) . ',</p><p>Pour activer votre compte, confirmez votre adresse e-mail :</p><p><a href="' . htmlspecialchars($lien) . '">Confirmer mon adresse e-mail</a></p><p>Si vous n\'êtes pas à l\'origine de cette inscription, ignorez simplement ce message.</p>'
                        );
                        rediriger(RACINE_URL, 'Votre compte est créé. Un e-mail de confirmation vient de vous être envoyé : cliquez sur le lien qu\'il contient pour activer votre compte.');
                    }

                    // vérification désactivée : on connecte directement la personne
                    session_regenerate_id(true); // nouvel identifiant de session à la connexion
                    $_SESSION['user'] = ['id' => $nouveau->getId(), 'username' => $nouveau->getUsername()];
                    $_SESSION['message'] = 'Bienvenue ' . $pseudo . ', votre compte est créé.';
                    Mailer::envoyer($email, 'Bienvenue à la Maison Rosalie', 'Bonjour ' . $pseudo . ",\n\nVotre compte est créé : vous pouvez maintenant noter les recettes et laisser un commentaire.\n\nÀ très vite,\nLa Maison Rosalie\n");
                    rediriger(adresseDeRetour());
                }
                // s'il y a des erreurs, on continue : la page /connection s'affiche avec les messages
                break;

            case 'connexion':
                $email = mb_strtolower(lireTexte('email'));
                $motDePasse = lireMotDePasse('pwd');
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

                // mot de passe correct, mais adresse e-mail pas encore confirmée
                if (verificationEmailActive() && $compte->getEmailVerified() === 0) {
                    $erreurs['connexion'] = 'Confirmez d\'abord votre adresse e-mail : cliquez sur le lien reçu par e-mail à l\'inscription.';
                    break;
                }

                $userManager->clearFailures($email);
                session_regenerate_id(true); // nouvel identifiant de session à la connexion
                $_SESSION['user'] = ['id' => $compte->getId(), 'username' => $compte->getUsername()];
                rediriger(adresseDeRetour(), 'Bonjour ' . $compte->getUsername() . ' !');

                // l'inscription à la newsletter (le formulaire est dans le pied de page de toutes les pages)
            case 'newsletter':
                $retour = explode('#', adresseDeRetour())[0]; // sans le « #votre-note » ajouté sur les pages de recette
                // champ piège : rempli, c'est un robot (on répond « merci » sans rien enregistrer)
                if (lireTexte('site_web') !== '') {
                    rediriger($retour, 'Merci, vous êtes inscrit(e) à la newsletter.');
                }
                $emailNews = mb_strtolower(lireTexte('newsletter-email'));
                if (!emailValide($emailNews)) {
                    rediriger($retour, 'L\'adresse email de la newsletter n\'est pas valide.');
                }
                // anti-spam : 5 inscriptions au maximum par heure et par visiteur
                $inscriptions = array_filter($_SESSION['newsletter_envois'] ?? [], fn($moment) => $moment > time() - 3600);
                if (count($inscriptions) >= 5) {
                    rediriger($retour, 'Trop d\'inscriptions pour le moment. Réessayez plus tard.');
                }
                $inscriptions[] = time();
                $_SESSION['newsletter_envois'] = array_values($inscriptions);

                $newsletterManager = new NewsletterManager($db);
                if ($newsletterManager->subscribe($emailNews)) {
                    $abonne = $newsletterManager->getByEmail($emailNews);
                    Mailer::envoyer($emailNews, 'Vous êtes inscrit(e) à la newsletter', texteNewsletter("Bonjour,\n\nMerci de votre inscription : vous recevrez bientôt les nouveautés de la Maison Rosalie.", (int) $abonne['id'], $emailNews));
                    $_SESSION['message'] = 'Merci, vous êtes inscrit(e) à la newsletter. Un e-mail de confirmation vient de vous être envoyé.';
                } else {
                    $_SESSION['message'] = 'Cette adresse est déjà inscrite à la newsletter.';
                }
                rediriger($retour);

                // le bouton « Me désinscrire » de la page ouverte par le lien reçu par e-mail
            case 'desinscription':
                $idAbonne = lireEntier('id', 1, PHP_INT_MAX);
                $newsletterManager = new NewsletterManager($db);
                $abonne = $idAbonne === null ? null : $newsletterManager->getById($idAbonne);
                if ($abonne !== null && hash_equals(jetonDesinscription($idAbonne, $abonne['email']), lireTexte('jeton'))) {
                    $newsletterManager->remove($idAbonne);
                    $_SESSION['message'] = 'Vous êtes désinscrit(e) de la newsletter.';
                } else {
                    $_SESSION['message'] = 'Ce lien de désinscription n\'est pas valide.';
                }
                rediriger(RACINE_URL);

            case 'contact':
                // champ piège invisible pour un humain : s'il est rempli, c'est un robot.
                // On répond "merci" sans rien enregistrer, pour ne pas lui dire qu'il est repéré.
                if (lireTexte('site_web') !== '') {
                    rediriger(RACINE_URL . 'contact', 'Merci, votre message a bien été envoyé.');
                }

                $nom = lireTexte('nom');
                $email = lireTexte('email');
                $sujet = lireTexte('sujet');
                $texte = lireTexte('message');

                if (preg_match('/^[\p{L}\s\'-]{2,80}$/u', $nom) !== 1) {
                    $erreurs['nom'] = 'Le nom doit contenir entre 2 et 80 lettres.';
                }
                if (!emailValide($email)) {
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
                    rediriger(RACINE_URL . 'contact', 'Merci, votre message a bien été envoyé. Nous vous répondrons rapidement.');
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
        $top3 = (new RecipeManager($db))->getTop3();
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

        // la moyenne, le nombre de votes, et la note de la personne connectée (null si elle n'a pas voté)
        $noteManager = new RatingManager($db);
        $stats = $noteManager->getStats($recette->getId());
        $maNote = $utilisateur === null ? null : $noteManager->getUserRate($utilisateur['id'], $recette->getId());

        // 10 commentaires à la fois : ?avis=2 en affiche 20, ?avis=3 en affiche 30...
        $pageAvis = isset($_GET['avis']) ? max(1, min(50, (int) $_GET['avis'])) : 1;
        $commentaires = (new CommentManager($db))->getByRecipe($recette->getId(), CommentManager::PAGE_SIZE * $pageAvis, 0, $estAdmin);

        require RACINE_PATH . '/view/recette.php';
        break;

    // l'administration : réservée aux administrateurs
    case 'admin':
        if (!$estAdmin) {
            rediriger(RACINE_URL, 'Cette page est réservée aux administrateurs.');
        }

        // chaque onglet a son adresse (/admin/recettes...) : on ne charge que les données de l'onglet affiché
        $onglets = [
            'recettes' => 'Recettes',
            'ajouter' => 'Ajouter une recette',
            'commentaires' => 'Commentaires',
            'messages' => 'Messages',
            'newsletter' => 'Newsletter',
        ];
        $onglet = $_GET['slug'] ?? 'recettes';
        if (!is_string($onglet) || !isset($onglets[$onglet])) {
            $onglet = 'recettes';
        }

        switch ($onglet) {
            case 'recettes':
                $recettes = (new RecipeManager($db))->getAll(true); // true : avec les recettes masquées
                break;
            case 'ajouter':
                $categories = (new CategoryManager($db))->getAll();
                break;
            case 'commentaires':
                $commentairesAdmin = (new CommentManager($db))->getAllForAdmin(50);
                break;
            case 'messages':
                $messages = (new MessageContactManager($db))->getAll(100);
                break;
            case 'newsletter':
                $newsletterManager = new NewsletterManager($db);
                $abonnes = $newsletterManager->getAll();
                $messageNewsletter = $newsletterManager->getMessage();
                break;
        }
        require RACINE_PATH . '/view/admin.php';
        break;

    // le compte de la personne connectée (changer son mot de passe)
    case 'compte':
        if ($utilisateur === null) {
            rediriger(RACINE_URL, 'Connectez-vous pour accéder à votre compte.');
        }
        $monCompte = (new UserManager($db))->getById($utilisateur['id']);
        require RACINE_PATH . '/view/compte.php';
        break;

    // le lien de confirmation reçu à l'inscription : /verification/12/jeton (active le compte)
    case 'verification':
        $verifie = false;
        [$idCompte, $jetonRecu] = lireLienSigne();
        if ($idCompte !== null) {
            $managerUsers = new UserManager($db);
            $candidat = $managerUsers->getById($idCompte);
            if ($candidat !== null && hash_equals(jetonVerification($idCompte, (string) $candidat->getEmail()), $jetonRecu)) {
                $managerUsers->verifyEmail($idCompte);
                $verifie = true;
            }
        }
        require RACINE_PATH . '/view/verification.php';
        break;

    // le lien reçu par e-mail : /desinscription/12/jeton (on affiche un bouton de confirmation, rien n'est supprimé en GET)
    case 'desinscription':
        $abonne = null;
        [$idAbonne, $jetonRecu] = lireLienSigne();
        if ($idAbonne !== null) {
            $candidat = (new NewsletterManager($db))->getById($idAbonne);
            if ($candidat !== null && hash_equals(jetonDesinscription($idAbonne, $candidat['email']), $jetonRecu)) {
                $abonne = $candidat;
            }
        }
        require RACINE_PATH . '/view/desinscription.php';
        break;

    // pages sans données à préparer
    case 'a-propos':
    case 'mentions-legales':
    case 'contact':
    case 'connection':
        require RACINE_PATH . '/view/' . $pg . '.php';
        break;

    default:
        http_response_code(404);
        require RACINE_PATH . '/view/404.php';
}
