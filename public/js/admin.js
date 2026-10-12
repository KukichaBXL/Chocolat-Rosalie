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

document.getElementById('ajouter-etape').addEventListener('click', () => {
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

// demande une confirmation avant une suppression : <form data-confirm="Question ?">
document.querySelectorAll('form[data-confirm]').forEach((formulaire) => {
    formulaire.addEventListener('submit', (e) => {
        if (!confirm(formulaire.dataset.confirm)) {
            e.preventDefault();
        }
    });
});
