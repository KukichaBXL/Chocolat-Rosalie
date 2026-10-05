<?php
// path: model/mapping/StepMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `step`
class StepMapping extends AbstractMapping
{
    // Champs du mapping
    protected ?int $idstep = null;
    protected ?int $recipe_id = null;
    protected ?int $step_number = null;
    protected ?string $step_title = null;
    protected ?string $description = null;
    protected ?string $step_photo = null;


     // Getter idStep
    public function getIdstep(): ?int
    {
        return $this->idstep;
    }

    // Setter idStep
    public function setIdstep(int $id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->idstep = $id;
    }

    // getter recipeId
    public function getRecipeId(): ?int
    {
        return $this->recipe_id;
    }
    // setter recipeId
    public function setRecipeId(int $recipe_id): void
    {
        $this->recipe_id = $recipe_id;
    }

    // getter stepNumber
    public function getStepNumber(): ?int
    {
         return $this->step_number;
    }

    // setter stepNumber
    public function setStepNumber(int $step_number): void
    {
        $this->step_number = $step_number;
    }

    // getter stepTitle
    public function getStepTitle(): ?string
    {
        return $this->step_title;
    }

    // setter stepTitle
    public function setStepTitle(string $step_title): void
    {
        $this->step_title = $step_title;
    }

    // getter description
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // setter description
    public function setDescription (string $description): void 
    {
        $this->description = $description;
    }

    // getter step_photo
    public function getStepPhoto (): ?string
    {
        return $this->step_photo;
    }

    // setter step_photo
    public function setStepPhoto (string $step_photo): void
    {
        $this->step_photo = $step_photo;
    }

}
