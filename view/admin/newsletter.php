<?php
// path: view/admin/newsletter.php
// Variables : $abonnes (tableaux id, email, created_at) et $messageNewsletter (subject, body), préparés par PublicController.
$modeEnvoi = defined('MAIL_TRANSPORT') && MAIL_TRANSPORT === 'smtp' ? 'envoi réel par SMTP' : 'mode test : les e-mails sont écrits dans logs/emails.log, rien ne part';
?>

<section class="container admin-section">
    <h2>La newsletter</h2>
    <p class="admin-note">Mode d'envoi : <strong><?= htmlspecialchars($modeEnvoi) ?></strong>. Chaque e-mail se termine par un lien personnel de désinscription.</p>

    <form method="post" class="admin-form">
        <?= champCsrf() ?>
        <div class="form-field">
            <input type="text" name="subject" required minlength="3" maxlength="150" placeholder="Sujet" value="<?= valeur('subject', $messageNewsletter['subject']) ?>" />
        </div>
        <div class="form-field">
            <textarea name="body" rows="9" required minlength="10" maxlength="5000" placeholder="Message"><?= valeur('body', $messageNewsletter['body']) ?></textarea>
        </div>
        <div class="admin-boutons">
            <button type="submit" name="action" value="enregistrer-newsletter" class="btn-pill">Enregistrer</button>
            <button type="submit" name="action" value="tester-newsletter" class="btn-pill">M'envoyer un essai</button>
            <button type="submit" name="action" value="envoyer-newsletter" class="btn-pill"
                data-confirm="Envoyer cette newsletter aux <?= count($abonnes) ?> abonné(s) ?">Envoyer à tous (<?= count($abonnes) ?>)</button>
        </div>
    </form>
</section>

<section class="container admin-section">
    <h2>Les abonnés (<?= count($abonnes) ?>)</h2>
    <?php if ($abonnes === []) : ?>
        <p>Personne n'est encore inscrit.</p>
    <?php else : ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Inscrit le</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($abonnes as $abonne) : ?>
                    <tr>
                        <td><?= htmlspecialchars($abonne['email']) ?></td>
                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($abonne['created_at']))) ?></td>
                        <td>
                            <form method="post" data-confirm="Retirer cet abonné ?">
                                <input type="hidden" name="action" value="retirer-abonne" />
                                <input type="hidden" name="subscriber_id" value="<?= (int) $abonne['id'] ?>" />
                                <?= champCsrf() ?>
                                <button type="submit">Retirer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<script src="<?= RACINE_URL ?>js/admin.js"></script>
