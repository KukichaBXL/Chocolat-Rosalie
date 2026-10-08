<?php
declare(strict_types=1);

// Contrôleur public : il PRÉPARE les données de chaque page (avec les managers), puis inclut la vue.

// Variables disponibles : $db (connexion MyPDO) et $pg (la page demandée), créés dans public/index.php.

use model\manager\CommentManager;
use model\manager\IngredientManager;
use model\manager\RecipeManager;
use model\manager\StepManager;

switch ($pg) {
    // page d'accueil
    case 'accueil':
        require RACINE_PATH.'/view/accueil.php';
        break;

    // liste des recettes
    case 'recettes':
        $recettes = (new RecipeManager($db))->getAll();
        require RACINE_PATH.'/view/recettes.php';
        break;

    // une recette : 
    case 'recette':
        // le slug vient de l'adresse
        $slug = $_GET['slug'] ?? '';
        $recette = is_string($slug) ? (new RecipeManager($db))->getOneBySlug($slug) : null;

        // recette inconnue : on affiche la page 404
        if ($recette === null) {
            http_response_code(404);
            require RACINE_PATH.'/view/404.php';
            break;
        }

        // les ingrédients et les étapes de CETTE recette
        $ingredients = (new IngredientManager($db))->getByRecipe($recette->getId());
        $etapes = (new StepManager($db))->getByRecipe($recette->getId());

        // les commentaires, du plus récent au plus ancien, 10 à la fois
        // "Voir plus d'avis" : ?avis=2 affiche 20 commentaires, ?avis=3 en affiche 30...
        $pageAvis = isset($_GET['avis']) ? max(1, min(50, (int) $_GET['avis'])) : 1;
        $commentaires = (new CommentManager($db))->getByRecipe($recette->getId(), CommentManager::PAGE_SIZE * $pageAvis, 0);
        require RACINE_PATH.'/view/recette.php';
        break;

    // pages sans données à préparer
    case 'a-propos':
    case 'contact':
    case 'connection':
        require RACINE_PATH.'/view/'.$pg.'.php';
        break;

    // toute autre adresse : page 404
    default:
        http_response_code(404);
        require RACINE_PATH.'/view/404.php';
}
