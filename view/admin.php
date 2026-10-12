<?php
// path: view/admin.php
// Variables : $categories, $recettes, $commentairesAdmin et $erreurs (préparées par PublicController).
$titre = 'Administration';
require RACINE_PATH . '/view/inc/header.php';

// les textes des étapes déjà tapés (si le formulaire est revenu avec des erreurs), sinon 3 étapes vides
$textesEtapes = is_array($_POST['etape_texte'] ?? null) ? array_values($_POST['etape_texte']) : ['', '', ''];
?>

<section class="container page-title">
    <h1>Administration</h1>
    <p><a href="#recettes">Recettes</a> · <a href="#commentaires">Commentaires</a> · <a href="#ajouter">Ajouter une recette</a></p>
</section>

<section class="container admin-section" id="recettes">
    <h2>Les recettes (<?= count($recettes) ?>)</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Titre</th>
                <th>Difficulté</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recettes as $recette) : ?>
                <?php $masquee = $recette->getRecipeStatus() === 'masquée'; ?>
                <tr class="<?= $masquee ? 'recette-masquee' : '' ?>">
                    <?php if ($masquee) : ?>
                        <td><?= htmlspecialchars($recette->getTitle()) ?></td>
                    <?php else : ?>
                        <td><a href="<?= RACINE_URL ?>recette/<?= htmlspecialchars($recette->getRecipesSlug()) ?>"><?= htmlspecialchars($recette->getTitle()) ?></a></td>
                    <?php endif; ?>
                    <td><?= htmlspecialchars($recette->getDifficulty()) ?></td>
                    <td><?= $masquee ? 'Masquée' : 'Visible' ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="<?= $masquee ? 'afficher-recette' : 'masquer-recette' ?>" />
                            <input type="hidden" name="recipe_id" value="<?= $recette->getId() ?>" />
                            <?= champCsrf() ?>
                            <button type="submit"><?= $masquee ? 'Réafficher' : 'Masquer' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>

<section class="container admin-section" id="commentaires">
    <h2>Les commentaires</h2>
    <?php if ($commentairesAdmin === []) : ?>
        <p>Aucun commentaire pour le moment.</p>
    <?php else : ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Recette</th>
                    <th>Auteur</th>
                    <th>Commentaire</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($commentairesAdmin as $commentaire) : ?>
                    <?php $masque = $commentaire->getCommentStatus() === 'banni'; ?>
                    <tr class="<?= $masque ? 'commentaire-masque' : '' ?>">
                        <td><?= htmlspecialchars($commentaire->getRecipeTitle()) ?></td>
                        <td><?= htmlspecialchars($commentaire->getUsername()) ?></td>
                        <td><?= htmlspecialchars($commentaire->getCommentText()) ?></td>
                        <td><?= $masque ? 'Masqué' : 'Visible' ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="action" value="<?= $masque ? 'afficher-commentaire' : 'masquer-commentaire' ?>" />
                                <input type="hidden" name="comment_id" value="<?= $commentaire->getCommentId() ?>" />
                                <?= champCsrf() ?>
                                <button type="submit"><?= $masque ? 'Réafficher' : 'Masquer' ?></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<section class="container page-title" id="ajouter">
    <h2>Ajouter une recette</h2>
    <p>Remplissez la fiche, puis ajoutez les photos (jpg, png ou webp, 300 Ko maximum).</p>
</section>

<form class="container admin-form" method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="action" value="ajouter-recette" />
    <?= champCsrf() ?>

    <fieldset class="admin-bloc">
        <legend>La recette</legend>
        <div class="admin-infos">
            <label class="photo-slot photo-principale">
                <span class="photo-vide"></span>
                <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required />
                <span class="form-error"><?= $erreurs['photo'] ?? '' ?></span>
            </label>

            <div class="admin-champs">
                <div class="form-field">
                    <input type="text" name="title" required maxlength="100" placeholder="Titre" value="<?= ancien('title') ?>" />
                    <span class="form-error"><?= $erreurs['title'] ?? '' ?></span>
                </div>
                <div class="admin-ligne">
                    <div class="form-field">
                        <input type="number" name="prepare_time" min="1" max="1440" placeholder="Préparation (min)" value="<?= ancien('prepare_time') ?>" />
                        <span class="form-error"><?= $erreurs['prepare_time'] ?? '' ?></span>
                    </div>
                    <div class="form-field">
                        <input type="number" name="cook_time" min="0" max="1440" placeholder="Cuisson (min)" value="<?= ancien('cook_time') ?>" />
                        <span class="form-error"><?= $erreurs['cook_time'] ?? '' ?></span>
                    </div>
                </div>
                <div class="admin-ligne">
                    <div class="form-field">
                        <input type="number" name="portions" min="1" max="50" placeholder="Personnes" value="<?= ancien('portions') ?>" />
                        <span class="form-error"><?= $erreurs['portions'] ?? '' ?></span>
                    </div>
                    <div class="form-field">
                        <select name="difficulty">
                            <option value="">Difficulté</option>
                            <?php foreach (['facile', 'moyen', 'difficile'] as $niveau) : ?>
                                <option value="<?= $niveau ?>" <?= ancien('difficulty') === $niveau ? 'selected' : '' ?>><?= ucfirst($niveau) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-error"><?= $erreurs['difficulty'] ?? '' ?></span>
                    </div>
                </div>
                <div class="form-field">
                    <select name="category">
                        <option value="">Catégorie</option>
                        <?php foreach ($categories as $categorie) : ?>
                            <option value="<?= $categorie->getId() ?>" <?= ancien('category') === (string) $categorie->getId() ? 'selected' : '' ?>><?= htmlspecialchars($categorie->getTitle()) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-error"><?= $erreurs['category'] ?? '' ?></span>
                </div>
                <div class="form-field">
                    <textarea name="description" maxlength="400" placeholder="Description"><?= ancien('description') ?></textarea>
                    <span class="form-error"><?= $erreurs['description'] ?? '' ?></span>
                </div>
                <div class="form-field">
                    <textarea name="ingredients" required placeholder="Ingrédients, un par ligne&#10;150 g de chocolat noir&#10;1 pincée de sel&#10;4 œufs"><?= ancien('ingredients') ?></textarea>
                    <span class="form-error"><?= $erreurs['ingredients'] ?? '' ?></span>
                </div>
            </div>
        </div>
    </fieldset>

    <fieldset class="admin-bloc">
        <legend>Les étapes</legend>
        <ol class="admin-etapes" id="liste-etapes">
            <?php foreach ($textesEtapes as $i => $texte) : ?>
                <li class="admin-etape">
                    <label class="photo-slot">
                        <span class="photo-vide"></span>
                        <input type="file" name="etape_photo[]" accept="image/jpeg,image/png,image/webp" />
                    </label>
                    <div class="form-field">
                        <textarea name="etape_texte[]" placeholder="Étape <?= $i + 1 ?>"><?= is_string($texte) ? htmlspecialchars($texte) : '' ?></textarea>
                    </div>
                </li>
            <?php endforeach; ?>
        </ol>
        <span class="form-error"><?= $erreurs['etapes'] ?? '' ?></span>
        <button type="button" class="btn-pill" id="ajouter-etape">Ajouter une étape</button>
    </fieldset>

    <button type="submit" class="btn-pill">Publier la recette</button>
    <p class="form-feedback"><?= $erreurs['connexion'] ?? '' ?></p>
</form>

<script src="<?= RACINE_URL ?>js/admin.js"></script>
<?php require RACINE_PATH . '/view/inc/footer.php'; ?>