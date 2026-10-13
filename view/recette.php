<?php
// path: view/recette.php
// Les données ($recette, $ingredients, $etapes, $commentaires) sont préparées par controller/PublicController.php
$titre = $recette->getTitle();
require RACINE_PATH . '/view/inc/header.php';
?>

<article class="container recette">
    <div class="recette-top">
        <div class="recette-fiche">
            <h1><span>Recette</span> <?= htmlspecialchars($recette->getTitle()) ?> <span>de Rosalie</span></h1>

            <!-- une ligne de repères : catégorie, note moyenne, durées, difficulté -->
            <ul class="recette-infos">
                <?php if ($recette->getCategoryTitle() !== null) : ?>
                    <?php
                    // petite icône (Font Awesome) selon la catégorie
                    $icones = ['Gâteaux' => 'fa-birthday-cake', 'Mousses' => 'fa-cloud', 'Boissons' => 'fa-coffee', 'Glacé' => 'fa-snowflake-o'];
                    $icone = $icones[$recette->getCategoryTitle()] ?? 'fa-cutlery';
                    ?>
                    <li class="recette-categorie"><i class="fa <?= $icone ?>" aria-hidden="true"></i> <?= htmlspecialchars($recette->getCategoryTitle()) ?></li>
                <?php endif; ?>
                <!-- la note moyenne (le vote se fait plus bas, avec les commentaires) -->
                <li class="note-moyenne">★ <span id="note-moyenne"><?= $stats['average'] === null ? '–' : number_format($stats['average'], 1, ',') ?></span>/5 · <span id="note-votes"><?= $stats['votes'] ?></span> avis</li>
                <li><i class="fa fa-clock-o" aria-hidden="true"></i> <?= $recette->getPrepareTime() ?> min</li>
                <li><i class="fa fa-fire" aria-hidden="true"></i> <?= $recette->getCookTime() ?> min</li>
                <li><i class="fa fa-signal" aria-hidden="true"></i> <?= htmlspecialchars($recette->getDifficulty()) ?></li>
            </ul>

            <?php if (!empty($recette->getDescription())) : ?>
                <p class="recette-description"><?= htmlspecialchars($recette->getDescription()) ?></p>
            <?php endif; ?>

            <?php $portionsBase = max(1, (int) $recette->getPortions()); ?>
            <section class="ingredients-carte" aria-labelledby="titre-ingredients">
                <h2 id="titre-ingredients">Ingrédients</h2>
                <!-- sans JavaScript : un simple texte ; portions.js le remplace par le sélecteur + / − -->
                <p class="portions-fixe" id="portions-fixe">Pour <?= $portionsBase ?> personne<?= $portionsBase > 1 ? 's' : '' ?></p>
                <div class="portions" id="portions" data-base="<?= $portionsBase ?>" hidden>
                    <span>Pour</span>
                    <button type="button" id="portions-moins" aria-label="Une personne de moins">&minus;</button>
                    <output id="portions-nb" aria-live="polite"><?= $portionsBase ?></output>
                    <button type="button" id="portions-plus" aria-label="Une personne de plus">+</button>
                    <span id="portions-mot">personnes</span>
                </div>
                <?php if ($ingredients === []) : ?>
                    <p>Les ingrédients de cette recette arrivent bientôt.</p>
                <?php else : ?>
                    <ul class="ingredients">
                        <?php foreach ($ingredients as $ingredient) : ?>
                            <?php
                            // "sel 1" devient "1 pincée" ; une pincée ne change pas avec le nombre de personnes
                            $quantiteBrute = $ingredient->getQuantity() === null ? null : (float) $ingredient->getQuantity();
                            [$quantite, $unite, $change] = ingredientLogique($ingredient->getName(), $quantiteBrute, $ingredient->getUnit());
                            ?>
                            <li>
                                <span class="ing-nom"><?= htmlspecialchars($ingredient->getName()) ?></span>
                                <span class="ing-pointille" aria-hidden="true"></span>
                                <?php if ($quantite === null) : ?>
                                    <span class="ing-texte"><?= htmlspecialchars((string) $unite) ?></span>
                                <?php elseif (!$change) : ?>
                                    <span class="ing-texte"><?= htmlspecialchars(quantiteTexte($quantite, $unite)) ?></span>
                                <?php else : ?>
                                    <span class="ing-qte" data-qte="<?= $quantite ?>" data-unite="<?= htmlspecialchars((string) $unite) ?>"><?= htmlspecialchars(quantiteTexte($quantite, $unite)) ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>

            <h2 class="preparation-titre">Préparation</h2>
            <?php if ($etapes === []) : ?>
                <p>Les étapes de cette recette arrivent bientôt.</p>
            <?php else : ?>
                <ol class="preparation">
                    <?php foreach ($etapes as $etape) : ?>
                        <li>
                            <strong><?= htmlspecialchars($etape->getStepTitle()) ?></strong>
                            <!-- {chocolat noir} dans le texte devient "200 g de chocolat noir" et suit le nombre de personnes -->
                            <p><?= texteEtape($etape->getDescription(), $ingredients) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>

            <!-- le logo de la maison, en bas de la fiche -->
            <p class="fiche-logo">
                <img src="<?= RACINE_URL ?>assets/logo.png" alt="" width="36" height="36" />
            </p>
        </div>

        <?php if (!empty($recette->getPhotoMain())) : ?>
            <img class="recette-photo" src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($recette->getPhotoMain()) ?>" alt="<?= htmlspecialchars($recette->getTitle()) ?>" width="510" height="854" />
        <?php endif; ?>
    </div>
    <h2 style="" class="etape-titre">Étapes : </h2>
    <div class="recette-photo-step">
        <?php foreach ($etapes as $etape) : ?>
            <?php if (!empty($etape->getStepPhoto())) : ?>
                <img src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($etape->getStepPhoto()) ?>" alt="Étape <?= $etape->getStepNumber() ?>" width="100" height="100" loading="lazy" />
            <?php endif; ?>

        <?php endforeach; ?>

    </div>



    <!-- sous les photos des étapes : à gauche les autres recettes, à droite les avis (deux carrousels) -->
    <div class="recette-bas">
        <section class="divers-recettes carrousel" aria-label="Nos divers recettes">
            <h2>Nos divers recettes</h2>
            <div class="carrousel-corps">
                <button type="button" class="carrousel-fleche carrousel-prec" aria-label="Recette précédente">&#8249;</button>
                <ul class="carrousel-piste">
                    <?php foreach ($recettesMenu as $autre) : ?>
                        <?php if ($autre->getId() !== $recette->getId()) : ?>
                            <li class="carrousel-item">
                                <a href="<?= RACINE_URL ?>recette/<?= htmlspecialchars($autre->getRecipesSlug()) ?>" class="autre-recette">
                                    <?php if (!empty($autre->getPhotoMain())) : ?>
                                        <img src="<?= RACINE_URL ?>assets/<?= htmlspecialchars($autre->getPhotoMain()) ?>" alt="" width="200" height="150" loading="lazy" />
                                    <?php endif; ?>
                                    <span class="autre-titre"><?= htmlspecialchars($autre->getTitle()) ?></span>
                                    <span class="note-etoiles" aria-label="Note moyenne : <?= $autre->getAverage() ?? 'aucune' ?> sur 5"><?= etoiles($autre->getAverage()) ?></span>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="carrousel-fleche carrousel-suiv" aria-label="Recette suivante">&#8250;</button>
            </div>
        </section>

        <section class="avis-bloc carrousel" id="commentaires"
            data-recipe="<?= $recette->getId() ?>"
            data-csrf="<?= $_SESSION['csrf_token'] ?>"
            data-url="<?= RACINE_URL ?>api/">
            <h2 id="titre-avis">Nos avis :</h2>

            <p id="aucun-avis" <?= $commentaires['comments'] === [] ? '' : 'hidden' ?>>Aucun avis pour l'instant. Soyez le premier à donner le vôtre !</p>

            <div class="carrousel-corps">
                <button type="button" class="carrousel-fleche carrousel-prec" aria-label="Avis précédent">&#8249;</button>
                <!-- du plus récent au plus ancien ; avis.js ajoute et retire des <li> sans recharger la page -->
                <ul id="liste-avis" class="carrousel-piste">
                    <?php foreach ($commentaires['comments'] as $commentaire) : ?>
                        <li class="commentaire<?= $commentaire->getCommentStatus() === 'banni' ? ' commentaire-masque' : '' ?>" data-id="<?= $commentaire->getCommentId() ?>" data-author="<?= $commentaire->getAuthorId() ?>">
                            <p class="commentaire-auteur">
                                <?= htmlspecialchars($commentaire->getUsername()) ?>
                                <span><?= date('d/m/Y', strtotime($commentaire->getCreatedAt())) ?></span>
                                <!-- la note que cet auteur a donnée à la recette (vide s'il n'a pas noté) -->
                                <span class="commentaire-note"><?= $commentaire->getRate() === null ? '' : str_repeat('★', $commentaire->getRate()) . str_repeat('☆', 5 - $commentaire->getRate()) ?></span>
                            </p>
                            <?php if ($commentaire->getCommentTitle() !== null) : ?>
                                <p><strong><?= htmlspecialchars($commentaire->getCommentTitle()) ?></strong></p>
                            <?php endif; ?>
                            <!-- htmlspecialchars : du code HTML ou JavaScript écrit dans un commentaire s'affiche comme du texte -->
                            <p><?= htmlspecialchars($commentaire->getCommentText()) ?></p>
                            <?php if ($commentaire->getCommentStatus() === 'banni') : ?>
                                <!-- un commentaire masqué n'est lu que par l'administrateur, qui peut le réafficher -->
                                <p class="commentaire-info">Commentaire masqué : les visiteurs ne le voient pas.</p>
                                <button type="button" class="reafficher-avis">Réafficher</button>
                            <?php elseif ($utilisateur !== null && ($commentaire->getAuthorId() === $utilisateur['id'] || $estAdmin)) : ?>
                                <button type="button" class="supprimer-avis"><?= $commentaire->getAuthorId() === $utilisateur['id'] ? 'Supprimer' : 'Masquer' ?></button>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="carrousel-fleche carrousel-suiv" aria-label="Avis suivant">&#8250;</button>
            </div>
            <?php if ($commentaires['hasMore']) : ?>
                <!-- sans JavaScript, ce lien recharge la page avec 10 commentaires de plus ; avis.js le remplace par un chargement sans rechargement -->
                <a href="?avis=<?= $pageAvis + 1 ?>#titre-avis" class="btn-pill" id="voir-plus">Voir plus d'avis</a>
            <?php endif; ?>
        </section>
    </div>

    <!-- juste en dessous : donner son avis (note + commentaire) ou se connecter -->
    <section class="participer">
        <!-- les visiteurs ne voient ni le vote ni le formulaire : seulement une invitation à se connecter ou à s'inscrire -->
        <?php if ($utilisateur === null) : ?>
            <h2 id="votre-note">Donnez votre avis : </h2>
            <div class="invitation-avis">
                <p>Connectez-vous ou créez un compte pour noter cette recette et laisser un commentaire.</p>
                <p>
                    <button type="button" class="btn-pill" popovertarget="popup-connexion">Se connecter</button>
                    <a href="<?= RACINE_URL ?>connection?retour=<?= urlencode($_SERVER['REQUEST_URI']) ?>" class="btn-pill">S'inscrire</a>
                </p>
            </div>
        <?php else : ?>
            <!-- votre note : une seule par personne, modifiable à tout moment, avec ou sans commentaire.
             notation.js l'envoie à /api/noter sans recharger la page -->
            <h2 id="votre-note">Votre note : </h2>
            <div class="notation" id="notation"
                data-url="<?= RACINE_URL ?>api/noter"
                data-recipe="<?= $recette->getId() ?>"
                data-csrf="<?= $_SESSION['csrf_token'] ?>"
                data-user="<?= $utilisateur['id'] ?? 0 ?>"
                data-note="<?= $maNote ?? 0 ?>">
                <div class="etoiles" role="group" aria-label="Votre note">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <button type="button" class="etoile" data-valeur="<?= $i ?>" aria-label="<?= $i ?> sur 5">★</button>
                    <?php endfor; ?>
                </div>
                <p class="note-message" id="note-message" role="status">
                    <?= $maNote === null ? 'Cliquez sur une étoile pour noter. Le commentaire est facultatif.' : 'Votre note : ' . $maNote . '/5. Cliquez sur une autre étoile pour la modifier.' ?>
                </p>
            </div>

            <h2>Ajouter un commentaire (facultatif) : </h2>
            <form class="commentaire-form" id="form-avis" novalidate>
                <div class="form-field">
                    <input type="text" name="title" maxlength="120" placeholder="Sujet (facultatif)" />
                </div>
                <div class="form-field">
                    <textarea name="text" required maxlength="500" placeholder="Votre commentaire ..."></textarea>
                </div>
                <p class="form-feedback" id="avis-message" role="status"></p>
                <button type="submit" class="btn-pill">Publier le commentaire</button>
            </form>
        <?php endif; ?>
    </section>
</article>



<script src="<?= RACINE_URL ?>js/notation.js"></script>
<script src="<?= RACINE_URL ?>js/avis.js"></script>
<script src="<?= RACINE_URL ?>js/portions.js"></script>
<script src="<?= RACINE_URL ?>js/carrousel.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>