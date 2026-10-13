<?php
// path: view/verification.php
// $verifie (bool) est préparé par PublicController : true si le lien de confirmation est valide.
$titre = 'Confirmation de l\'adresse e-mail';
$description = 'Confirmation de votre adresse e-mail Maison Rosalie.';
require RACINE_PATH . '/view/inc/header.php';
?>
<section class="container mentions page-courte">
    <h1>Confirmation de votre e-mail</h1>
    <?php if ($verifie) : ?>
        <p class="mentions-note">Merci, votre adresse e-mail est confirmée : votre compte est actif. Vous pouvez maintenant vous connecter.</p>
        <p class="mentions-note"><button type="button" class="btn-pill" popovertarget="popup-connexion">Se connecter</button></p>
    <?php else : ?>
        <p class="mentions-note">Ce lien de confirmation n'est pas valide.</p>
    <?php endif; ?>
</section>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>