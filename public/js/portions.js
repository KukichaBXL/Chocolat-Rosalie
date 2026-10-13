/* Portions modifiables sur la page d'une recette.
   Le calcul est le même qu'en PHP (controller/outils.php : quantiteTexte, libelleIngredient). */

const bloc = document.getElementById('portions');
if (bloc) {
    const base = Number(bloc.dataset.base);
    const sortie = document.getElementById('portions-nb');
    const moins = document.getElementById('portions-moins');
    const plus = document.getElementById('portions-plus');
    const mot = document.getElementById('portions-mot');
    let nb = base;

    // le JavaScript fonctionne : on affiche le sélecteur à la place du texte fixe
    bloc.hidden = false;
    document.getElementById('portions-fixe').hidden = true;

    // 1.5 devient "1,5" (les cuillères : "1½")
    const nombreFr = (n, fraction) => {
        if (fraction && n % 1 === 0.5) { return (n >= 1 ? Math.floor(n) : '') + '½'; }
        return String(Math.round(n * 100) / 100).replace('.', ',');
    };

    function quantiteTexte(quantite, unite) {
        unite = unite === 'gr' ? 'g' : (unite || '');
        if (unite === 'g' && quantite >= 1000) { quantite /= 1000; unite = 'kg'; }
        else if (unite === 'ml' && quantite >= 1000) { quantite /= 1000; unite = 'l'; }
        else if (unite === 'cl' && quantite >= 100) { quantite /= 100; unite = 'l'; }

        let fraction = false;
        if (unite === 'g' || unite === 'ml') {
            quantite = quantite < 10 ? Math.round(quantite * 10) / 10 : Math.round(quantite / 5) * 5; // on pèse à 5 g près
        } else if (unite === 'kg' || unite === 'l' || unite === 'cl') {
            quantite = Math.round(quantite * 100) / 100;
        } else if (unite.startsWith('cuillère')) {
            quantite = Math.max(0.5, Math.floor(quantite * 2 + 0.5) / 2);
            fraction = true;
        } else {
            quantite = Math.max(1, Math.floor(quantite + 0.5));
        }

        if (quantite >= 2 && ['pincée', 'gousse', 'sachet', 'cuillère à soupe', 'cuillère à café'].includes(unite)) {
            unite = unite.replace('cuillère', 'cuillères');
            if (['pincée', 'gousse', 'sachet'].includes(unite)) { unite += 's'; }
        }
        return (nombreFr(quantite, fraction) + ' ' + unite).trim();
    }

    function libelle(nom, quantite, unite) {
        const texte = quantiteTexte(quantite, unite);
        if (unite) {
            return texte + (/^[aeiouyhéèêàâîïôœ]/i.test(nom) ? " d'" : ' de ') + nom;
        }
        if (Math.max(1, Math.floor(quantite + 0.5)) >= 2 && !/[sxz]$/.test(nom)) { nom += 's'; }
        return texte + ' ' + nom;
    }

    function recalculer() {
        const facteur = nb / base;
        // la liste d'ingrédients : seulement la quantité
        document.querySelectorAll('.ing-qte[data-qte]').forEach((el) => {
            el.textContent = quantiteTexte(Number(el.dataset.qte) * facteur, el.dataset.unite);
        });
        // les étapes : la phrase complète
        document.querySelectorAll('.qte[data-qte]').forEach((el) => {
            el.textContent = libelle(el.dataset.nom, Number(el.dataset.qte) * facteur, el.dataset.unite);
        });
        sortie.textContent = nb;
        mot.textContent = nb > 1 ? 'personnes' : 'personne';
        moins.disabled = nb <= 1;
        plus.disabled = nb >= 50;
    }

    moins.addEventListener('click', () => { nb = Math.max(1, nb - 1); recalculer(); });
    plus.addEventListener('click', () => { nb = Math.min(50, nb + 1); recalculer(); });
    recalculer();
}
