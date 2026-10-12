// Notation par étoiles : aperçu au survol, clic pour noter, mise à jour de la moyenne sans recharger la page.

const notation = document.getElementById('notation');
const etoiles = notation ? notation.querySelectorAll('.etoile') : [];

if (etoiles.length > 0) {
    const message = document.getElementById('note-message');
    let maNote = Number(notation.dataset.note); // 0 = pas encore de vote

    // colore les étoiles de 1 à $jusqua
    function colorer(jusqua) {
        etoiles.forEach((etoile) => {
            etoile.classList.toggle('active', Number(etoile.dataset.valeur) <= jusqua);
        });
    }
    colorer(maNote);

    etoiles.forEach((etoile) => {
        // aperçu au survol, puis retour à la note enregistrée
        etoile.addEventListener('mouseenter', () => colorer(Number(etoile.dataset.valeur)));
        etoile.addEventListener('mouseleave', () => colorer(maNote));
        etoile.addEventListener('click', () => envoyer(Number(etoile.dataset.valeur)));
    });

    async function envoyer(note) {
        const donnees = new FormData();
        donnees.append('recipe_id', notation.dataset.recipe);
        donnees.append('rate', note);
        donnees.append('csrf_token', notation.dataset.csrf);

        try {
            const reponse = await fetch(notation.dataset.url, { method: 'POST', body: donnees });
            const json = await reponse.json();
            message.textContent = json.message;
            if (json.success) {
                maNote = json.data.rate;
                colorer(maNote);
                document.getElementById('note-moyenne').textContent = json.data.average.toFixed(1).replace('.', ',');
                document.getElementById('note-votes').textContent = json.data.votes;
                // les commentaires de cette personne affichent sa nouvelle note
                const etoilesTexte = '★'.repeat(maNote) + '☆'.repeat(5 - maNote);
                document.querySelectorAll('.commentaire[data-author="' + notation.dataset.user + '"] .commentaire-note')
                    .forEach((element) => { element.textContent = etoilesTexte; });
            }
        } catch (erreur) {
            message.textContent = 'Impossible d\'enregistrer la note. Réessayez.';
        }
    }
}
