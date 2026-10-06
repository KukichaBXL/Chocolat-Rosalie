<?php
// path: model/manager/UserManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\MyPDO;
use model\mapping\UserMapping;
use Exception;

class UserManager implements ManagerInterface
{
    protected MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // vérification des identifiants de connexion
    // retourne l'utilisateur si le login et le mot de passe sont corrects, sinon null
    public function connectUser(?UserMapping $userMap): ?UserMapping
    {
        $sql = "SELECT user_id, user_login, user_pwd, user_full_name, user_email, user_role
            FROM user
            WHERE user_login = :login";
        $stmt = $this->connect->prepare($sql);
        $stmt->bindValue(':login', $userMap->getUserLogin());
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
        if(!password_verify($userMap->getUserPwd(), $user['user_pwd'])) {
            return null;
        }

        // on ne garde pas le hash du mot de passe dans l'objet
        unset($user['user_pwd']);

        return new UserMapping($user);
    }

    // récupération d'un utilisateur par son id (sans le mot de passe), pour la page de profil
    public function getUserById(int $id): ?UserMapping
    {
        $sql = "SELECT user_id, user_login, user_full_name, user_email, user_role
            FROM user
            WHERE user_id = :id";
        $stmt = $this->connect->prepare($sql);
        $stmt->bindValue(':id', $id, MyPDO::PARAM_INT);
        try{
            $stmt->execute();
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
        }

        if($stmt->rowCount() === 0) {
            return null;
        }
        $user = $stmt->fetch();
        $stmt->closeCursor();

        return new UserMapping($user);
    }
        static public function sessionUser(UserMapping $user): void
        {
            // nouvel identifiant de session pour éviter la fixation de session
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user->getUserId();
            $_SESSION['user_login'] = $user->getUserLogin();
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

        // l'utilisateur connecté est-il administrateur ?
        static public function isAdmin(): bool
        {
            return ($_SESSION['user_role'] ?? null) === 'admin';
        }

        // vérification du jeton CSRF envoyé par un formulaire
        static public function checkToken(mixed $token): bool
        {
            return is_string($token) && isset($_SESSION['token'])
                && hash_equals($_SESSION['token'], $token);
        }

        // enregistre un message à afficher après la redirection, puis redirige
        static public function flashAndRedirect(string $path, string $message, string $type = 'success'): never
        {
            $_SESSION['flash'] = ['message' => $message, 'type' => $type];
            header('Location: ' . RACINE_URL . $path);
            exit;
        }

        // récupère le message flash éventuel, qui n'est affiché qu'une seule fois
        static public function getFlash(): ?array
        {
            $flash = $_SESSION['flash'] ?? null;
            unset($_SESSION['flash']);
            return $flash;
        }
}
