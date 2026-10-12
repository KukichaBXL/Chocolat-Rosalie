<?php
// path: model/manager/RatingManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\MyPDO;

// les requêtes SQL sur la table `rating`
class RatingManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // la note d'un utilisateur pour une recette (1 à 5), ou null s'il n'a pas encore voté
    public function getUserRate(int $userId, int $recipeId): ?int
    {
        $prepare = $this->connect->prepare("SELECT rate FROM rating WHERE user_id = :user AND recipe_id = :recipe");
        $prepare->bindValue(':user', $userId, MyPDO::PARAM_INT);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->execute();
        $note = $prepare->fetchColumn();
        return $note === false ? null : (int) $note;
    }

    // enregistre la note d'un utilisateur, s'il avait déjà voté, la nouvelle note REMPLACE l'ancienne
    // (la clé primaire est (user_id, recipe_id) : il ne peut pas y avoir deux votes)
    public function rate(int $userId, int $recipeId, int $note): void
    {
        $sql = "INSERT INTO rating (user_id, recipe_id, rate) VALUES (:user, :recipe, :rate)
                ON DUPLICATE KEY UPDATE rate = VALUES(rate), date = NOW()";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':user', $userId, MyPDO::PARAM_INT);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->bindValue(':rate', $note, MyPDO::PARAM_INT);
        $prepare->execute();
    }

    // la moyenne (une décimale, null s'il n'y a aucun vote) et le nombre de votes d'une recette
    public function getStats(int $recipeId): array
    {
        $prepare = $this->connect->prepare("SELECT ROUND(AVG(rate), 1) AS moyenne, COUNT(*) AS votes FROM rating WHERE recipe_id = :recipe");
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->execute();
        $ligne = $prepare->fetch();
        return [
            'average' => $ligne['moyenne'] === null ? null : (float) $ligne['moyenne'],
            'votes' => (int) $ligne['votes'],
        ];
    }
}
