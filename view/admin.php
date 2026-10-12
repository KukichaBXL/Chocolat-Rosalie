<?php
// path: view/admin.php
// Variables : $onglet (l'onglet affiché) et $onglets (adresse => titre), préparés par PublicController.
// Chaque onglet a sa vue dans view/admin/ et a sa propre adresse : /admin/recettes, /admin/ajouter...
$titre = 'Administration';
$description = "Administration de Maison Rosalie : recettes, commentaires et messages.";
require RACINE_PATH . '/view/inc/header.php';
?>

<section class="container page-title">
    <h1>Administration</h1>
    <nav class="admin-onglets" aria-label="Administration">
        <?php foreach ($onglets as $adresse => $nom) : ?>
            <a href="<?= RACINE_URL ?>admin/<?= $adresse ?>" <?= $adresse === $onglet ? 'class="active" aria-current="page"' : '' ?>><?= $nom ?></a>
        <?php endforeach; ?>
    </nav>
</section>

<?php require RACINE_PATH . '/view/admin/' . $onglet . '.php'; ?>

<?php require RACINE_PATH . '/view/inc/footer.php'; ?>