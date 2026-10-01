// validation du formulaire de contact avant l'envoi
// (le serveur devra revérifier, le JavaScript peut être contourné)

const formulaire = document.querySelector(".contact-form");

// une expression régulière et un message par champ
const regles = {
    nom: [/^[\p{L}\s'-]{2,80}$/u, "Le nom doit contenir entre 2 et 80 lettres."],
    email: [/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/, "L'adresse email n'est pas valide (exemple : nom@domaine.be)."],
    sujet: [/^.{0,120}$/, "Le sujet ne doit pas dépasser 120 caractères."],
    message: [/^.{3,500}$/s, "Le message doit contenir entre 3 et 500 caractères."],
};

formulaire.addEventListener("submit", (event) => {
    let valide = true;

    for (const champ in regles) {
        const [regex, message] = regles[champ];
        const valeur = formulaire.elements[champ].value.trim();
        const zoneErreur = formulaire.querySelector(`[data-error-for="${champ}"]`);

        if (regex.test(valeur)) {
            zoneErreur.textContent = "";
        } else {
            zoneErreur.textContent = message;
            valide = false;
        }
    }

    // on bloque l'envoi tant qu'il reste une erreur
    if (!valide) {
        event.preventDefault();
    }
});
