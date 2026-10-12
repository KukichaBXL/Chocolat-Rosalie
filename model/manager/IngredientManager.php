<?php
// path: model/manager/IngredientManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\IngredientMapping;
use model\MyPDO;

// les requêtes SQL sur la table `ingredient`
class IngredientManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    public function getByRecipe(int $recipeId): array
    {

        $sql = "SELECT i.id, i.name, ri.quantity, ri.unit
                FROM recipes_ingredient ri
                JOIN ingredient i ON i.id = ri.ingredient_id
                WHERE ri.recipe_id = :recipe
                ORDER BY i.name";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->execute();

        $ingredients = [];
        foreach ($prepare->fetchAll() as $ligne) {
            $ingredients[] = new IngredientMapping($ligne);
        }
        return $ingredients;
    }

    // renvoie l'id d'un ingrédient, en le créant s'il n'existe pas encore
    public function findOrCreate(string $name): int
    {
        $prepare = $this->connect->prepare("SELECT id FROM ingredient WHERE name = :name");
        $prepare->bindValue(':name', $name);
        $prepare->execute();
        $id = $prepare->fetchColumn();
        if ($id !== false) {
            return (int) $id;
        }

        $prepare = $this->connect->prepare("INSERT INTO ingredient (name) VALUES (:name)");
        $prepare->bindValue(':name', $name);
        $prepare->execute();
        return (int) $this->connect->lastInsertId();
    }

    // lie un ingrédient à une recette (quantité et unité facultatives)
    // si l'ingrédient est écrit deux fois, la dernière ligne remplace la première
    public function addToRecipe(int $recipeId, int $ingredientId, ?string $quantity, ?string $unit): void
    {
        $sql = "INSERT INTO recipes_ingredient (recipe_id, ingredient_id, quantity, unit)
                VALUES (:recipe, :ingredient, :quantity, :unit)
                ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), unit = VALUES(unit)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->bindValue(':ingredient', $ingredientId, MyPDO::PARAM_INT);
        $prepare->bindValue(':quantity', $quantity, $quantity === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->bindValue(':unit', $unit, $unit === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->execute();
    }
}
