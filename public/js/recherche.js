// recherche en temps réel : la loupe déplie le champ, les résultats viennent de /api/recherche?q=...
// enveloppé dans une fonction : ses variables (formulaire, champ...) ne doivent pas entrer en conflit avec contact.js / connection.js
(() => {
    const racine = document.querySelector(".brand").getAttribute("href");
    const formulaire = document.querySelector(".recherche");
    const champ = document.getElementById("recherche-champ");
    const bouton = formulaire.querySelector(".recherche-btn");
    const liste = document.getElementById("recherche-resultats");
    let minuteur = null;
    let controleur = null;

    function ouvrir(ouvert) {
        formulaire.classList.toggle("ouvert", ouvert);
        bouton.setAttribute("aria-expanded", ouvert);
        if (ouvert) champ.focus();
        else fermerListe();
    }

    function fermerListe() {
        liste.hidden = true;
        liste.replaceChildren();
    }

    function ligne(texte, classe) {
        const li = document.createElement("li");
        li.className = classe;
        li.textContent = texte;
        return li;
    }

    function afficher(recettes) {
        liste.replaceChildren();
        if (recettes.length === 0) {
            liste.append(ligne("Aucune recette trouvée.", "recherche-vide"));
        }
        for (const r of recettes) {
            const li = document.createElement("li");
            li.setAttribute("role", "option");
            const a = document.createElement("a");
            a.href = racine + "recette/" + encodeURIComponent(r.slug);
            const titre = document.createElement("span");
            titre.textContent = r.title; // textContent : jamais interprété comme du code
            a.append(titre);
            if (r.category) {
                const cat = document.createElement("small");
                cat.textContent = r.category;
                a.append(cat);
            }
            li.append(a);
            liste.append(li);
        }
        liste.hidden = false;
    }

    async function chercher() {
        const texte = champ.value.trim();
        if (texte.length < 2) return fermerListe();
        controleur?.abort(); // une frappe plus récente annule la requête précédente
        controleur = new AbortController();
        try {
            const reponse = await fetch(racine + "api/recherche?q=" + encodeURIComponent(texte), { signal: controleur.signal });
            const json = await reponse.json();
            if (json.success) afficher(json.data.recipes);
            else fermerListe();
        } catch (e) {
            if (e.name !== "AbortError") {
                liste.replaceChildren(ligne("La recherche est indisponible.", "recherche-vide"));
                liste.hidden = false;
            }
        }
    }

    // la loupe : ouvre le champ ; une fois ouvert avec du texte, elle envoie le formulaire
    bouton.addEventListener("click", (e) => {
        if (!formulaire.classList.contains("ouvert")) {
            e.preventDefault();
            ouvrir(true);
        } else if (champ.value.trim() === "") {
            e.preventDefault();
            ouvrir(false);
        }
    });

    champ.addEventListener("input", () => {
        clearTimeout(minuteur);
        minuteur = setTimeout(chercher, 200); // on attend la fin de la frappe
    });

    // Entrée : va à la première recette trouvée ; flèches : se déplacer dans les résultats
    formulaire.addEventListener("submit", (e) => {
        const premier = liste.querySelector("a");
        if (premier) {
            e.preventDefault();
            location.href = premier.href;
        }
    });

    formulaire.addEventListener("keydown", (e) => {
        const liens = [...liste.querySelectorAll("a")];
        const i = liens.indexOf(document.activeElement);
        if (e.key === "Escape") {
            ouvrir(false);
            bouton.focus();
        } else if (e.key === "ArrowDown" && liens.length) {
            e.preventDefault();
            liens[(i + 1) % liens.length].focus();
        } else if (e.key === "ArrowUp" && liens.length) {
            e.preventDefault();
            (i <= 0 ? champ : liens[i - 1]).focus();
        }
    });

    // un clic ailleurs referme la recherche
    document.addEventListener("click", (e) => {
        if (!formulaire.contains(e.target)) ouvrir(false);
    });
})();
