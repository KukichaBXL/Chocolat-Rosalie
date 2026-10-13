// Page d'administration : aperçu des photos choisies et bouton "Ajouter une étape".

// quand on choisit une image, elle remplace le cadre vide (.photo-vide)
function brancherApercu(champ) {
    champ.addEventListener('change', () => {
        const cadre = champ.closest('.photo-slot').querySelector('.photo-vide');
        const fichier = champ.files[0];
        cadre.style.backgroundImage = fichier ? `url(${URL.createObjectURL(fichier)})` : '';
        cadre.style.backgroundSize = 'cover';
        cadre.style.backgroundPosition = 'center';
    });
}
document.querySelectorAll('.photo-slot input[type="file"]').forEach(brancherApercu);

// "Ajouter une étape" : on copie la dernière étape et on vide ses champs
const listeEtapes = document.getElementById('liste-etapes');

// (ces lignes ne servent que sur le formulaire de recette : sur les autres onglets il n'y a pas de bouton « Ajouter une étape »)
document.getElementById('ajouter-etape')?.addEventListener('click', () => {
    const nombre = listeEtapes.children.length;
    if (nombre >= 20) {
        return; // le serveur n'accepte que 20 étapes
    }
    const copie = listeEtapes.lastElementChild.cloneNode(true);
    const texte = copie.querySelector('textarea');
    texte.value = '';
    texte.placeholder = 'Étape ' + (nombre + 1);
    const photo = copie.querySelector('input[type="file"]');
    photo.value = '';
    copie.querySelector('.photo-vide').style.backgroundImage = '';
    brancherApercu(photo);
    listeEtapes.appendChild(copie);
});

// "Ajouter un ingrédient" : on copie la dernière ligne (quantité, unité, nom) et on vide ses champs
const listeIngredients = document.getElementById('liste-ingredients');

document.getElementById('ajouter-ingredient')?.addEventListener('click', () => {
    if (listeIngredients.children.length >= 40) {
        return; // le serveur n'accepte que 40 ingrédients
    }
    const copie = listeIngredients.lastElementChild.cloneNode(true);
    copie.querySelectorAll('input').forEach((champ) => (champ.value = ''));
    copie.querySelector('select').selectedIndex = 0;
    listeIngredients.appendChild(copie);
    copie.querySelector('input').focus();
});

// le bouton × retire la ligne (la dernière ligne restante est seulement vidée)
listeIngredients?.addEventListener('click', (e) => {
    const bouton = e.target.closest('.ingredient-retirer');
    if (!bouton) {
        return;
    }
    const ligne = bouton.closest('.ingredient-ligne');
    if (listeIngredients.children.length > 1) {
        ligne.remove();
    } else {
        ligne.querySelectorAll('input').forEach((champ) => (champ.value = ''));
        ligne.querySelector('select').selectedIndex = 0;
    }
});

// demande une confirmation avant un bouton important : <button data-confirm="Question ?">
document.querySelectorAll('button[data-confirm]').forEach((bouton) => {
    bouton.addEventListener('click', (e) => {
        if (!confirm(bouton.dataset.confirm)) {
            e.preventDefault();
        }
    });
});

// demande une confirmation avant une suppression : <form data-confirm="Question ?">
document.querySelectorAll('form[data-confirm]').forEach((formulaire) => {
    formulaire.addEventListener('submit', (e) => {
        if (!confirm(formulaire.dataset.confirm)) {
            e.preventDefault();
        }
    });
});
