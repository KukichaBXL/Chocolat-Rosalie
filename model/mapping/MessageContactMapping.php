<?php
// path: model/mapping/MessageContactMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `message_contact`
class MessageContactMapping extends AbstractMapping
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $email = null;
    protected ?string $subject = null;
    protected ?string $message = null;
    protected ?string $date = null;


    // Getter Id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter Id
    public function setId(int $id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->id = $id;
    }

    // Getter Name
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter Name
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    // Getter email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Setter email
    public function setEmail(string $email): void
    {
        $this->email = $email;
    } 

    // Getter subject
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    // Setter subject
    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    // Getter message
    public function getMessage(): ?string
    {
        return $this->message;
    }

    // Setter message
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    // Getter date
    public function getDate(): ?string
    {
        return $this->date;
    }

    // Setter date

    public function setDate(string $date): void
    {
        $this->date = $date;
    }

}
