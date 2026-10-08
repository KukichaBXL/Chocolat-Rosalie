<?php
// path: model/manager/MessageContactManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\MyPDO;

// Requêtes sur `message_contact`
class MessageContactManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // Enregistre un message, conservé pour l'équipe de la chocolaterie
    public function add(string $name, string $email, ?string $subject, string $message): void
    {
        $prepare = $this->connect->prepare("
            INSERT INTO message_contact (name, email, subject, message)
            VALUES (:name, :email, :subject, :message)");
        $prepare->bindValue(':name', $name);
        $prepare->bindValue(':email', $email);
        $prepare->bindValue(':subject', $subject, $subject === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->bindValue(':message', $message);
        $prepare->execute();
    }
}
