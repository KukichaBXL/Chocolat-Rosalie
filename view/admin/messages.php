<?php
// path: view/admin/messages.php
// Variable : $messages (préparée par PublicController) : les messages reçus par le formulaire de contact.
?>

<section class="container admin-section">
    <h2>Les messages reçus</h2>
    <?php if ($messages === []) : ?>
        <p>Aucun message pour le moment.</p>
    <?php else : ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Sujet</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $envoi) : ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $envoi->getDate()) ?></td>
                        <td><?= htmlspecialchars((string) $envoi->getName()) ?></td>
                        <td><a href="mailto:<?= htmlspecialchars((string) $envoi->getEmail()) ?>"><?= htmlspecialchars((string) $envoi->getEmail()) ?></a></td>
                        <td><?= htmlspecialchars((string) $envoi->getSubject()) ?></td>
                        <td class="cellule-texte"><?= nl2br(htmlspecialchars((string) $envoi->getMessage())) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>