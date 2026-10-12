<?php
// path: controller/ApiController.php
// typage strict
declare(strict_types=1);

// L'API : des adresses /api/... qui répondent en JSON (jamais en HTML) au JavaScript du site.
// Chaque adresse est documentée dans docs/API.md.
// Variables disponibles : $db, $utilisateur (null si personne n'est connecté), $estAdmin.

use model\mapping\CommentMapping;
use model\manager\CommentManager;
use model\manager\RatingManager;
use model\manager\RecipeManager;

// un commentaire sous la forme envoyée au JavaScript
function commentaireEnTableau(CommentMapping $commentaire, ?array $utilisateur, bool $estAdmin): array
{
    return [
        'id' => $commentaire->getCommentId(),
        'author' => $commentaire->getUsername(),
        'date' => date('d/m/Y', strtotime($commentaire->getCreatedAt())),
        'title' => $commentaire->getCommentTitle(),
        'text' => $commentaire->getCommentText(),
        'authorId' => $commentaire->getAuthorId(),
        // la note que l'auteur a donnée à la recette (null s'il n'a pas noté)
        'rate' => $commentaire->getRate(),
        // le bouton "Supprimer" est affiché à l'auteur et à l'administrateur (le serveur revérifie à la suppression)
        // le texte du bouton : l'auteur supprime, l'administrateur masque
        'deleteLabel' => $utilisateur !== null && $commentaire->getAuthorId() === $utilisateur['id'] ? 'Supprimer' : 'Masquer',
        'canDelete' => $utilisateur !== null && ($commentaire->getAuthorId() === $utilisateur['id'] || $estAdmin),
        // vrai si l'administrateur a masqué ce commentaire (seul l'administrateur reçoit les commentaires masqués)
        'masque' => $commentaire->getCommentStatus() === 'banni',
    ];
}

// /api/noter => $_GET['slug'] vaut 'noter'
$route = $_GET['slug'] ?? '';

switch ($route) {
    // ===== POST /api/noter : donner une note de 1 à 5 à une recette =====
    case 'noter':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            repondreJson(405, false, 'Cette adresse accepte seulement la méthode POST.');
        }
        if ($utilisateur === null) {
            repondreJson(401, false, 'Connectez-vous pour noter cette recette.');
        }
        if (!verifierCsrf()) {
            repondreJson(403, false, 'Formulaire expiré. Rechargez la page.');
        }

        // on ne fait jamais confiance au navigateur : lireEntier refuse 0, 6, "abc"...
        $idRecette = lireEntier('recipe_id', 1, PHP_INT_MAX);
        $note = lireEntier('rate', 1, 5);
        if ($idRecette === null || $note === null) {
            $erreurs = [];
            if ($idRecette === null) {
                $erreurs['recipe_id'] = 'Recette invalide.';
            }
            if ($note === null) {
                $erreurs['rate'] = 'La note doit être un nombre entier de 1 à 5.';
            }
            repondreJson(400, false, 'Données invalides.', [], $erreurs);
        }
        if (!(new RecipeManager($db))->exists($idRecette)) {
            repondreJson(404, false, 'Cette recette n\'existe pas.');
        }

        $noteManager = new RatingManager($db);
        $noteManager->rate($utilisateur['id'], $idRecette, $note);
        $stats = $noteManager->getStats($idRecette);
        repondreJson(200, true, 'Merci pour votre note !', [
            'rate' => $note,
            'average' => $stats['average'],
            'votes' => $stats['votes'],
        ]);

        // ===== GET /api/commentaires?recipe_id=1&offset=10 : 10 commentaires de plus =====
    case 'commentaires':
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            repondreJson(405, false, 'Cette adresse accepte seulement la méthode GET.');
        }
        $idRecette = filter_var($_GET['recipe_id'] ?? '', FILTER_VALIDATE_INT);
        $decalage = filter_var($_GET['offset'] ?? 0, FILTER_VALIDATE_INT);
        if ($idRecette === false || $idRecette < 1 || $decalage === false || $decalage < 0) {
            repondreJson(400, false, 'Données invalides.');
        }
        if (!(new RecipeManager($db))->exists($idRecette)) {
            repondreJson(404, false, 'Cette recette n\'existe pas.');
        }
        $resultat = (new CommentManager($db))->getByRecipe($idRecette, CommentManager::PAGE_SIZE, $decalage, $estAdmin);
        repondreJson(200, true, 'Commentaires chargés.', [
            'comments' => array_map(fn($c) => commentaireEnTableau($c, $utilisateur, $estAdmin), $resultat['comments']),
            'hasMore' => $resultat['hasMore'],
        ]);

        // ===== POST /api/commenter : ajouter un commentaire =====
    case 'commenter':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            repondreJson(405, false, 'Cette adresse accepte seulement la méthode POST.');
        }
        if ($utilisateur === null) {
            repondreJson(401, false, 'Connectez-vous pour commenter.');
        }
        if (!verifierCsrf()) {
            repondreJson(403, false, 'Formulaire expiré. Rechargez la page.');
        }

        $idRecette = lireEntier('recipe_id', 1, PHP_INT_MAX);
        $sujet = lireTexte('title');
        $texte = lireTexte('text');
        $erreurs = [];
        if ($idRecette === null) {
            $erreurs['recipe_id'] = 'Recette invalide.';
        }
        if (mb_strlen($sujet) > 120) {
            $erreurs['title'] = 'Le sujet ne doit pas dépasser 120 caractères.';
        }
        if (mb_strlen($texte) < 3 || mb_strlen($texte) > 500) {
            $erreurs['text'] = 'Le commentaire doit contenir entre 3 et 500 caractères.';
        }
        if ($erreurs !== []) {
            repondreJson(400, false, 'Données invalides.', [], $erreurs);
        }
        if (!(new RecipeManager($db))->exists($idRecette)) {
            repondreJson(404, false, 'Cette recette n\'existe pas.');
        }

        // anti-spam : 3 commentaires au maximum par tranche de 5 minutes et par utilisateur
        $commentaireManager = new CommentManager($db);
        if ($commentaireManager->countRecentByAuthor($utilisateur['id'], 5) >= 3) {
            repondreJson(429, false, 'Vous avez déjà publié 3 commentaires. Réessayez dans quelques minutes.');
        }

        $nouveau = $commentaireManager->add($utilisateur['id'], $idRecette, $sujet === '' ? null : $sujet, $texte);
        repondreJson(201, true, 'Votre commentaire est publié.', ['comment' => commentaireEnTableau($nouveau, $utilisateur, $estAdmin)]);

        // ===== POST /api/supprimer-commentaire : supprimer un commentaire =====
    case 'supprimer-commentaire':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            repondreJson(405, false, 'Cette adresse accepte seulement la méthode POST.');
        }
        if ($utilisateur === null) {
            repondreJson(401, false, 'Connectez-vous pour supprimer un commentaire.');
        }
        if (!verifierCsrf()) {
            repondreJson(403, false, 'Formulaire expiré. Rechargez la page.');
        }

        $idCommentaire = lireEntier('comment_id', 1, PHP_INT_MAX);
        if ($idCommentaire === null) {
            repondreJson(400, false, 'Données invalides.', [], ['comment_id' => 'Commentaire invalide.']);
        }
        $commentaireManager = new CommentManager($db);
        $commentaire = $commentaireManager->getById($idCommentaire);
        if ($commentaire === null) {
            repondreJson(404, false, 'Ce commentaire n\'existe pas.');
        }
        // seuls l'auteur du commentaire et un administrateur peuvent le supprimer
        if ($commentaire->getAuthorId() !== $utilisateur['id'] && !$estAdmin) {
            repondreJson(403, false, 'Vous ne pouvez pas supprimer le commentaire d\'un autre.');
        }
        // l'auteur supprime vraiment son propre commentaire ; l'administrateur, lui, le MASQUE seulement (il reste dans la base)
        if ($commentaire->getAuthorId() === $utilisateur['id']) {
            $commentaireManager->delete($idCommentaire);
            repondreJson(200, true, 'Le commentaire est supprimé.', ['id' => $idCommentaire]);
        }
        $commentaireManager->setStatus($idCommentaire, 'banni');
        // on renvoie le commentaire : l'administrateur le voit grisé, avec un bouton « Réafficher »
        repondreJson(200, true, 'Le commentaire est masqué.', [
            'id' => $idCommentaire,
            'comment' => commentaireEnTableau($commentaireManager->getById($idCommentaire), $utilisateur, $estAdmin),
        ]);

        // ===== POST /api/reafficher-commentaire : remettre un commentaire masqué (administrateur) =====
    case 'reafficher-commentaire':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            repondreJson(405, false, 'Cette adresse accepte seulement la méthode POST.');
        }
        if ($utilisateur === null) {
            repondreJson(401, false, 'Connectez-vous pour réafficher un commentaire.');
        }
        if (!$estAdmin) {
            repondreJson(403, false, 'Seul un administrateur peut réafficher un commentaire.');
        }
        if (!verifierCsrf()) {
            repondreJson(403, false, 'Formulaire expiré. Rechargez la page.');
        }

        $idCommentaire = lireEntier('comment_id', 1, PHP_INT_MAX);
        if ($idCommentaire === null) {
            repondreJson(400, false, 'Données invalides.', [], ['comment_id' => 'Commentaire invalide.']);
        }
        $commentaireManager = new CommentManager($db);
        if ($commentaireManager->getById($idCommentaire) === null) {
            repondreJson(404, false, 'Ce commentaire n\'existe pas.');
        }
        $commentaireManager->setStatus($idCommentaire, 'publié');
        repondreJson(200, true, 'Le commentaire est de nouveau visible.', [
            'id' => $idCommentaire,
            'comment' => commentaireEnTableau($commentaireManager->getById($idCommentaire), $utilisateur, $estAdmin),
        ]);

    default:
        repondreJson(404, false, 'Cette adresse n\'existe pas.');
}
