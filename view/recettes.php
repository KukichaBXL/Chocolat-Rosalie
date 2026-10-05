<?php
// path: view/recettes.php
$titre = 'Nos recettes';
require RACINE_PATH.'/view/inc/header.php';

use model\manager\RecipeManager;
$recettes = (new RecipeManager($db))->getAll();

?>

<section class="container page-title">
    <h1>Nos recettes</h1>
    <p>Des recettes transmises depuis 1860, à découvrir pas à pas.</p>
</section>

<ul class="container recettes-grid">
    <?php foreach ($recettes as $recette) : ?>
        <li class="recette-card">
            <img src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($recette->getPhotoMain() ?? '') ?>" alt="" width="400" height="300" loading="lazy" />
            <div class="recette-card-body">
                <h2><?= htmlspecialchars($recette->getTitle()) ?></h2>
                <p>Difficulté : <?= htmlspecialchars($recette->getDifficulty()) ?></p>
                <a href="<?= RACINE_URL ?>recette/<?= htmlspecialchars($recette->getRecipesSlug()) ?>" class="btn-pill">Voir la recette</a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>