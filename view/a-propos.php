<?php
// path: view/a-propos.php
$titre = 'À propos';
require RACINE_PATH . '/view/inc/header.php';
?>

<article class="container histoire">
    <div class="reveal">
        <h1>
            Maison Rosalie
            <span>Depuis 1860</span>
        </h1>

        <p>
            Il était une fois une femme, un secret et une passion pour le chocolat.<br />
            En 1860, dans une petite maison au cœur de l’Europe, Rosalie confectionnait de délicates douceurs pour quelques
            familles privilégiées. Elle travaillait avec peu de choses : du cacao soigneusement choisi, du sucre, quelques
            gestes appris au fil des années… et surtout une intuition rare.
        </p>

        <p>Rosalie avait une conviction simple : le chocolat ne devait pas seulement se manger. Il devait se vivre.</p>

        <p>
            Elle passait des heures à chercher l’équilibre parfait entre l’intensité du cacao, la finesse des textures et la
            délicatesse des parfums. Ses créations devinrent peu à peu une signature. On venait chez Rosalie pour offrir un
            chocolat, mais surtout pour offrir une émotion.<br />
            De génération en génération, son savoir-faire se transmit.<br />
            Les carnets de recettes furent conservés, les gestes furent répétés, perfectionnés, parfois réinventés. Mais une
            chose ne changea jamais : cette exigence presque obsessionnelle pour la beauté, la précision et le goût.
        </p>

        <p>Ainsi naquit Maison Rosalie.</p>
    </div>


    <img
        class="histoire-image reveal"
        src="<?= RACINE_URL ?>assets/atelier.webp"
        alt="L’atelier historique de la Maison Rosalie, en noir et blanc"
        width="419" height="279" loading="lazy" />

    <div class="reveal">
        <h2>166 ans de savoir-faire</h2>
        <p>
            Aujourd’hui, 166 ans plus tard, le monde a changé. Les tendances passent, les goûts évoluent, les époques se
            succèdent.
        </p>

        <p>Mais certaines choses traversent le temps.</p>

        <p>
            Chez Maison Rosalie, chaque chocolat est pensé comme un petit objet précieux. Une ganache fondante, un praliné
            délicat, une couverture de cacao brillante : chaque création rend hommage au geste originel de Rosalie.
        </p>

        <p>
            Nous ne cherchons pas à reproduire le passé. Nous cherchons à en préserver l’esprit.<br />
            Car le véritable luxe n’est pas dans l’excès.<br />
            Il est dans le temps que l’on prend.<br />
            Dans la rareté d’un ingrédient.<br />
            Dans la précision d’un geste.<br />
            Dans la sensation d’ouvrir une boîte et de découvrir quelque chose qui semble avoir été créé spécialement pour soi.
        </p>

        <p>
            La signature Rosalie<br />
            Depuis 1860, Maison Rosalie cultive une certaine idée du chocolat :<br />
            rare, élégant, généreux et profondément émotionnel.<br />
            Chaque création porte l’héritage de Rosalie, mais aussi une invitation à écrire la suite de son histoire.
        </p>

        <p>
            Parce qu’un grand chocolat ne se contente pas de fondre en bouche.<br />
            Il laisse un souvenir.<br />
            Maison Rosalie<br />
            166 ans de savoir-faire.<br />
            Une histoire qui continue de fondre le temps.
        </p>
    </div>

</article>

<script src="<?= RACINE_URL ?>js/main.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>