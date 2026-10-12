<?php
// path: view/connection.php
$titre = 'Inscription';
require RACINE_PATH . '/view/inc/header.php';
?>


<section class="connection-hub reveal">
    <div class="img-connect">
        <img src="<?= RACINE_URL ?>assets/valeur-creativite.jpg" alt="">
    </div>
    <div class="form-connect">

        <h1>Créer un compte</h1>
        <p>Nouveau client</p>

        <form action="" method="post" class="connect-form" id="form-inscription" novalidate>
            <input type="hidden" name="action" value="inscription" />
            <?= champCsrf() ?>
            <span>Choisissez un nom d'utilisateur, votre email et un mot de passe pour créer votre compte.</span>
            <div class="form-field">
                <input type="text" id="username" name="username" required maxlength="30" autocomplete="username" placeholder="Nom d'utilisateur" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />
                <span class="form-error" data-error-for="username"><?= $erreurs['username'] ?? '' ?></span>
            </div>
            <div class="form-field">
                <input type="email" id="email" name="email" required maxlength="120" autocomplete="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
                <span class="form-error" data-error-for="email"><?= $erreurs['email'] ?? '' ?></span>
            </div>
            <div class="form-field">
                <input type="password" id="pwd" name="pwd" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Mot de passe (8 caractères min.)" />
                <span class="form-error" data-error-for="pwd"><?= $erreurs['pwd'] ?? '' ?></span>
            </div>
            <div class="form-field">
                <input type="password" id="pwd-confirm" name="pwd_confirm" required minlength="8" maxlength="72" autocomplete="new-password" placeholder="Confirmer le mot de passe" />
                <span class="form-error" data-error-for="pwd-confirm"><?= $erreurs['pwd_confirm'] ?? '' ?></span>
            </div>
            <p>8 caractères minimum, avec une majuscule, une minuscule et un chiffre.</p>
            <br>
            <button type="submit" id="btn-connect" class="btn-pill">Créer mon compte</button>
        </form>

        <p class="deja-client">
            Déjà client ?
            <button type="button" popovertarget="popup-connexion">Se connecter</button>
        </p>

        <div class="line"></div>
        <br>

        <div class="txt-connect">
            <p>Inscrivez-vous en 1 clic !</p>
            <p>Plus besoin de créer un énième mot de passe.</p>
            <p>Utilisez directement l'un de vos comptes ci-dessous.</p>
        </div>

        <div class="logo-connect">
            <a href="#" aria-label="Facebook"><img src="<?= RACINE_URL ?>assets/icon-facebook.svg" alt="" width="28" height="28" /></a>
            <a href="#" aria-label="X"><img src="<?= RACINE_URL ?>assets/icon-x.svg" alt="" width="28" height="28" /></a>
            <a href="#" aria-label="Google"><img src="<?= RACINE_URL ?>assets/icon-mail.svg" alt="" width="28" height="28" /></a>
        </div>

    </div>
</section>

<script src="<?= RACINE_URL ?>js/main.js"></script>
<script src="<?= RACINE_URL ?>js/connection.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>