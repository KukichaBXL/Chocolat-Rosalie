<?php
// path: model/manager/RecipeManager.php
// typage strict
declare(strict_types=1);

namespace model\manager;

use model\interface\ManagerInterface;
use model\mapping\RecipeMapping;
use model\MyPDO;

// les requêtes SQL sur la table `recipes`
class RecipeManager implements ManagerInterface
{
    private MyPDO $connect;

    public function __construct(MyPDO $connect)
    {
        $this->connect = $connect;
    }

    // La requête de lecture d'une recette, avec ce qu'il faut pour l'affichage :
    // temps total (BE-14), note moyenne, nombre de votes et catégorie. Tout est calculé par la base.
    private const SELECT_AVEC_NOTES = "SELECT r.*,
                r.prepare_time + r.cook_time AS total_time,
                (SELECT ROUND(AVG(rate), 1) FROM rating WHERE recipe_id = r.id) AS average,
                (SELECT COUNT(*) FROM rating WHERE recipe_id = r.id) AS votes,
                (SELECT GROUP_CONCAT(c.title ORDER BY c.title SEPARATOR ', ')
                   FROM recipes_has_category rc JOIN category c ON c.id = rc.category_id
                  WHERE rc.recipes_id = r.id) AS category_title
            FROM recipes r";

    // les recettes affichées sur le site, par ordre alphabétique.
    // $avecMasquees = true : TOUTES les recettes, y compris les masquées (pour la page d'administration)
    public function getAll(bool $avecMasquees = false): array
    {
        $condition = $avecMasquees ? "" : " WHERE r.recipe_status = 'publiée'";
        $query = $this->connect->query(self::SELECT_AVEC_NOTES . $condition . " ORDER BY r.title");
        return $this->versMappings($query->fetchAll());
    }

    // une recette grâce à son adresse (slug), ou null si elle n'existe pas
    public function getOneBySlug(string $slug): ?RecipeMapping
    {
        $prepare = $this->connect->prepare(self::SELECT_AVEC_NOTES . " WHERE r.recipes_slug = :slug AND r.recipe_status = 'publiée'");
        $prepare->bindValue(':slug', $slug);
        $prepare->execute();

        $ligne = $prepare->fetch();
        // fetch() renvoie false quand il n'y a aucun résultat
        if ($ligne === false) {
            return null;
        }
        return new RecipeMapping($ligne);
    }

    // le top 3 (BE-30) : 1) note moyenne décroissante, 2) à égalité plus de votes, 3) encore à égalité la plus récente.
    // Les recettes sans vote passent après celles qui en ont : s'il y a moins de 3 recettes notées,
    // le top est complété avec les recettes les plus récentes.
    public function getTop3(): array
    {
        $sql = self::SELECT_AVEC_NOTES . " WHERE r.recipe_status = 'publiée' ORDER BY votes > 0 DESC, average DESC, votes DESC, r.created_at DESC, r.id DESC LIMIT 3";
        return $this->versMappings($this->connect->query($sql)->fetchAll());
    }

    // la recherche en temps réel : les recettes dont le titre, la catégorie ou un ingrédient contient le texte tapé.
    // Les caractères spéciaux du LIKE (% et _) sont neutralisés pour qu'ils soient cherchés comme du texte normal.
    public function search(string $texte, int $limite = 6): array
    {
        $motif = '%' . addcslashes($texte, '%_\\') . '%';
        $sql = self::SELECT_AVEC_NOTES . " WHERE r.recipe_status = 'publiée' AND (
                    r.title LIKE :t
                 OR EXISTS (SELECT 1 FROM recipes_has_category rc JOIN category c ON c.id = rc.category_id
                             WHERE rc.recipes_id = r.id AND c.title LIKE :c)
                 OR EXISTS (SELECT 1 FROM recipes_ingredient ri JOIN ingredient i ON i.id = ri.ingredient_id
                             WHERE ri.recipe_id = r.id AND i.name LIKE :i)
                ) ORDER BY r.title LIMIT :limite";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':t', $motif);
        $prepare->bindValue(':c', $motif);
        $prepare->bindValue(':i', $motif);
        $prepare->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $prepare->execute();
        return $this->versMappings($prepare->fetchAll());
    }

    // transforme des lignes SQL en objets RecipeMapping
    private function versMappings(array $lignes): array
    {
        $recettes = [];
        foreach ($lignes as $ligne) {
            $recettes[] = new RecipeMapping($ligne);
        }
        return $recettes;
    }

    // cette adresse (slug) est-elle déjà prise par une recette ?
    public function slugExists(string $slug): bool
    {
        $prepare = $this->connect->prepare("SELECT 1 FROM recipes WHERE recipes_slug = :slug");
        $prepare->bindValue(':slug', $slug);
        $prepare->execute();
        return $prepare->fetchColumn() !== false;
    }

    // ajoute une recette et renvoie son id
    public function create(string $title, ?string $description, string $photo, int $prepareTime, int $cookTime, int $portions, string $difficulty, int $userId, string $slug): int
    {
        $sql = "INSERT INTO recipes (title, description, photo_main, prepare_time, cook_time, portions, difficulty, users_id, recipes_slug)
                VALUES (:title, :description, :photo, :prepare, :cook, :portions, :difficulty, :user, :slug)";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':title', $title);
        $prepare->bindValue(':description', $description, $description === null ? MyPDO::PARAM_NULL : MyPDO::PARAM_STR);
        $prepare->bindValue(':photo', $photo);
        $prepare->bindValue(':prepare', $prepareTime, MyPDO::PARAM_INT);
        $prepare->bindValue(':cook', $cookTime, MyPDO::PARAM_INT);
        $prepare->bindValue(':portions', $portions, MyPDO::PARAM_INT);
        $prepare->bindValue(':difficulty', $difficulty);
        $prepare->bindValue(':user', $userId, MyPDO::PARAM_INT);
        $prepare->bindValue(':slug', $slug);
        $prepare->execute();
        return (int) $this->connect->lastInsertId();
    }

    // cette recette existe-t-elle sur le site ? Une recette masquée compte comme absente,
    // sauf si $avecMasquees vaut true (pour l'administration)
    public function exists(int $recipeId, bool $avecMasquees = false): bool
    {
        $sql = $avecMasquees ? "SELECT 1 FROM recipes WHERE id = :id" : "SELECT 1 FROM recipes WHERE id = :id AND recipe_status = 'publiée'";
        $prepare = $this->connect->prepare($sql);
        $prepare->bindValue(':id', $recipeId, MyPDO::PARAM_INT);
        $prepare->execute();
        return $prepare->fetchColumn() !== false;
    }

    // masque ou réaffiche une recette SANS la supprimer : $statut vaut 'masquée' ou 'publiée'
    // (ses étapes, notes et commentaires restent dans la base)
    public function setStatus(int $recipeId, string $statut): void
    {
        $prepare = $this->connect->prepare("UPDATE recipes SET recipe_status = :statut WHERE id = :id");
        $prepare->bindValue(':statut', $statut);
        $prepare->bindValue(':id', $recipeId, MyPDO::PARAM_INT);
        $prepare->execute();
    }
}
