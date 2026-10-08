<?php
// path: model/manager/CommentManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\CommentMapping;
use model\MyPDO;

// les requêtes SQL sur la table `comments`
class CommentManager implements ManagerInterface
{
    // nombre de commentaires affichés à la fois
    public const PAGE_SIZE = 10;

    // la requête de lecture : le nom de l'auteur est dans la table `users`, d'où le JOIN
    private const SELECT_AVEC_AUTEUR = "SELECT c.comment_id, c.author_id, c.recipe_id, c.comment_title, c.comment_text,
                       c.comment_status, c.created_at, u.username
                FROM comments c
                JOIN users u ON u.id = c.author_id";

    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // les commentaires publiés d'UNE recette, du plus récent au plus ancien
    public function getByRecipe(int $recipeId, int $limit, int $offset): array
    {
        $sql = self::SELECT_AVEC_AUTEUR . "
                WHERE c.recipe_id = :recipe AND c.comment_status = 'publié'
                ORDER BY c.created_at DESC, c.comment_id DESC
                LIMIT :limit OFFSET :offset";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->bindValue(':limit', $limit + 1, MyPDO::PARAM_INT);
        $prepare->bindValue(':offset', $offset, MyPDO::PARAM_INT);
        $prepare->execute();
        $lignes = $prepare->fetchAll();

        $hasMore = count($lignes) > $limit;

        $commentaires = [];
        // array_slice enlève le commentaire en trop
        foreach (array_slice($lignes, 0, $limit) as $ligne) {
            $commentaires[] = new CommentMapping($ligne);
        }
        return ['comments' => $commentaires, 'hasMore' => $hasMore];
    }

    // un commentaire grâce à son id, ou null s'il n'existe pas
    public function getById(int $commentId): ?CommentMapping
    {
        $prepare = $this->connect->prepare(self::SELECT_AVEC_AUTEUR . " WHERE c.comment_id = :id");
        $prepare->bindValue(':id', $commentId, MyPDO::PARAM_INT);
        $prepare->execute();

        $ligne = $prepare->fetch();
        // fetch() renvoie false quand il n'y a aucun résultat
        if ($ligne === false) {
            return null;
        }
        return new CommentMapping($ligne);
    }

    // ajoute un commentaire et renvoie le commentaire enregistré
    public function add(int $authorId, int $recipeId, ?string $title, string $text): CommentMapping
    {
        $sql = "INSERT INTO comments (author_id, recipe_id, comment_title, comment_text)
                VALUES (:author, :recipe, :title, :text)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':author', $authorId, MyPDO::PARAM_INT);
        $prepare->bindValue(':recipe', $recipeId, MyPDO::PARAM_INT);
        $prepare->bindValue(':title', $title, $title === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->bindValue(':text', $text);
        $prepare->execute();

        return $this->getById((int) $this->connect->lastInsertId());
    }

    // supprime un commentaire
    public function delete(int $commentId): void
    {
        $prepare = $this->connect->prepare("DELETE FROM comments WHERE comment_id = :id");
        $prepare->bindValue(':id', $commentId, MyPDO::PARAM_INT);
        $prepare->execute();
    }

    // nombre de commentaires écrits par cet utilisateur pendant les X dernières minutes
    // sert à limiter le nombre de commentaires (anti-spam)
    public function countRecentByAuthor(int $authorId, int $minutes): int
    {
        $sql = "SELECT COUNT(*) FROM comments
                WHERE author_id = :author AND created_at > NOW() - INTERVAL :minutes MINUTE";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':author', $authorId, MyPDO::PARAM_INT);
        $prepare->bindValue(':minutes', $minutes, MyPDO::PARAM_INT);
        $prepare->execute();
        return (int) $prepare->fetchColumn();
    }
}
