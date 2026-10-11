<?php
// path: model/manager/CategoryManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\CategoryMapping;
use model\MyPDO;

// les requêtes SQL sur la table `category`
class CategoryManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    public function getAll(): array
    {
        $query = $this->connect->query("SELECT * FROM category ORDER BY title");

        $categories = [];
        foreach ($query->fetchAll() as $ligne) {
            $categories[] = new CategoryMapping($ligne);
        }
        return $categories;
    }

    public function exists(int $categoryId): bool
    {
        $prepare = $this->connect->prepare("SELECT 1 FROM category WHERE id = :id");
        $prepare->bindValue(':id', $categoryId, MyPDO::PARAM_INT);
        $prepare->execute();
        return $prepare->fetchColumn() !== false;
    }

    // range une recette dans une catégorie
    public function addRecipe(int $categoryId, int $recipeId): void
    {
        $prepare = $this->connect->prepare("INSERT INTO recipes_has_category (recipes_id, category_id) VALUES (:recipe, :category)");
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->bindValue(':category', $categoryId, MyPDO::PARAM_INT);
        $prepare->execute();
    }
}
