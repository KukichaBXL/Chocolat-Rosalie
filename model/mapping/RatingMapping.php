<?php
// path: model/mapping/RatingMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `rating`
class RatingMapping extends AbstractMapping
{
    protected ?int $user_id = null;
    protected ?int $recipe_id = null;
    protected ?int $rate = null;
    protected ?string $date = null;

    // Getter Id
    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    // Setter Id
    public function setUserId(int $user_id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($user_id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->user_id = $user_id;
    }

    // Getter Recipe_id
    public function getRecipeId(): ?int
    {
        return $this->recipe_id;
    }

    // Setter recipe_id
    public function setRecipeId(int $recipe_id): void
    {
        // si le chiffre est trop petit
        if ($recipe_id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->recipe_id = $recipe_id;
    }

    // Getter rate
    public function getRate(): ?int
    {
        return $this->rate;
    }

    // Setter rate 
    public function setRate(int $rate): void
{
    // une note est un entier de 1 à 5
    if ($rate < 1 || $rate > 5) throw new Exception("La note doit être comprise entre 1 et 5", 333);
    $this->rate = $rate;
}
    // Getter date
    public function getDate(): ?string
    {
        return $this->date;
    }

    // Setter date
    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    
}
