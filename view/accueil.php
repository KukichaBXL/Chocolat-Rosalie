<?php
// path: view/accueil.php
$titre = 'Recettes de chocolatier';
$description = 'Maison Rosalie, chocolaterie belge : découvrez nos recettes au chocolat pas à pas et le top 3 des recettes les mieux notées.';
require RACINE_PATH . '/view/inc/header.php';
?>

<section class="hero reveal">
    <img
        src="<?= RACINE_URL ?>assets/hero-accueil.webp"
        alt="Une chocolatière garnit des bonbons au chocolat à la poche à douille dans l'atelier"
        width="1280" height="573" />
</section>

<section class="intro container reveal">
    <h1 class="intro-title">
        Un héritage chocolatier façonné depuis <span class="accent">166</span> ans
    </h1>
    <p>Des créations d'exception pour des moments précieux</p>
</section>

<section class="values container reveal" aria-label="Nos valeurs">
    <ul class="value-list">
        <li>
            <figure>
                <img src="<?= RACINE_URL ?>assets/valeur-savoir-faire.jpg" alt="Chocolat fondu versé à la louche" width="226" height="325" loading="lazy" />
                <figcaption>Savoir faire</figcaption>
            </figure>
        </li>
        <li>
            <figure>
                <img src="<?= RACINE_URL ?>assets/valeur-creativite.webp" alt="Coffret de chocolats Maison Rosalie ouvert" width="226" height="325" loading="lazy" />
                <figcaption>Créativité</figcaption>
            </figure>
        </li>
        <li>
            <figure>
                <img src="<?= RACINE_URL ?>assets/valeur-qualite.webp" alt="Truffes au chocolat dans une boîte en métal doré" width="226" height="325" loading="lazy" />
                <figcaption>Qualité</figcaption>
            </figure>
        </li>
        <li>
            <figure>
                <img src="<?= RACINE_URL ?>assets/valeur-passion.webp" alt="Un chocolatier démoule une plaque de bonbons au chocolat" width="226" height="325" loading="lazy" />
                <figcaption>Passion</figcaption>
            </figure>
        </li>
    </ul>
</section>

<?php if ($top3 !== []) : ?>
    <section class="top3 container reveal" aria-labelledby="titre-top3">
        <h2 id="titre-top3">Les 3 recettes les mieux notées</h2>
        <ol class="top3-liste">
            <?php foreach ($top3 as $rang => $recette) : ?>
                <li class="top3-carte">
                    <span class="top3-rang"><?= $rang + 1 ?></span>
                    <img src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($recette->getPhotoMain() ?? '') ?>" alt="<?= htmlspecialchars($recette->getTitle()) ?>" width="300" height="200" loading="lazy" />
                    <h3><?= htmlspecialchars($recette->getTitle()) ?></h3>
                    <p class="note-etoiles" aria-label="Note moyenne : <?= $recette->getAverage() ?? 'aucune' ?> sur 5">
                        <?= etoiles($recette->getAverage()) ?>
                        <?= $recette->getVotes() === 0 ? 'Pas encore noté' : number_format($recette->getAverage(), 1, ',') . ' · ' . $recette->getVotes() . ' avis' ?>
                    </p>
                    <p><?= $recette->getTotalTime() ?> min · <?= htmlspecialchars($recette->getDifficulty()) ?></p>
                    <a href="<?= RACINE_URL ?>recette/<?= htmlspecialchars($recette->getRecipesSlug()) ?>" class="btn-pill">Voir la recette</a>
                </li>
            <?php endforeach; ?>
        </ol>
    </section>
<?php endif; ?>

<div class="cta-wrap container">
    <a href="<?= RACINE_URL ?>recettes" class="btn-pill">Découvrez nos recettes</a>
</div>

<script src="<?= RACINE_URL ?>js/main.js"></script>

<?php require RACINE_PATH . '/view/inc/footer.php'; ?>