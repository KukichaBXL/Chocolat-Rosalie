<?php
// path: view/recettes.php
$titre = 'Nos recettes';
require RACINE_PATH.'/view/inc/header.php';

// recettes écrites en dur pour l'instant, avec les noms des colonnes de la table `recipes` :
// ce tableau sera remplacé par les recettes lues dans la base
$recettes = [
    ['title' => 'Fondant chocolat de Rosalie', 'recipes_slug' => 'fondant-chocolat-de-rosalie', 'photo_main' => 'recette-fondant.jpg', 'difficulty' => 'facile'],
    ['title' => 'Mousse au chocolat noir', 'recipes_slug' => 'mousse-au-chocolat-noir', 'photo_main' => 'recette-mousse.jpg', 'difficulty' => 'facile'],
    ['title' => 'Chocolat chaud épicé', 'recipes_slug' => 'chocolat-chaud-epice', 'photo_main' => 'recette-chocolat-chaud.jpg', 'difficulty' => 'facile'],
    ['title' => 'Entremets praliné', 'recipes_slug' => 'entremets-praline', 'photo_main' => 'recette-entremets.jpg', 'difficulty' => 'difficile'],
    ['title' => 'Glace au chocolat artisanale', 'recipes_slug' => 'glace-au-chocolat-artisanale', 'photo_main' => 'recette-glace.jpg', 'difficulty' => 'moyen'],
];
?>

<section class="main-recette">
    <div class="main-img">
    <img src="./assets/6a100a69e55b9f863016f75d8723207f49f807d1.jpg" alt="recette">
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

<ul class="container recettes-grid">
    <?php foreach ($recettes as $recette) : ?>
        <li class="recette-card"  >
            <div class="recette-card-body"  style="background-image: url('<?= RACINE_URL ?>assets/<?= $recette['photo_main'] ?>">
                <div class="recette-card-body-txt">
                    <h2><?= $recette['title'] ?></h2>
                    <p>Difficulté : <?= $recette['difficulty'] ?></p>
                    <a href="<?= RACINE_URL ?>recette/<?= $recette['recipes_slug'] ?>" class="btn-pill recette-btn">Voir la recette</a>
                </div>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>