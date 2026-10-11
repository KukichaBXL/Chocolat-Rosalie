<?php
// path: model/manager/RecipeManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\RecipeMapping;
use model\MyPDO;

// les requêtes SQL sur la table `recipes`
class RecipeManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    public function getAll(): array
    {
        $sql = "SELECT * FROM recipes ORDER BY title";
        $query = $this->connect->query($sql);

        $recettes = [];
        foreach ($query->fetchAll() as $ligne) {
            $recettes[] = new RecipeMapping($ligne);
        }
        return $recettes;
    }

    public function getOneBySlug(string $slug): ?RecipeMapping
    {
        $sql = "SELECT * FROM recipes WHERE recipes_slug = :slug";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':slug', $slug);
        $prepare->execute();

        $ligne = $prepare->fetch();
        // fetch() renvoie false quand il n'y a aucun résultat
        if ($ligne === false) {
            return null;
        }
        return new RecipeMapping($ligne);
    }

    // cette adresse (slug) est-elle déjà prise par une recette ?
    public function slugExists(string $slug): bool
    {
        $prepare = $this->connect->prepare("SELECT 1 FROM recipes WHERE recipes_slug = :slug");
        $prepare->bindValue(':slug', $slug);
        $prepare->execute();
        return $prepare->fetchColumn() !== false;
    }

    // ajoute une recette et renvoie son id
    public function create(string $title, ?string $description, string $photo, int $prepareTime, int $cookTime, int $portions, string $difficulty, int $userId, string $slug): int
    {
        $sql = "INSERT INTO recipes (title, description, photo_main, prepare_time, cook_time, portions, difficulty, users_id, recipes_slug)
                VALUES (:title, :description, :photo, :prepare, :cook, :portions, :difficulty, :user, :slug)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':title', $title);
        $prepare->bindValue(':description', $description, $description === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->bindValue(':photo', $photo);
        $prepare->bindValue(':prepare', $prepareTime, MyPDO::PARAM_INT);
        $prepare->bindValue(':cook', $cookTime, MyPDO::PARAM_INT);
        $prepare->bindValue(':portions', $portions, MyPDO::PARAM_INT);
        $prepare->bindValue(':difficulty', $difficulty);
        $prepare->bindValue(':user', $userId, MyPDO::PARAM_INT);
        $prepare->bindValue(':slug', $slug);
        $prepare->execute();
        return (int) $this->connect->lastInsertId();
    }
}
