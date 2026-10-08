<?php
// path: view/recette.php
use model\manager\CommentManager;
use model\manager\IngredientManager;
use model\manager\RecipeManager;
use model\manager\StepManager;

// le slug vient de l'adresse : /recette/mousse-au-chocolat
$slug = $_GET['slug'] ?? '';
$recette = is_string($slug) ? (new RecipeManager($db))->getOneBySlug($slug) : null;

// recette inconnue : on affiche la page 404
if ($recette === null) {
    http_response_code(404);
    require RACINE_PATH.'/view/404.php';
    exit;
}

// les ingrédients et les étapes de CETTE recette
$ingredients = (new IngredientManager($db))->getByRecipe($recette->getId());
$etapes = (new StepManager($db))->getByRecipe($recette->getId());

// les commentaires de la recette, du plus récent au plus ancien, 10 à la fois
// "Voir plus d'avis" : ?avis=2 affiche 20 commentaires, ?avis=3 en affiche 30...
$pageAvis = isset($_GET['avis']) ? max(1, min(50, (int) $_GET['avis'])) : 1;
$commentaires = (new CommentManager($db))->getByRecipe($recette->getId(), CommentManager::PAGE_SIZE * $pageAvis, 0);

$titre = $recette->getTitle();
require RACINE_PATH.'/view/inc/header.php';
?>

<article class="container recette">
    <div class="recette-top">
        <div class="recette-fiche">
            <p class="recette-surtitre">Recette</p>
            <h1><?= htmlspecialchars($recette->getTitle()) ?></h1>
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
    <h2 style="margin-top:50px;" class="etape-titre">Étapes : </h2>
    <div class="recette-photo-step" >
        <?php foreach ($etapes as $etape) : ?>
            <?php if (!empty($etape->getStepPhoto())) : ?>
                <img src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($etape->getStepPhoto()) ?>" alt="Étape <?= $etape->getStepNumber() ?>" width="120" height="120" loading="lazy" />
            <?php endif; ?>
                        
        <?php endforeach; ?>
        
    </div>


    
    <section class="commentaires">
        <h2 id="titre-avis">Nos avis : </h2>

        <?php if ($commentaires['comments'] === []) : ?>
            <p>Aucun avis pour l'instant. Soyez le premier à donner le vôtre !</p>
        <?php else : ?>
            <!-- du plus récent au plus ancien, par tranches de 10 -->
            <ul>
                <?php foreach ($commentaires['comments'] as $commentaire) : ?>
                    <li class="commentaire">
                        <p class="commentaire-auteur">
                            <?= htmlspecialchars($commentaire->getUsername()) ?>
                            <span><?= date('d/m/Y', strtotime($commentaire->getCreatedAt())) ?></span>
                        </p>
                        <?php if ($commentaire->getCommentTitle() !== null) : ?>
                            <p><strong><?= htmlspecialchars($commentaire->getCommentTitle()) ?></strong></p>
                        <?php endif; ?>
                        <!-- htmlspecialchars : du code HTML ou JavaScript écrit dans un commentaire s'affiche comme du texte -->
                        <p><?= htmlspecialchars($commentaire->getCommentText()) ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($commentaires['hasMore']) : ?>
            <!-- "Voir plus" : un simple lien qui recharge la page avec 10 commentaires de plus -->
            <a href="?avis=<?= $pageAvis + 1 ?>#titre-avis" class="btn-pill">Voir plus d'avis</a>
        <?php endif; ?>
        <br>
        <h2>Ajouter un commentaire : </h2>
        <form class="commentaire-form" method="post">
            <div class="form-field">
                <input type="text" id="commentaire-sujet" name="sujet" maxlength="120" placeholder="Sujet (facultatif)"/>
            </div>
            <div class="form-field">
                <textarea id="commentaire-message" name="message" required minlength="3" maxlength="500" placeholder="Votre commentaire ..."></textarea>
            </div>
            <button type="submit" class="btn-pill">Publier le commentaire</button>
        </form>
    </section>
</article>



<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
