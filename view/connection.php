<?php
// path: view/connection.php
$titre = 'Inscription';
require RACINE_PATH . '/view/inc/header.php';
?>


<section class="connection-hub reveal">
    <div class="img-connect">
        <img src="./assets/valeur-creativite.jpg" alt="">
    </div>
    <div class="form-connect">

        <h1>Créer un compte</h1>
        <p>Nouveau client</p>

        <form action="" method="post" class="connect-form" novalidate>
            <span>Choisissez votre email et un mot de passe pour créer votre compte.</span>
            <div class="form-field">
                <input type="email" id="email" name="email" required maxlength="120" autocomplete="email" placeholder="Email" />
                <span class="form-error" data-error-for="email"></span>
            </div>
            <div class="form-field">
                <input type="password" id="pwd" name="pwd" required minlength="8" autocomplete="new-password" placeholder="Mot de passe (8 caractères min.)" />
                <span class="form-error" data-error-for="pwd"></span>
            </div>
            <div class="form-field">
                <input type="password" id="pwd-confirm" name="pwd_confirm" required minlength="8" autocomplete="new-password" placeholder="Confirmer le mot de passe" />
                <span class="form-error" data-error-for="pwd-confirm"></span>
            </div>
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

<script src="./js/main.js"></script>
<script src="./js/connection.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>
