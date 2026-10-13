<?php
// path: view/a-propos.php
$titre = 'À propos';
$description = 'Depuis 1860, l\'histoire de la Maison Rosalie : une femme, un secret et une passion pour le chocolat, transmise de génération en génération.';
require RACINE_PATH . '/view/inc/header.php';
?>
<h1 class="about-title">
    Maison Rosalie
    <span>Depuis 1860</span>
</h1>

<section class="container histoire">
    <div class="line"></div>

    <article class="about-top reveal">


        <div class="about-top-txt">
            <p>
                <b class="about-txt-start">Il</b> était une fois une femme, un secret et une passion pour le chocolat.<br />
                En 1860, dans une petite maison au cœur de l’Europe, Rosalie confectionnait de délicates douceurs pour quelques
                familles privilégiées. Elle travaillait avec peu de choses : du cacao soigneusement choisi, du sucre, quelques
                gestes appris au fil des années… et surtout une intuition rare.
            </p>

            <div class="about-top-txt-citation">
                <p>Rosalie avait une conviction simple : le chocolat ne devait pas seulement se manger. Il devait se vivre.</p>
            </div>
            <br>

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

        <div class="about-top-img">
            <img
                class="histoire-image reveal"
                src="<?= RACINE_URL ?>assets/about.webp"
                alt="L’atelier historique de la Maison Rosalie, en noir et blanc"
                width="419" height="279" loading="lazy" />
        </div>

    </article>

    <div class="line"></div>

    <article aria-label="Chiffres clés du patrimoine" class="heritage-stats-section reveal">
        <div class="stats-grid">
            <!-- Card 1: Fondation -->
            <div class="stat-card" data-purpose="stat-card-foundation">
                <svg class="stat-icon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" viewbox="0 0 24 24">
                    <path d="M5 22h14"></path>
                    <path d="M5 2h14"></path>
                    <path d="M17 22v-4.172a2 2 0 0 0-.586-1.414L12 12l-4.414 4.414A2 2 0 0 0 7 17.828V22"></path>
                    <path d="M7 2v4.172a2 2 0 0 0 .586 1.414L12 12l4.414-4.414A2 2 0 0 0 17 6.172V2"></path>
                </svg>
                <div class="stat-number">1860</div>
                <div class="stat-subtitle">Fondation</div>
                <p class="stat-description">
                    Plus d’un siècle et demi de passion ininterrompue au service de l’excellence française et du respect de la tradition.
                </p>
            </div>
            <!-- Card 2: Savoir-faire -->
            <div class="stat-card" data-purpose="stat-card-savoir-faire">
                <svg class="stat-icon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" viewbox="0 0 24 24">
                    <circle cx="12" cy="12" r="9"></circle>
                    <path d="m14 10-4 4"></path>
                    <path d="m10 10 4 4"></path>
                    <path d="M12 3v2"></path>
                    <path d="M12 19v2"></path>
                </svg>
                <div class="stat-number">166 Ans</div>
                <div class="stat-subtitle">Savoir-faire</div>
                <p class="stat-description">
                    Des gestes inchangés : conchage lent, caramélisation au chaudron de cuivre et enrobages au millimètre.
                </p>
            </div>
            <!-- Card 3: Grand Cru -->
            <div class="stat-card" data-purpose="stat-card-grand-cru">
                <svg class="stat-icon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" viewbox="0 0 24 24">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    <path d="m9 12 2 2 4-4"></path>
                </svg>
                <div class="stat-number">100%</div>
                <div class="stat-subtitle">Grand Cru</div>
                <p class="stat-description">
                    Fèves d’exception sourcées éthiquement dans les terroirs les plus purs de Madagascar, d’Équateur et du Venezuela.
                </p>
            </div>
        </div>
    </article>

    <article class="about-bottom">

        <div class="about-bottom-img">
            <img
                class="histoire-image reveal"
                src="<?= RACINE_URL ?>assets/atelier.webp"
                alt="L’atelier historique de la Maison Rosalie, en noir et blanc"
                width="419" height="279" loading="lazy" />
        </div>

        <div class="line"></div>

        <div class="about-bottom-txt reveal">
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
                <b class="about-bottom-end reveal">Une histoire qui continue de fondre le temps.</b>
            </p>
        </div>

    </article>

</section>

<script src="<?= RACINE_URL ?>js/main.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>