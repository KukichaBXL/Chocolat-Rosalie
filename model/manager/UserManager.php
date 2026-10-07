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
        $sql = "SELECT user_id, user_login, user_pwd, user_full_name, user_email, user_role
            FROM user
            WHERE user_login = :login";
        $stmt = $this->connect->prepare($sql);
        $stmt->bindValue(':login', $userMap->getUsername());
        try{
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la connexion : " . $e->getMessage());
        }

        // si le login n'existe pas
        if($stmt->rowCount() === 0) {
            return null;
        }
        $user = $stmt->fetch();
        // fermeture de la requête
        $stmt->closeCursor();

        // vérification du mot de passe avec le hash stocké en base
        if(!password_verify($userMap->getPassword(), $user['user_pwd'])) {
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

    // un utilisateur grâce à son id, SANS son mot de passe
    // renvoie null si l'id n'existe pas
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
        static public function sessionUser(UserMapping $user): void
        {
            // nouvel identifiant de session pour éviter la fixation de session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->getUserId();
            $_SESSION['user_login'] = $user->getUsername();
            $_SESSION['user_full_name'] = $user->getUserFullName();
            $_SESSION['user_role'] = $user->getUserRole();
            unset($_SESSION['token']);
            // redirection vers l'accueil
            header('Location: '.RACINE_URL.'/');
            exit;
        }
        static public function deconnectUser(): void
        {
            // destruction complète de la session
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
            header('Location: ' . RACINE_URL . '/');
            exit;
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
    // $passwordHash est DÉJÀ haché par password_hash() : on n'enregistre jamais un mot de passe en clair
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

}