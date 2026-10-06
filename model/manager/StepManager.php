<?php
// path: model/manager/StepManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;
use model\interface\StepMapping;
use model\interface\ManagerInterface;
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
}
