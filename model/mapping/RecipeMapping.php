<?php
// path: model/mapping/RecipeMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;


use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `recipes`
class RecipeMapping extends AbstractMapping
{   
    // Champs du mapping
    protected ?int $id = null;
    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $photo_main = null;
    protected ?int $prepare_time = null;
    protected ?int $cook_time = null;
    protected ?int $portions = null;
    protected ?string $created_at = null;
    protected ?string $difficulty = null;
    protected ?int $users_id = null;
    protected ?string $recipes_slug = null;



    // Getter
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter
    public function setId(int $id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->id = $id;
    }

    // Getter
    public function getTitle (): ?string
    {
        return $this->title;
    }

    // Setter
    public function setTitle(string $title): void
    {
        $nbTitle = mb_strlen(trim($title));
        if ($nbTitle < 3 || $nbTitle > 180) throw new Exception("Le titre doit avoir entre 3 et 180 caractères", 333);
        $title = trim($title);
        $this->title = $title;
    }

    // Getter 
    public function getDescription (): ?string
    {
        return $this->description;
    }

    // Setter
    public function setDescription(?string $description): void 
    {
        $this->description = $description;
    }

    // Getter
    public function getPhotoMain(): ?string
    {
        return $this->photo_main;
    }
    // Setter
    public function setPhotoMain(?string $photo_main): void
    {
        $this->photo_main = $photo_main;
    }

    // Getter
    public function getPrepareTime(): ?int
    {
        return $this->prepare_time;
    }
    // Setter
    public function setPrepareTime(int $prepare_time): void
    {
        $this->prepare_time = $prepare_time;
    }

    // Getter
    public function getCookTime(): ?int
    {
        return $this->cook_time;
    }
    // Setter
    public function setCookTime(int $cook_time): void
    {
        $this->cook_time = $cook_time;
    }

    // Getter
    public function getPortions(): ?int
    {
        return $this->portions;
    }
    // Setter
    public function setPortions(int $portions): void
    {
        $this->portions = $portions;
    }

    // Getter
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }
    // Setter
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    // Getter
    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }
    // Setter
    public function setDifficulty(string $difficulty): void
    {
        $this->difficulty = $difficulty;
    }

    // Getter
    public function getUsersId(): ?int
    {
        return $this->users_id;
    }
    // Setter
    public function setUsersId(int $users_id): void
    {
        $this->users_id = $users_id;
    }

    // getter
    public function getRecipesSlug(): ?string
    {
        return $this->recipes_slug;
    }
    // setter
    public function setRecipesSlug(string $recipes_slug): void
    {
        $this->recipes_slug = $recipes_slug;
    }
}
