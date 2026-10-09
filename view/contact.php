<?php
// path: view/contact.php
$titre = 'Contact';
require RACINE_PATH . '/view/inc/header.php';
?>

<section class="container page-title reveal">
    <h1>Contactez-nous</h1>
    <p>Un petit message suffit pour nous faire fondre de plaisir !</p>
</section>

<form class="container contact-form reveal" method="post" novalidate>
    <input type="hidden" name="action" value="contact" />
    <?= champCsrf() ?>
    <!-- champ piège anti-spam : invisible pour un humain, les robots le remplissent (voir .champ-piege dans le CSS) -->
    <input type="text" name="site_web" class="champ-piege" tabindex="-1" autocomplete="off" aria-hidden="true" />

    <div class="form-field">
        <input type="text" id="nom" name="nom" required maxlength="80" autocomplete="name" placeholder="Nom & prénom" value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" />
        <span class="form-error" data-error-for="nom"><?= $erreurs['nom'] ?? '' ?></span>
    </div>
    <div class="form-field">
        <input type="email" id="email" name="email" required maxlength="120" autocomplete="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
        <span class="form-error" data-error-for="email"><?= $erreurs['email'] ?? '' ?></span>
    </div>
    <div class="form-field">
        <input type="text" id="sujet" name="sujet" maxlength="120" placeholder="Sujet" value="<?= htmlspecialchars($_POST['sujet'] ?? '') ?>" />
        <span class="form-error" data-error-for="sujet"><?= $erreurs['sujet'] ?? '' ?></span>
    </div>
    <div class="form-field message">
        <textarea id="message" name="message" required maxlength="500" placeholder="Votre message ..."><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
        <span class="form-error" data-error-for="message"><?= $erreurs['message'] ?? '' ?></span>
    </div>

    <button type="submit" class="btn-pill">Envoyer le message</button>
    <p class="form-feedback"><?= $erreurs['general'] ?? '' ?></p>
</form>



<section class="contact-infos">
    <div class="contact-infos-inner">
        <div class="contact-texte">
            <h2>Retrouvez-nous</h2>
            <address>
                Rue du Sablon, 56<br />
                <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a>
            </address>
            <br>
            <h2>Horaire</h2>
            <p>Lundi au Vendredi<br />De 9H à 18h</p>
        </div>
    </div>
    <div class="map-loc" id="map"></div>
</section>


<script src="<?= RACINE_URL ?>js/contact.js" defer></script>
<script src="<?= RACINE_URL ?>js/main.js"></script>

<?php require RACINE_PATH . '/view/inc/footer.php'; ?>