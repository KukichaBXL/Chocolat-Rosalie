<?php
// path: controller/AdminController.php
// typage strict
declare(strict_types=1);

// Les actions réservées aux administrateurs : ajouter / supprimer une recette, masquer / réafficher un commentaire.
// Ce fichier n'est inclus par RouterController que si le rôle 'admin' est confirmé dans la base ($estAdmin).

use model\manager\CategoryManager;
use model\manager\CommentManager;
use model\manager\IngredientManager;
use model\manager\RecipeManager;
use model\manager\StepManager;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifierCsrf()) {
    switch (lireTexte('action')) {
        case 'ajouter-recette':
            // 1) on lit et on vérifie les champs
            $titre = lireTexte('title');
            $description = lireTexte('description');
            $difficulte = lireTexte('difficulty');
            $tempsPreparation = lireEntier('prepare_time', 1, 1440);
            $tempsCuisson = lireEntier('cook_time', 0, 1440);
            $portions = lireEntier('portions', 1, 50);
            $idCategorie = lireEntier('category', 1, PHP_INT_MAX);
            $ingredients = lireIngredients(lireTexte('ingredients'));

            // les étapes arrivent dans un tableau : etape_texte[0], etape_texte[1]... (les étapes vides sont ignorées)
            $etapes = [];
            $textes = is_array($_POST['etape_texte'] ?? null) ? array_slice($_POST['etape_texte'], 0, 20, true) : [];
            foreach ($textes as $position => $texte) {
                if (is_string($texte) && trim($texte) !== '') {
                    $etapes[] = ['position' => $position, 'texte' => trim($texte), 'photo' => null];
                }
            }

            $categorieManager = new CategoryManager($db);
            $recetteManager = new RecipeManager($db);

            if (mb_strlen($titre) < 3 || mb_strlen($titre) > 100) {
                $erreurs['title'] = 'Le titre doit contenir de 3 à 100 caractères.';
            } elseif (creerSlug($titre) === '') {
                $erreurs['title'] = 'Le titre doit contenir au moins une lettre ou un chiffre.';
            }
            if (mb_strlen($description) > 400) {
                $erreurs['description'] = 'La description ne doit pas dépasser 400 caractères.';
            }
            if ($tempsPreparation === null) {
                $erreurs['prepare_time'] = 'Indiquez un nombre de minutes entre 1 et 1440.';
            }
            if ($tempsCuisson === null) {
                $erreurs['cook_time'] = 'Indiquez un nombre de minutes entre 0 et 1440.';
            }
            if ($portions === null) {
                $erreurs['portions'] = 'Indiquez un nombre de personnes entre 1 et 50.';
            }
            if (!in_array($difficulte, ['facile', 'moyen', 'difficile'], true)) {
                $erreurs['difficulty'] = 'Choisissez une difficulté.';
            }
            if ($idCategorie === null || !$categorieManager->exists($idCategorie)) {
                $erreurs['category'] = 'Choisissez une catégorie.';
            }
            if ($ingredients === null || $ingredients === []) {
                $erreurs['ingredients'] = 'Indiquez de 1 à 40 ingrédients, un par ligne (100 caractères maximum chacun).';
            }
            if ($etapes === []) {
                $erreurs['etapes'] = 'Indiquez au moins une étape.';
            }

            // 2) les images : on les enregistre seulement si le reste est correct
            $fichiersEnregistres = [];
            if ($erreurs === []) {
                $photo = enregistrerImage($_FILES['photo'] ?? []);
                if ($photo['erreur'] !== null) {
                    $erreurs['photo'] = $photo['erreur'];
                } elseif ($photo['nom'] === null) {
                    $erreurs['photo'] = 'Ajoutez la photo de la recette.';
                } else {
                    $fichiersEnregistres[] = $photo['nom'];
                }

                foreach ($etapes as $i => $etape) {
                    // PHP range $_FILES['etape_photo'] par propriété : on retrouve le fichier de cette étape
                    $fichier = [];
                    foreach (['error', 'size', 'tmp_name'] as $propriete) {
                        $fichier[$propriete] = $_FILES['etape_photo'][$propriete][$etape['position']] ?? null;
                    }
                    $photoEtape = enregistrerImage($fichier);
                    if ($photoEtape['erreur'] !== null) {
                        $erreurs['etapes'] = 'Étape ' . ($i + 1) . ' : ' . $photoEtape['erreur'];
                    }
                    if ($photoEtape['nom'] !== null) {
                        $etapes[$i]['photo'] = $photoEtape['nom'];
                        $fichiersEnregistres[] = $photoEtape['nom'];
                    }
                }
            }
            // une erreur : on efface les images déjà enregistrées, la recette n'est pas créée
            if ($erreurs !== []) {
                foreach ($fichiersEnregistres as $nomFichier) {
                    unlink(RACINE_PATH . '/public/assets/' . $nomFichier);
                }
                break;
            }

            // 3) l'adresse de la recette : "Tarte au chocolat" donne tarte-au-chocolat (puis -2, -3... si elle existe déjà)
            $slug = creerSlug($titre);
            $numero = 2;
            while ($recetteManager->slugExists($slug)) {
                $slug = creerSlug($titre) . '-' . $numero;
                $numero++;
            }

            // 4) tout est enregistré, ou rien du tout (transaction)
            $ingredientManager = new IngredientManager($db);
            $etapeManager = new StepManager($db);
            $db->beginTransaction();
            try {
                $idRecette = $recetteManager->create(
                    $titre,
                    $description === '' ? null : $description,
                    $photo['nom'],
                    $tempsPreparation,
                    $tempsCuisson,
                    $portions,
                    $difficulte,
                    $utilisateur['id'],
                    $slug
                );
                $categorieManager->addRecipe($idCategorie, $idRecette);
                foreach ($ingredients as $ingredient) {
                    $idIngredient = $ingredientManager->findOrCreate($ingredient['name']);
                    $ingredientManager->addToRecipe($idRecette, $idIngredient, $ingredient['quantity'], $ingredient['unit']);
                }
                foreach ($etapes as $i => $etape) {
                    $etapeManager->add($idRecette, $i + 1, $etape['texte'], $etape['photo']);
                }
                $db->commit();
            } catch (Throwable $e) {
                $db->rollBack();
                foreach ($fichiersEnregistres as $nomFichier) {
                    unlink(RACINE_PATH . '/public/assets/' . $nomFichier);
                }
                throw $e; // la page d'erreur (public/index.php) s'en occupe
            }

            $_SESSION['message'] = 'La recette « ' . $titre . ' » est publiée.';
            header('Location: ' . RACINE_URL . 'recette/' . $slug);
            exit;

            // masquer une recette : elle reste dans la base (avec ses étapes, notes et commentaires), seul son statut change
        case 'masquer-recette':
        case 'afficher-recette':
            $idRecette = lireEntier('recipe_id', 1, PHP_INT_MAX);
            $recetteManager = new RecipeManager($db);
            if ($idRecette === null || !$recetteManager->exists($idRecette, true)) {
                $_SESSION['message'] = 'Cette recette n\'existe pas.';
            } elseif (lireTexte('action') === 'masquer-recette') {
                $recetteManager->setStatus($idRecette, 'masquée');
                $_SESSION['message'] = 'La recette est masquée : elle n\'apparaît plus sur le site.';
            } else {
                $recetteManager->setStatus($idRecette, 'publiée');
                $_SESSION['message'] = 'La recette est de nouveau visible.';
            }
            header('Location: ' . RACINE_URL . 'admin#recettes');
            exit;

            // masquer un commentaire : il reste dans la base, seul son statut change
        case 'masquer-commentaire':
        case 'afficher-commentaire':
            $idCommentaire = lireEntier('comment_id', 1, PHP_INT_MAX);
            $commentaireManager = new CommentManager($db);
            if ($idCommentaire === null || $commentaireManager->getById($idCommentaire) === null) {
                $_SESSION['message'] = 'Ce commentaire n\'existe pas.';
            } elseif (lireTexte('action') === 'masquer-commentaire') {
                $commentaireManager->setStatus($idCommentaire, 'banni');
                $_SESSION['message'] = 'Le commentaire est masqué.';
            } else {
                $commentaireManager->setStatus($idCommentaire, 'publié');
                $_SESSION['message'] = 'Le commentaire est de nouveau visible.';
            }
            header('Location: ' . RACINE_URL . 'admin#commentaires');
            exit;
    }
}
