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
}
