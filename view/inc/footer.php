    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <img src="<?= RACINE_URL ?>assets/logo.png" alt="" class="footer-logo" width="183" height="183" loading="lazy" />

            <ul class="footer-nav">
                <li><a href="<?= RACINE_URL ?>">Accueil</a></li>
                <li><a href="<?= RACINE_URL ?>a-propos">À propos</a></li>
                <li><a href="<?= RACINE_URL ?>recettes">Recettes</a></li>
                <li><a href="<?= RACINE_URL ?>contact">Contact</a></li>
            </ul>

            <div class="footer-newsletter">
                <h2>Newsletter</h2>
                <p>Soyez les premiers à découvrir nos nouveautés, nos offres exclusives et nos promotions spéciales !</p>
                <!-- inscription à la newsletter : on tape l'adresse puis Entrée (ou un clic sur l'enveloppe) -->
                <form class="newsletter-field" method="post" action="<?= RACINE_URL ?>">
                    <input type="hidden" name="action" value="newsletter" />
                    <input type="hidden" name="retour" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>" />
                    <?= champCsrf() ?>
                    <input type="text" name="site_web" class="champ-piege" tabindex="-1" autocomplete="off" aria-hidden="true" />
                    <button type="submit" class="newsletter-envoi" aria-label="S'inscrire à la newsletter">
                        <img src="<?= RACINE_URL ?>assets/icon-mail.svg" alt="" width="24" height="24" />
                    </button>
                    <input type="email" name="newsletter-email" maxlength="120" required autocomplete="email" aria-label="Votre adresse email" />
                </form>
                <div class="socials">
                    <a href="#" aria-label="Instagram"><img src="<?= RACINE_URL ?>assets/icon-instagram.svg" alt="" width="28" height="28" /></a>
                    <a href="#" aria-label="Facebook"><img src="<?= RACINE_URL ?>assets/icon-facebook.svg" alt="" width="28" height="28" /></a>
                    <a href="#" aria-label="YouTube"><img src="<?= RACINE_URL ?>assets/icon-youtube.svg" alt="" width="28" height="28" /></a>
                    <a href="#" aria-label="TikTok"><img src="<?= RACINE_URL ?>assets/icon-tiktok.svg" alt="" width="28" height="28" /></a>
                    <a href="#" aria-label="X"><img src="<?= RACINE_URL ?>assets/icon-x.svg" alt="" width="28" height="28" /></a>
                </div>
            </div>
        </div>

        <p class="copyright">© 2026 Maison Rosalie - Tous droits réservés · <a href="<?= RACINE_URL ?>mentions-legales">Mentions légales</a></p>
    </footer>
    </body>

    </html>