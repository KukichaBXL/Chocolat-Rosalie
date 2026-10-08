<?php
// path: model/mapping/CommentMapping.php
// typage strict
declare(strict_types=1);

namespace model\mapping;

use model\abstract\AbstractMapping;
use Exception;

// une ligne de la table `comments`
class CommentMapping extends AbstractMapping
{
    protected ?int $comment_id = null;
    protected ?int $author_id = null;
    protected ?int $recipe_id = null;
    protected ?string $comment_title = null;
    protected ?string $comment_text = null;
    protected ?string $comment_status = null;
    protected ?string $created_at = null;
    protected ?string $username = null;


     // Getter Comment ID
    public function getCommentId(): ?int
    {
        return $this->comment_id;
    }

    // Setter Comment TI
    public function setCommentId(int $comment_id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($comment_id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->comment_id = $comment_id;
    }

     // Getter Author ID
    public function getAuthorId(): ?int
    {
        return $this->author_id;
    }

    // Setter Author ID
    public function setAuthorId(int $author_id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($author_id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->author_id = $author_id;
    }

     // Getter Recipe ID
    public function getRecipeId(): ?int
    {
        return $this->recipe_id;
    }

    // Setter Recipe ID 
    public function setRecipeId(int $recipe_id): void
    {
        // si le chiffre est trop petit (négatif)
        if ($recipe_id <= 0) throw new Exception("L'id ne peut pas être négatif ou valoir 0", 333);
        $this->recipe_id = $recipe_id;
    }

    // Getter Comment title
    public function getCommentTitle(): ?string
    {
        return $this->comment_title;
    }

    // Setter Comment title
    public function setCommentTitle(string $comment_title): void
    {
        $this->comment_title = $comment_title;
    }

    // Getter Comment Text
    public function getCommentText(): ?string
    {
        return $this->comment_text;
    }

    // Setter Comment text
    public function setCommentText(string $comment_text): void
    {
        $this->comment_text = $comment_text;
    }

    // Getter Comment status
    public function getCommentStatus(): ?string
    {
        return $this->comment_status;
    }

    // Setter Comment status
    public function setCommentStatus(string $comment_status): void
    {
        $this->comment_status = $comment_status;
    }

    // Getter Created_at
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    // Setter Comment status
    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }
    
    // Getter username
    public function getUsername(): ?string
    {
        return $this->username;
    }

    // Setter username
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    
}
