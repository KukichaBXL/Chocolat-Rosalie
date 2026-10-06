<?php
// path: view/recette.php
$titre = 'Fondant chocolat de Rosalie';
require RACINE_PATH.'/view/inc/header.php';
?>

<article class="container recette">
    <div class="recette-top">
        <div class="recette-fiche">
            <p class="recette-surtitre">Recette</p>
            <h1>Fondant chocolat de Rosalie</h1>

            <h2>Ingrédients — 4 personnes</h2>
            <ul>
                <li>150 g de chocolat noir</li>
                <li>100 g de beurre</li>
                <li>3 œufs</li>
                <li>70 g de sucre</li>
                <li>50 g de farine</li>
                <li>4 c. à café de pâte à tartiner aux noisettes</li>
                <li>1 pincée de sel</li>
            </ul>

            <h2>Préparation</h2>
            <ol>
                <li>Préchauffe le four à 200 °C.</li>
                <li>Fais fondre le chocolat avec le beurre au bain-marie.</li>
                <li>Fouette les œufs avec le sucre, puis ajoute la farine et le sel.</li>
                <li>Incorpore le chocolat fondu jusqu'à obtenir une pâte homogène.</li>
                <li>Verse la moitié de la pâte dans 4 petits moules beurrés.</li>
                <li>Dépose une cuillère de pâte à tartiner au centre, puis recouvre avec le reste de pâte.</li>
                <li>Enfourne 9 à 11 minutes : le centre doit rester tremblotant.</li>
                <li>Laisse reposer 1 minute avant de démouler.</li>
            </ol>
        </div>

        <img class="recette-photo" src="<?= RACINE_URL ?>assets/recette-fondant.jpg" alt="Fondant au chocolat coulant" width="510" height="854" />
    </div>

    <section class="commentaires">
        <h2>Nos avis</h2>

        <ul>
            <li class="commentaire">
                <p class="commentaire-auteur">Julie M. <span>12 mars 2026</span></p>
                <p>Testé ce week-end, un vrai régal, merci pour la recette !</p>
            </li>
            <li class="commentaire">
                <p class="commentaire-auteur">Thomas D. <span>3 février 2026</span></p>
                <p>Le cœur coulant est parfait, j'ai suivi les temps à la lettre.</p>
            </li>
        </ul>

        <form class="commentaire-form" method="post">
            <div class="form-field">
                <input type="text" id="commentaire-sujet" name="sujet" maxlength="120" placeholder="Sujet (facultatif)"/>
            </div>
            <div class="form-field">
                <textarea id="commentaire-message" name="message" required minlength="3" maxlength="500" placeholder="Votre commentaire ..."></textarea>
            </div>
            <button type="submit" class="btn-pill">Publier le commentaire</button>
        </form>
    </section>
</article>

<?php require RACINE_PATH.'/view/inc/footer.php'; ?>
