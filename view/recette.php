<?php
// path: view/recette.php
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


    <!-- les avis branchés sur la base (à faire)-->
    <section class="commentaires">
        <h2>Nos avis : </h2>

        <ul>
            <?php foreach ($comments as $comment) :?>
            <li class="commentaire">
                <p class="commentaire-auteur"><?= htmlspecialchars($comment->get) ?>  ?></p>
                <p>Testé ce week-end, un vrai régal, merci pour la recette !</p>
            </li>
            <li class="commentaire">
                <p class="commentaire-auteur">Thomas D. <span>3 février 2026</span></p>
                <p>Le cœur coulant est parfait, j'ai suivi les temps à la lettre.</p>
            </li>
            <?php endforeach; ?>
        </ul>
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