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
}
