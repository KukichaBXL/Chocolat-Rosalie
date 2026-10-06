<?php
// path: view/recettes.php
use model\manager\RecipeManager;

$titre = 'Nos recettes';
require RACINE_PATH.'/view/inc/header.php';

// les recettes lues dans la base : un tableau d'objets RecipeMapping
$recettes = (new RecipeManager($db))->getAll();
?>

<section class="main-recette">
    <div class="main-img">
    <img src="./assets/fourchette.webp" alt="recette">
    </div>
    <div class="main-txt">
        <h1>Nos recettes</h1>
        <p>Bienvenue dans notre espace dédié aux recettes ! 
        Vous retrouverez ici toutes nos recettes maison, inspirées de l’univers de Rosalie et préparées avec soin. Des recettes gourmandes, savoureuses et accessibles, pour découvrir nos produits autrement et partager un petit bout de notre savoir-faire.
        Que vous soyez à la recherche d’une idée pour vous régaler, d’une nouvelle recette à tester ou simplement curieux de découvrir nos créations, vous trouverez ici de quoi vous inspirer. Bonne découverte et surtout… </p>
        <br>
        <p class="regale">Régalez-vous !</p>
    </div>
    
</section>

<div class="carousel container">
    <button class="carousel-btn carousel-prev" aria-label="Recette précédente">&#8249;</button>

    <div class="carousel-viewport">
        <ul class="recettes-grid">
            <?php foreach ($recettes as $recette) : ?>
                <li class="recette-card">
                    <div class="recette-card-body" style="background-image: url('<?= RACINE_URL ?>assets/<?= htmlspecialchars($recette->getPhotoMain() ?? '') ?>')">
                        <div class="recette-card-body-txt">
                            <h2><?= htmlspecialchars($recette->getTitle()) ?></h2>
                            <p>Difficulté : <?= htmlspecialchars($recette->getDifficulty()) ?></p>
                            <a href="<?= RACINE_URL ?>recette/<?= htmlspecialchars($recette->getRecipesSlug()) ?>" class="btn-pill recette-btn">Voir la recette</a>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <button class="carousel-btn carousel-next" aria-label="Recette suivante">&#8250;</button>
</div>

<script src="./js/recettes.js"></script>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>