<?php
// path: view/accueil.php
$titre = 'Recettes de chocolatier';
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
                <img src="<?= RACINE_URL ?>assets/valeur-creativite.jpg" alt="Coffret de chocolats Maison Rosalie ouvert" width="226" height="325" loading="lazy" />
                <figcaption>Créativité</figcaption>
            </figure>
        </li>
        <li>
            <figure>
                <img src="<?= RACINE_URL ?>assets/valeur-qualite.jpg" alt="Truffes au chocolat dans une boîte en métal doré" width="226" height="325" loading="lazy" />
                <figcaption>Qualité</figcaption>
            </figure>
        </li>
        <li>
            <figure>
                <img src="<?= RACINE_URL ?>assets/valeur-passion.jpg" alt="Un chocolatier démoule une plaque de bonbons au chocolat" width="226" height="325" loading="lazy" />
                <figcaption>Passion</figcaption>
            </figure>
        </li>
    </ul>
</section>

<div class="cta-wrap container">
    <a href="<?= RACINE_URL ?>recettes" class="btn-pill">Découvrez nos recettes</a>
</div>


<script src="<?= RACINE_URL ?>js/main.js"></script>

<?php require RACINE_PATH . '/view/inc/footer.php'; ?>