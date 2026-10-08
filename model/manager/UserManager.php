<?php
// path: model/manager/UserManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\UserMapping;
use model\MyPDO;

// les requêtes SQL sur la table `users`
// et sur la table `login_attempt` (les connexions ratées, pour bloquer les essais répétés)
class UserManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // un utilisateur grâce à son email, AVEC son mot de passe haché
    // renvoie null si l'email n'existe pas
    public function getByEmail(string $email): ?UserMapping
    {
        $sql = "SELECT id, username, email, password, role, created_at FROM users WHERE email = :email";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':email', $email);
        $prepare->execute();

        $ligne = $prepare->fetch();
        // fetch() renvoie false quand il n'y a aucun résultat
        if ($ligne === false) {
            return null;
        }
        return new UserMapping($ligne);
    }

    // un utilisateur grâce à son id, SANS son mot de passe (pour une page de profil, par exemple)
    public function getById(int $id): ?UserMapping
    {
        $sql = "SELECT id, username, email, role, created_at FROM users WHERE id = :id";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':id', $id, MyPDO::PARAM_INT);
        $prepare->execute();

        $ligne = $prepare->fetch();
        if ($ligne === false) {
            return null;
        }
        return new UserMapping($ligne);
    }

    // ce nom d'utilisateur est-il déjà pris ?
    public function usernameExists(string $username): bool
    {
        $prepare = $this->connect->prepare("SELECT 1 FROM users WHERE username = :username");
        $prepare->bindValue(':username', $username);
        $prepare->execute();
        return $prepare->fetchColumn() !== false;
    }

    // cet email est-il déjà utilisé ?
    public function emailExists(string $email): bool
    {
        $prepare = $this->connect->prepare("SELECT 1 FROM users WHERE email = :email");
        $prepare->bindValue(':email', $email);
        $prepare->execute();
        return $prepare->fetchColumn() !== false;
    }

    // crée un compte et renvoie l'utilisateur créé
    // $passwordHash est DÉJÀ haché par password_hash()
    public function create(string $username, string $email, string $passwordHash): UserMapping
    {
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':username', $username);
        $prepare->bindValue(':email', $email);
        $prepare->bindValue(':password', $passwordHash);
        $prepare->execute();

        // le rôle « inscrit » est la valeur par défaut de la colonne `role`
        return new UserMapping([
            'id' => (int) $this->connect->lastInsertId(),
            'username' => $username,
            'email' => $email,
            'role' => 'inscrit',
        ]);
    }

    // Protection contre les essais de connexion répétés 
    // nombre d'échecs récents pour cette adresse IP et pour cet email
    // renvoie ['ip' => nombre, 'email' => nombre]
    public function countRecentFailures(string $ip, string $email, int $minutes): array
    {
        // un même marqueur (:ip, :email) ne peut servir qu'une fois dans une requête préparée : on les numérote
        $sql = "SELECT COALESCE(SUM(ip = :ip1), 0) AS par_ip, COALESCE(SUM(email = :email1), 0) AS par_email
                FROM login_attempt
                WHERE attempted_at > NOW() - INTERVAL :minutes MINUTE
                  AND (ip = :ip2 OR email = :email2)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':ip1', $ip);
        $prepare->bindValue(':email1', $email);
        $prepare->bindValue(':ip2', $ip);
        $prepare->bindValue(':email2', $email);
        $prepare->bindValue(':minutes', $minutes, MyPDO::PARAM_INT);
        $prepare->execute();
        $ligne = $prepare->fetch();

        return ['ip' => (int) $ligne['par_ip'], 'email' => (int) $ligne['par_email']];
    }

    // enregistre une connexion ratée
    public function addFailure(string $ip, string $email): void
    {
        $prepare = $this->connect->prepare("INSERT INTO login_attempt (ip, email) VALUES (:ip, :email)");
        $prepare->bindValue(':ip', $ip);
        $prepare->bindValue(':email', $email);
        $prepare->execute();
    }

    // après une connexion réussie, on efface les échecs de ce compte
    // (et au passage les échecs de plus d'un jour, pour ne pas remplir la table)
    public function clearFailures(string $email): void
    {
        $sql = "DELETE FROM login_attempt WHERE email = :email OR attempted_at < NOW() - INTERVAL 1 DAY";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':email', $email);
        $prepare->execute();
    }
}
