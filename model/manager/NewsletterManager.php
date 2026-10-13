<?php
// path: model/manager/NewsletterManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\MyPDO;

// les requêtes sur `newsletter_subscriber` (les abonnés) et `newsletter` (le message en préparation, une seule ligne : id 1)
class NewsletterManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // inscrit une adresse. Renvoie true si elle est nouvelle, false si elle était déjà inscrite
    public function subscribe(string $email): bool
    {
        $prepare = $this->connect->prepare("INSERT IGNORE INTO newsletter_subscriber (email) VALUES (:email)");
        $prepare->bindValue(':email', $email);
        $prepare->execute();
        return $prepare->rowCount() === 1;
    }

    // un abonné (tableau id, email, created_at) ou null
    public function getById(int $id): ?array
    {
        $prepare = $this->connect->prepare("SELECT id, email, created_at FROM newsletter_subscriber WHERE id = :id");
        $prepare->bindValue(':id', $id, MyPDO::PARAM_INT);
        $prepare->execute();
        $ligne = $prepare->fetch();
        return $ligne === false ? null : $ligne;
    }

    public function getByEmail(string $email): ?array
    {
        $prepare = $this->connect->prepare("SELECT id, email, created_at FROM newsletter_subscriber WHERE email = :email");
        $prepare->bindValue(':email', $email);
        $prepare->execute();
        $ligne = $prepare->fetch();
        return $ligne === false ? null : $ligne;
    }

    public function getAll(): array
    {
        return $this->connect->query("SELECT id, email, created_at FROM newsletter_subscriber ORDER BY created_at DESC, id DESC")->fetchAll();
    }

    public function remove(int $id): void
    {
        $prepare = $this->connect->prepare("DELETE FROM newsletter_subscriber WHERE id = :id");
        $prepare->bindValue(':id', $id, MyPDO::PARAM_INT);
        $prepare->execute();
    }

    // le message de la prochaine newsletter : ['subject' => ..., 'body' => ...]
    public function getMessage(): array
    {
        $ligne = $this->connect->query("SELECT subject, body FROM newsletter WHERE id = 1")->fetch();
        return $ligne === false ? ['subject' => '', 'body' => ''] : $ligne;
    }

    public function saveMessage(string $subject, string $body): void
    {
        $prepare = $this->connect->prepare("INSERT INTO newsletter (id, subject, body) VALUES (1, :subject, :body)
                                            ON DUPLICATE KEY UPDATE subject = VALUES(subject), body = VALUES(body)");
        $prepare->bindValue(':subject', $subject);
        $prepare->bindValue(':body', $body);
        $prepare->execute();
    }
}
