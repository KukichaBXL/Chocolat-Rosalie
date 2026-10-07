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
    protected ?string $quantity = null;
    protected ?string $unit = null;
    
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

    // Getter quantity
    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    // Setter quantity (peut être vide : "une pincée")
    public function setQuantity(?string $quantity): void
    {
        $this->quantity = $quantity;
    }

    // Getter unit
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    // Setter unit (peut être vide : "3 oeufs")
    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    // texte prêt à afficher : "150 gr", "0,5 l", "3" ou "une pincée"
    public function getQuantityLabel(): string
    {
        if ($this->quantity === null) {
            return (string) $this->unit;
        }
        $nombre = str_replace('.', ',', (string) (float) $this->quantity);
        return trim($nombre . ' ' . $this->unit);
    }

}
