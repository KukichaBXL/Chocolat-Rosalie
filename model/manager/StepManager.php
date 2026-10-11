<?php
// path: model/manager/StepManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\StepMapping;
use model\MyPDO;

// les requêtes SQL sur la table `step`
class StepManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    public function getByRecipe(int $recipeId): array
    {
        $sql = "SELECT * FROM step WHERE recipe_id = :recipe ORDER BY step_number";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->execute();

        $etapes = [];
        foreach ($prepare->fetchAll() as $ligne) {
            $etapes[] = new StepMapping($ligne);
        }
        return $etapes;
    }

    // ajoute une étape à une recette
    public function add(int $recipeId, int $number, string $description, ?string $photo): void
    {
        $sql = "INSERT INTO step (recipe_id, step_number, step_title, description, step_photo)
                VALUES (:recipe, :number, :title, :description, :photo)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->bindValue(':number', $number, MyPDO::PARAM_INT);
        $prepare->bindValue(':title', 'Étape ' . $number);
        $prepare->bindValue(':description', $description);
        $prepare->bindValue(':photo', $photo, $photo === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->execute();
    }
}
