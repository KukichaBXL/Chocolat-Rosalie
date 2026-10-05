<?php
// path: model/mapping/UserMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `users`
class UserMapping extends AbstractMapping
{
    // Champs du mapping
    protected ?int $id = null;
    protected ?string $username = null;
    protected ?string $email = null;
    protected ?string $password = null;
    protected ?string $role = null;
    protected ?string $created_at = null;


    // Getter id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter id
    public function setId(int $id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->id = $id;
    }

    // Getter Username
    public function getUsername(): ?string
    {
        return $this->username;
    }

    // Setter Username
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    // Getter Email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Setter Email 
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    // Getter password
    public function getPassword(): ?string
    {
        return $this->password;
    }

    // Setter password
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    // Getter rôle
    public function getRole(): ?string
    {
        return $this->role;
    }

    // Setter rôle
    public function setRole(string $role): void
    {
        $this->role = $role;
    }


}

