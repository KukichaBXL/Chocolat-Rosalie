<?php
// path: view/connection.php
$titre = 'Recettes de chocolatier';
require RACINE_PATH . '/view/inc/header.php';
?>


<section class="connection-hub reveal">
    <div class="img-connect">
        <img src="./assets/valeur-creativite.jpg" alt="">
    </div>
    <div class="form-connect">

        <h1>Mon compte</h1>
        <p>Nouveau client / Déja Client</p>

        <form action="" class="connect-form">
            <span>Veuillez renseigner votre email pour vous connecter ou créer un compte.</span>
            <div class="form-field">
                <input type="email" id="email" name="email" maxlength="120" autocomplete="email" placeholder="Email" />
                <span class="form-error" data-error-for="email"></span>
            </div>
            <div class="form-field">
                <input type="text" id="pwd" name="pwd" placeholder="Mot de passe ..." />
                <span class="form-error" data-error-for="Password"></span>
            </div>
            <br>
            <button id="btn-connect" class="btn-pill">Connexion</button>
        </form>
        <div class="line"></div>
        <br>

        <div class="txt-connect">
            <p>Accédez à votre Compte en 1 clic!</p>
            <p>Plus besoin de créer un énième compte et un mot de passe.</p>
            <p>Connectez-vous directement à votre compte en quelques secondes.</p>
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