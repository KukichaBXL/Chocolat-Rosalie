<?php
// path: view/admin/commentaires.php
// Variable : $commentairesAdmin (préparée par PublicController).
?>

<section class="container admin-section">
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
                        <td data-label="Recette"><?= htmlspecialchars($commentaire->getRecipeTitle()) ?></td>
                        <td data-label="Auteur"><?= htmlspecialchars($commentaire->getUsername()) ?></td>
                        <td data-label="Commentaire" class="cellule-texte"><?= htmlspecialchars($commentaire->getCommentText()) ?></td>
                        <td data-label="Statut"><span class="badge <?= $masque ? 'badge-masque' : '' ?>"><?= $masque ? 'Masqué' : 'Visible' ?></span></td>
                        <td data-label="Action">
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