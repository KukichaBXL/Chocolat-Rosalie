<?php
// path: model/mapping/IngredientMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `ingredient`
class IngredientMapping extends AbstractMapping
{
    protected ?int $id = null;
    protected ?string $name = null;
    
    // Getter id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter id
    public function setId(int $id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->id = $id;
    }

    // Getter name
    public function getName(): ?string
    {
        return $this->name;
    }

    // Setter Name
    public function setName(string $name): void
    {
        $this->name = $name;
    }

}
