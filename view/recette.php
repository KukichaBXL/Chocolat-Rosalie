<?php
// path: view/recette.php
// Les données ($recette, $ingredients, $etapes, $commentaires) sont préparées par controller/PublicController.php
$titre = $recette->getTitle();
require RACINE_PATH . '/view/inc/header.php';
?>

<article class="container recette">
    <div class="recette-top">
        <div class="recette-fiche">
            <p class="recette-surtitre">Recette</p>
            <h1><?= htmlspecialchars($recette->getTitle()) ?></h1>
            <!-- la note moyenne (le vote se fait plus bas, avec les commentaires) -->
            <p class="note-moyenne">
                ★ <span id="note-moyenne"><?= $stats['average'] === null ? '–' : number_format($stats['average'], 1, ',') ?></span>/5
                · <span id="note-votes"><?= $stats['votes'] ?></span> avis
            </p>
            <p><?= htmlspecialchars($recette->getDescription() ?? '') ?></p>

            <p>
                Préparation : <?= $recette->getPrepareTime() ?> min ·
                Cuisson : <?= $recette->getCookTime() ?> min ·
                Difficulté : <?= htmlspecialchars($recette->getDifficulty()) ?>
            </p>

            <h2>Ingrédients — <?= $recette->getPortions() ?> personne(s)</h2>
            <?php if ($ingredients === []) : ?>
                <p>Les ingrédients de cette recette arrivent bientôt.</p>
            <?php else : ?>
                <ul>
                    <?php foreach ($ingredients as $ingredient) : ?>
                        <li><?= htmlspecialchars($ingredient->getQuantityLabel()) ?> <?= htmlspecialchars($ingredient->getName()) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <h2>Préparation</h2>
            <?php if ($etapes === []) : ?>
                <p>Les étapes de cette recette arrivent bientôt.</p>
            <?php else : ?>
                <ol>
                    <?php foreach ($etapes as $etape) : ?>
                        <li>
                            <strong><?= htmlspecialchars($etape->getStepTitle()) ?></strong><br />
                            <?= htmlspecialchars($etape->getDescription()) ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>

        <?php if (!empty($recette->getPhotoMain())) : ?>
            <img class="recette-photo" src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($recette->getPhotoMain()) ?>" alt="<?= htmlspecialchars($recette->getTitle()) ?>" width="510" height="854" />
        <?php endif; ?>
    </div>
    <h2 style="" class="etape-titre">Étapes : </h2>
    <div class="recette-photo-step">
        <?php foreach ($etapes as $etape) : ?>
            <?php if (!empty($etape->getStepPhoto())) : ?>
                <img src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($etape->getStepPhoto()) ?>" alt="Étape <?= $etape->getStepNumber() ?>" width="100" height="100" loading="lazy" />
            <?php endif; ?>

        <?php endforeach; ?>

    </div>



    <section class="commentaires" id="commentaires"
        data-recipe="<?= $recette->getId() ?>"
        data-csrf="<?= $_SESSION['csrf_token'] ?>"
        data-url="<?= RACINE_URL ?>api/">
        <h2 id="titre-avis">Nos avis : </h2>

        <p id="aucun-avis" <?= $commentaires['comments'] === [] ? '' : 'hidden' ?>>Aucun avis pour l'instant. Soyez le premier à donner le vôtre !</p>

        <!-- du plus récent au plus ancien, par tranches de 10 ; avis.js ajoute et retire des <li> sans recharger la page -->
        <ul id="liste-avis">
            <?php foreach ($commentaires['comments'] as $commentaire) : ?>
                <li class="commentaire" data-id="<?= $commentaire->getCommentId() ?>" data-author="<?= $commentaire->getAuthorId() ?>">
                    <p class="commentaire-auteur">
                        <?= htmlspecialchars($commentaire->getUsername()) ?>
                        <span><?= date('d/m/Y', strtotime($commentaire->getCreatedAt())) ?></span>
                        <!-- la note que cet auteur a donnée à la recette (vide s'il n'a pas noté) -->
                        <span class="commentaire-note"><?= $commentaire->getRate() === null ? '' : str_repeat('★', $commentaire->getRate()) . str_repeat('☆', 5 - $commentaire->getRate()) ?></span>
                    </p>
                    <?php if ($commentaire->getCommentTitle() !== null) : ?>
                        <p><strong><?= htmlspecialchars($commentaire->getCommentTitle()) ?></strong></p>
                    <?php endif; ?>
                    <!-- htmlspecialchars : du code HTML ou JavaScript écrit dans un commentaire s'affiche comme du texte -->
                    <p><?= htmlspecialchars($commentaire->getCommentText()) ?></p>
                    <?php if ($utilisateur !== null && ($commentaire->getAuthorId() === $utilisateur['id'] || $estAdmin)) : ?>
                        <button type="button" class="supprimer-avis"><?= $commentaire->getAuthorId() === $utilisateur['id'] ? 'Supprimer' : 'Masquer' ?></button>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($commentaires['hasMore']) : ?>
            <!-- sans JavaScript, ce lien recharge la page avec 10 commentaires de plus ; avis.js le remplace par un chargement sans rechargement -->
            <a href="?avis=<?= $pageAvis + 1 ?>#titre-avis" class="btn-pill" id="voir-plus">Voir plus d'avis</a>
        <?php endif; ?>

        <!-- les visiteurs ne voient ni le vote ni le formulaire : seulement une invitation à se connecter ou à s'inscrire -->
        <?php if ($utilisateur === null) : ?>
            <h2 id="votre-note">Donnez votre avis : </h2>
            <div class="invitation-avis">
                <p>Connectez-vous ou créez un compte pour noter cette recette et laisser un commentaire.</p>
                <p>
                    <button type="button" class="btn-pill" popovertarget="popup-connexion">Se connecter</button>
                    <a href="<?= RACINE_URL ?>connection" class="btn-pill">S'inscrire</a>
                </p>
            </div>
        <?php else : ?>
            <!-- votre note : une seule par personne, modifiable à tout moment, avec ou sans commentaire.
                 notation.js l'envoie à /api/noter sans recharger la page -->
            <h2 id="votre-note">Votre note : </h2>
            <div class="notation" id="notation"
                data-url="<?= RACINE_URL ?>api/noter"
                data-recipe="<?= $recette->getId() ?>"
                data-csrf="<?= $_SESSION['csrf_token'] ?>"
                data-user="<?= $utilisateur['id'] ?? 0 ?>"
                data-note="<?= $maNote ?? 0 ?>">
                <div class="etoiles" role="group" aria-label="Votre note">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <button type="button" class="etoile" data-valeur="<?= $i ?>" aria-label="<?= $i ?> sur 5">★</button>
                    <?php endfor; ?>
                </div>
                <p class="note-message" id="note-message" role="status">
                    <?= $maNote === null ? 'Cliquez sur une étoile pour noter. Le commentaire est facultatif.' : 'Votre note : ' . $maNote . '/5. Cliquez sur une autre étoile pour la modifier.' ?>
                </p>
            </div>

            <h2>Ajouter un commentaire (facultatif) : </h2>
            <form class="commentaire-form" id="form-avis" novalidate>
                <div class="form-field">
                    <input type="text" name="title" maxlength="120" placeholder="Sujet (facultatif)" />
                </div>
                <div class="form-field">
                    <textarea name="text" required maxlength="500" placeholder="Votre commentaire ..."></textarea>
                </div>
                <p class="form-feedback" id="avis-message" role="status"></p>
                <button type="submit" class="btn-pill">Publier le commentaire</button>
            </form>
        <?php endif; ?>
    </section>
</article>



<script src="<?= RACINE_URL ?>js/notation.js"></script>
<script src="<?= RACINE_URL ?>js/avis.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>