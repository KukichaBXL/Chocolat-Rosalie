<?php
// path: view/404.php
$titre = 'Page introuvable';
require RACINE_PATH.'/view/inc/header.php';
?>

<section class="container page-title">
    <p class="grand-chiffre">404</p>
    <h1>Cette page n'existe pas</h1>
    <p>La page que vous cherchez a peut-être été déplacée ou n'a jamais existé.</p>
    <a href="<?= RACINE_URL ?>" class="btn-pill">Retour à l'accueil</a>
</section>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
