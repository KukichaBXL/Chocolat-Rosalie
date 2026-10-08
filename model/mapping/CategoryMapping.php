<?php
// path: model/mapping/CategoryMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use exception;

// une ligne de la table `category`
class CategoryMapping extends AbstractMapping
{
    protected ?int $id = null;
    protected ?string $title = null;
    protected ?string $description = null;
    protected ?string $category_slug = null;

    // Getter Id
    public function getId(): ?int
    {
        return $this->id;
    }

    // Setter Id
    public function setId(int $id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->id = $id;
    }

    // Getter Title
    public function getTitle(): ?string
    {
        return $this->title;
    }

    // Setter Title
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    // Getter Description 
    public function getDescription(): ?string
    {
        return $this->description;
    }

    // Setter Description
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    // Getter Category_slug
    public function getCategorySlug(): ?string
    {
        return $this->category_slug;
    }

    // Setter Category_slug
    public function setCategorySlug(string $category_slug): void
    {
        $this->category_slug = $category_slug;
    }
}
