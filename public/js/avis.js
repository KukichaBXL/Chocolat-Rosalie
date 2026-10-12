// Commentaires sans rechargement : ajouter, supprimer, "Voir plus d'avis".
// Les réponses du serveur sont en JSON (voir docs/API.md).

const section = document.getElementById('commentaires');

if (section) {
    const liste = document.getElementById('liste-avis');
    const aucunAvis = document.getElementById('aucun-avis');
    const formulaire = document.getElementById('form-avis');
    const voirPlus = document.getElementById('voir-plus');

    // envoie une requête à /api/... et renvoie la réponse JSON
    async function appeler(route, donnees) {
        const options = {};
        let adresse = section.dataset.url + route;
        if (donnees) {
            donnees.append('csrf_token', section.dataset.csrf);
            options.method = 'POST';
            options.body = donnees;
        }
        try {
            const reponse = await fetch(adresse, options);
            return await reponse.json();
        } catch (erreur) {
            return { success: false, message: 'Le serveur ne répond pas. Réessayez.', data: {}, errors: {} };
        }
    }

    // fabrique le <li> d'un commentaire reçu en JSON.
    // textContent (et non innerHTML) : un commentaire contenant du HTML ou du JavaScript s'affiche comme du simple texte
    function creerCommentaire(c) {
        const li = document.createElement('li');
        li.className = 'commentaire';
        li.dataset.id = c.id;
        li.dataset.author = c.authorId;

        const auteur = document.createElement('p');
        auteur.className = 'commentaire-auteur';
        auteur.textContent = c.author + ' ';
        const date = document.createElement('span');
        date.textContent = c.date;
        auteur.appendChild(date);
        // la note que cet auteur a donnée à la recette (vide s'il n'a pas noté)
        const note = document.createElement('span');
        note.className = 'commentaire-note';
        note.textContent = c.rate ? '★'.repeat(c.rate) + '☆'.repeat(5 - c.rate) : '';
        auteur.appendChild(note);
        li.appendChild(auteur);

        if (c.title) {
            const sujet = document.createElement('p');
            const fort = document.createElement('strong');
            fort.textContent = c.title;
            sujet.appendChild(fort);
            li.appendChild(sujet);
        }

        const texte = document.createElement('p');
        texte.textContent = c.text;
        li.appendChild(texte);

        if (c.canDelete) {
            const bouton = document.createElement('button');
            bouton.type = 'button';
            bouton.className = 'supprimer-avis';
            bouton.textContent = c.deleteLabel; // "Supprimer" pour l'auteur, "Masquer" pour l'administrateur
            li.appendChild(bouton);
        }
        return li;
    }

    // ----- ajouter un commentaire -----
    if (formulaire) {
        const message = document.getElementById('avis-message');
        formulaire.addEventListener('submit', async (e) => {
            e.preventDefault();
            const donnees = new FormData(formulaire);
            donnees.append('recipe_id', section.dataset.recipe);
            const json = await appeler('commenter', donnees);

            if (json.success) {
                liste.prepend(creerCommentaire(json.data.comment)); // en haut : le plus récent d'abord
                aucunAvis.hidden = true;
                formulaire.reset();
                message.textContent = '';
            } else {
                // le message général, ou les erreurs champ par champ
                message.textContent = Object.values(json.errors).join(' ') || json.message;
            }
        });
    }

    // ----- supprimer un commentaire (le <li> est repéré grâce à son data-id) -----
    liste.addEventListener('click', async (e) => {
        if (!e.target.classList.contains('supprimer-avis')) {
            return;
        }
        if (!confirm(e.target.textContent + ' ce commentaire ?')) {
            return;
        }
        const li = e.target.closest('li');
        const donnees = new FormData();
        donnees.append('comment_id', li.dataset.id);
        const json = await appeler('supprimer-commentaire', donnees);
        if (json.success) {
            li.remove();
            aucunAvis.hidden = liste.children.length > 0;
        } else {
            alert(json.message);
        }
    });

    // ----- voir plus d'avis : on demande les 10 suivants -----
    if (voirPlus) {
        voirPlus.addEventListener('click', async (e) => {
            e.preventDefault(); // pas de rechargement
            const json = await appeler('commentaires?recipe_id=' + section.dataset.recipe + '&offset=' + liste.children.length);
            if (json.success) {
                json.data.comments.forEach((c) => liste.appendChild(creerCommentaire(c)));
                if (!json.data.hasMore) {
                    voirPlus.remove(); // plus rien à charger
                }
            }
        });
    }
}
