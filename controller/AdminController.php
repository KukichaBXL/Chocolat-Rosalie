<?php
// path: controller/AdminController.php
// typage strict
declare(strict_types=1);

// Les actions réservées aux administrateurs.
// Ce fichier n'est inclus par RouterController que si le rôle 'admin' est confirmé dans la base ($estAdmin).

use model\manager\CategoryManager;
use model\manager\IngredientManager;
use model\manager\RecipeManager;
use model\manager\StepManager;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifierCsrf()) {
    switch (lireTexte('action')) {
        case 'ajouter-recette':
            // on lit et on vérifie les champs
            $titre = lireTexte('title');
            $description = lireTexte('description');
            $difficulte = lireTexte('difficulty');
            $tempsPreparation = lireEntier('prepare_time', 1, 1440);
            $tempsCuisson = lireEntier('cook_time', 0, 1440);
            $portions = lireEntier('portions', 1, 50);
            $idCategorie = lireEntier('category', 1, PHP_INT_MAX);
            $ingredients = lireIngredients(lireTexte('ingredients'));

            // les étapes arrivent dans un tableau 
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

            // les images
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
                    // PHP range $_FILES['etape_photo'] par propriété 
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

            // l'adresse de la recette : "Tarte au chocolat" donne tarte-au-chocolat (puis -2, -3... si elle existe déjà)
            $slug = creerSlug($titre);
            $numero = 2;
            while ($recetteManager->slugExists($slug)) {
                $slug = creerSlug($titre) . '-' . $numero;
                $numero++;
            }

            // tout est enregistré, ou rien du tout (transaction)
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
    }
}
