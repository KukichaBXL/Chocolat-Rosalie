<?php
// path: view/desinscription.php
// $abonne est préparé par PublicController : l'abonné si le lien est valide, sinon null.
$titre = 'Désinscription';
$description = 'Se désinscrire de la newsletter de la Maison Rosalie.';
require RACINE_PATH . '/view/inc/header.php';
?>
<section class="container mentions">
    <h1>Newsletter</h1>
    <?php if ($abonne !== null) : ?>
        <p class="mentions-note">Voulez-vous vraiment ne plus recevoir la newsletter à l'adresse <strong><?= htmlspecialchars($abonne['email']) ?></strong> ?</p>
        <form method="post" action="<?= RACINE_URL ?>" class="desinscription">
            <input type="hidden" name="action" value="desinscription" />
            <input type="hidden" name="id" value="<?= (int) $abonne['id'] ?>" />
            <input type="hidden" name="jeton" value="<?= htmlspecialchars(jetonDesinscription((int) $abonne['id'], $abonne['email'])) ?>" />
            <?= champCsrf() ?>
            <button type="submit" class="btn-pill">Me désinscrire</button>
        </form>
    <?php else : ?>
        <p class="mentions-note">Ce lien de désinscription n'est pas valide, ou vous êtes déjà désinscrit(e).</p>
    <?php endif; ?>
</section>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>