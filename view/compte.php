<?php
// path: view/compte.php
$titre = 'Mon compte';
$description = "Votre compte Maison Rosalie : modifier votre mot de passe.";
require RACINE_PATH . '/view/inc/header.php';
?>

<section class="container page-title reveal">
    <h1>Mon compte</h1>
    <p><?= htmlspecialchars($monCompte->getUsername()) ?> · <?= htmlspecialchars($monCompte->getEmail()) ?></p>
</section>

<form class="container contact-form reveal" method="post" novalidate>
    <h2>Changer mon mot de passe</h2>
    <input type="hidden" name="action" value="changer-mot-de-passe" />
    <?= champCsrf() ?>

    <div class="form-field">
        <input type="password" id="ancien_pwd" name="ancien_pwd" required autocomplete="current-password" placeholder="Mot de passe actuel" />
        <span class="form-error"><?= $erreurs['ancien_pwd'] ?? '' ?></span>
    </div>
    <div class="form-field">
        <input type="password" id="nouveau_pwd" name="nouveau_pwd" required autocomplete="new-password" placeholder="Nouveau mot de passe" />
        <span class="form-error"><?= $erreurs['nouveau_pwd'] ?? '' ?></span>
    </div>
    <div class="form-field">
        <input type="password" id="confirmation_pwd" name="confirmation_pwd" required autocomplete="new-password" placeholder="Confirmer le nouveau mot de passe" />
        <span class="form-error"><?= $erreurs['confirmation_pwd'] ?? '' ?></span>
    </div>

    <button type="submit" class="btn-pill">Enregistrer</button>
    <p class="form-feedback"><?= $erreurs['general'] ?? '' ?></p>
</form>
<script src="<?= RACINE_URL ?>js/main.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>