<?php
// path: view/admin/recettes.php
// Variable : $recettes (préparée par PublicController).
?>

<section class="container admin-section">
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
                        <td data-label="Titre"><?= htmlspecialchars($recette->getTitle()) ?></td>
                    <?php else : ?>
                        <td data-label="Titre"><a href="<?= RACINE_URL ?>recette/<?= htmlspecialchars($recette->getRecipesSlug()) ?>"><?= htmlspecialchars($recette->getTitle()) ?></a></td>
                    <?php endif; ?>
                    <td data-label="Difficulté"><?= htmlspecialchars($recette->getDifficulty()) ?></td>
                    <td data-label="Statut"><span class="badge <?= $masquee ? 'badge-masque' : '' ?>"><?= $masquee ? 'Masquée' : 'Visible' ?></span></td>
                    <td data-label="Actions">
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