<?php
// path: view/admin.php
$titre = 'Administration';
require RACINE_PATH.'/view/inc/header.php';
?>

<section class="container page-title">
    <h1>Ajouter une recette</h1>
    <p>Remplissez la fiche, puis ajoutez les photos.</p>
</section>

<form class="container admin-form" method="post" enctype="multipart/form-data">

    <!-- Infos générales -->
    
        <h2>La recette</h2>
        <label class="photo-slot photo-principale">
            <span class="photo-vide"><!-- aperçu photo principale --></span>
            <input type="file" name="photo" accept="image/*" required />
        </label>

        <div class="admin-champs">
            <div class="form-field">
                <input type="text" id="titre" name="titre" required maxlength="120" placeholder="Titre"/>
            </div>
            <div class="admin-ligne">
                <div class="form-field">
                    <input type="number" id="duree" name="duree" min="1" placeholder="Durée (min)"/>
                </div>
                <div class="form-field">
                    <input type="number" id="portions" name="portions" min="1" placeholder="Portions"/>
                </div>
            </div>
            <div class="form-field">
                <textarea id="description" name="description" maxlength="500" placeholder="Description"></textarea>
            </div>
            <div class="form-field">
                <textarea id="ingredients" name="ingredients" required placeholder="Ingrédients (un par ligne)"></textarea>
            </div>
        </div>
    

    <!-- Étapes -->
    
        <h2>Les étapes</h2>
        <ol class="admin-etapes">
            <li class="admin-etape">
                <label class="photo-slot">
                    <span class="photo-vide"><!-- aperçu étape 1 --></span>
                    <input type="file" name="etape_photo[]" accept="image/*" />
                </label>
                <div class="form-field">
                    <textarea id="etape1" name="etape_texte[]" required placeholder="Étape 1"></textarea>
                </div>
            </li>
            <li class="admin-etape">
                <label class="photo-slot">
                    <span class="photo-vide"><!-- aperçu étape 2 --></span>
                    <input type="file" name="etape_photo[]" accept="image/*"/>
                </label>
                <div class="form-field">
                    <textarea id="etape2" name="etape_texte[]" placeholder="Étape 2"></textarea>
                </div>
            </li>
            <li class="admin-etape">
                <label class="photo-slot">
                    <span class="photo-vide"><!-- aperçu étape 3 --></span>
                    <input type="file" name="etape_photo[]" accept="image/*" />
                </label>
                <div class="form-field">
                    <textarea id="etape3" name="etape_texte[]" placeholder="Étape 3"></textarea>
                </div>
            </li>
        </ol>
    

    <button type="submit" class="btn-pill">Publier la recette</button>
</form>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
