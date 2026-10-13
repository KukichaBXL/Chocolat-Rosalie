<?php
// path: view/mentions-legales.php
$titre = 'Mentions légales';
$description = 'Mentions légales du site de la Maison Rosalie : éditeur, hébergement, données personnelles, cookies et propriété intellectuelle.';
require RACINE_PATH . '/view/inc/header.php';
?>
<section class="container mentions">
    <h1>Mentions légales</h1>
    <p class="mentions-note">Site réalisé dans le cadre d'un projet de formation : la Maison Rosalie et son contenu sont fictifs.</p>

    <h2>Éditeur du site</h2>
    <p>
        Maison Rosalie, chocolaterie belge (famille Moreau)<br />
        Rue de l'Atelier 12, 4000 Liège, Belgique (adresse fictive)<br />
        <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a>
    </p>

    <h2>Réalisation</h2>
    <p>Site conçu et développé par l'équipe de la formation Développeur web (Tiro-Tupea, maxim-pacini, Robin Craeye).</p>

    <h2>Hébergement</h2>
    <p>Site hébergé en local à des fins pédagogiques ; aucun hébergeur commercial n'est utilisé.</p>

    <h2>Données personnelles</h2>
    <p>
        Les données saisies (nom d'utilisateur, email, mot de passe, notes, commentaires, messages de contact) servent uniquement
        au fonctionnement du site. Le mot de passe est chiffré et n'est jamais lisible. Elles ne sont ni vendues ni transmises à des tiers.
        Vous pouvez demander l'accès, la correction ou la suppression de vos données à
        <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a>.
    </p>

    <h2>Cookies</h2>
    <p>Le site utilise uniquement un cookie de session, nécessaire pour rester connecté. Il ne sert à aucun suivi publicitaire.</p>

    <h2>Propriété intellectuelle</h2>
    <p>Les textes, photographies, logo et recettes ont été créés par l'équipe du projet. Toute reproduction sans autorisation est interdite.</p>
</section>
<script src="<?= RACINE_URL ?>js/main.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>
