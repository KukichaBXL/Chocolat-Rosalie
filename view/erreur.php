<?php
// path: view/erreur.php
// affichée quand la base de données ne répond pas
$titre = 'Site indisponible';
require RACINE_PATH.'/view/inc/header.php';
?>

<section class="container page-title">
    <h1>Nos fourneaux refroidissent un instant</h1>
    <p>Le site est momentanément indisponible. Merci de réessayer dans quelques minutes.</p>
    <a href="<?= RACINE_URL ?>" class="btn-pill">Réessayer</a>
</section>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
