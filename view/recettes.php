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

<section class="container page-title">
    <h1>Nos recettes</h1>
    <p>Des recettes transmises depuis 1860, à découvrir pas à pas.</p>
</section>

<ul class="container recettes-grid">
    <?php foreach ($recettes as $recette) : ?>
        <li class="recette-card">
            <img src="<?= RACINE_URL ?>assets/<?= $recette['photo_main'] ?>" alt="" width="400" height="300" loading="lazy" />
            <div class="recette-card-body">
                <h2><?= $recette['title'] ?></h2>
                <p>Difficulté : <?= $recette['difficulty'] ?></p>
                <a href="<?= RACINE_URL ?>recette/<?= $recette['recipes_slug'] ?>" class="btn-pill">Voir la recette</a>
            </div>
        </li>
    <?php endforeach; ?>
</ul>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
