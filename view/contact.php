<?php
// path: view/contact.php
$titre = 'Contact';
require RACINE_PATH.'/view/inc/header.php';
?>

<section class="container page-title">
    <h1>Contactez-nous</h1>
    <p>Un petit message suffit pour nous faire fondre de plaisir !</p>
</section>

<form class="container contact-form" method="post" novalidate>
    <div class="form-field">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" required maxlength="80" autocomplete="name" />
        <span class="form-error" data-error-for="nom"></span>
    </div>
    <div class="form-field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required maxlength="120" autocomplete="email" />
        <span class="form-error" data-error-for="email"></span>
    </div>
    <div class="form-field">
        <label for="sujet">Sujet</label>
        <input type="text" id="sujet" name="sujet" maxlength="120" />
        <span class="form-error" data-error-for="sujet"></span>
    </div>
    <div class="form-field message">
        <label for="message">Message</label>
        <textarea id="message" name="message" required maxlength="500"></textarea>
        <span class="form-error" data-error-for="message"></span>
    </div>

    <button type="submit" class="btn-pill">Envoyer le message</button>
    <p class="form-feedback"></p>
</form>

<section class="contact-infos">
    <div class="container contact-infos-inner">
        <div class="contact-texte">
            <h2>Retrouvez-nous</h2>
            <address>
                Rue du Sablon, 56<br />
                <a href="mailto:contact@maisonrosalie.be">contact@maisonrosalie.be</a>
            </address>
            <h2>Horaire</h2>
            <p>Lundi au Vendredi<br />De 9H à 18h</p>
        </div>

        <img class="contact-map" src="<?= RACINE_URL ?>assets/carte-sablon.jpg" alt="Plan du quartier du Sablon avec l'emplacement de la boutique" width="634" height="422" loading="lazy" />
    </div>
</section>

<script src="<?= RACINE_URL ?>js/contact.js" defer></script>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
