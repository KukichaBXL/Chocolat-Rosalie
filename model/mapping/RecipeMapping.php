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
    protected string $recipe_status = 'publiée';   // 'publiée' ou 'masquée' (cachée du site par l'administrateur)
    // calculés par la requête de RecipeManager (pas des colonnes de la table)
    protected ?int $total_time = null;
    protected ?float $average = null;
    protected int $votes = 0;
    protected ?string $category_title = null;
    // MAX
    // protected ?string $PhotoStep = null;



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
    public function getTitle(): ?string
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
    public function getDescription(): ?string
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

    // ----- champs calculés par la requête SQL (ils ne sont pas dans la table `recipes`) -----

    // temps total en minutes : préparation + cuisson, calculé par la base
    public function getTotalTime(): ?int
    {
        return $this->total_time;
    }
    public function setTotalTime(int $total_time): void
    {
        $this->total_time = $total_time;
    }

    // note moyenne (une décimale), null s'il n'y a aucun vote
    public function getAverage(): ?float
    {
        return $this->average;
    }
    public function setAverage(?string $average): void
    {
        $this->average = $average === null ? null : (float) $average;
    }

    // nombre de votes
    public function getVotes(): int
    {
        return $this->votes;
    }
    public function setVotes(int $votes): void
    {
        $this->votes = $votes;
    }

    // titre de la catégorie (ou des catégories séparées par une virgule)
    public function getCategoryTitle(): ?string
    {
        return $this->category_title;
    }
    public function setCategoryTitle(?string $category_title): void
    {
        $this->category_title = $category_title;
    }

    // Getter recipe_status
    public function getRecipeStatus(): string
    {
        return $this->recipe_status;
    }

    // Setter recipe_status
    public function setRecipeStatus(string $recipe_status): void
    {
        $this->recipe_status = $recipe_status;
    }
}
