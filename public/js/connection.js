// Vérifie le formulaire d'inscription avant l'envoi.
// le JavaScript peut être contourné, il sert seulement à aider le visiteur.

const formulaire = document.getElementById('form-inscription');

const regPseudo = /^[\p{L}\p{N}._-]{3,30}$/u;
const regMail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const regPwd = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,72}$/;

// écrit un message d'erreur sous un champ (ou l'efface si le message est vide)
function afficherErreur(idChamp, message) {
    document.querySelector(`[data-error-for="${idChamp}"]`).textContent = message;
}

formulaire.addEventListener('submit', (e) => {
    const pseudo = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const pwd = document.getElementById('pwd').value;
    const confirmation = document.getElementById('pwd-confirm').value;

    const erreurPseudo = regPseudo.test(pseudo) ? '' : "Nom d'utilisateur invalide (3 à 30 lettres, chiffres, . _ -).";
    const erreurMail = regMail.test(email) ? '' : 'Mail invalide !';
    const erreurPwd = regPwd.test(pwd) ? '' : 'Mot de passe invalide !';
    const erreurConfirm = pwd === confirmation ? '' : 'Les deux mots de passe ne sont pas identiques.';

    afficherErreur('username', erreurPseudo);
    afficherErreur('email', erreurMail);
    afficherErreur('pwd', erreurPwd);
    afficherErreur('pwd-confirm', erreurConfirm);

    // on bloque l'envoi seulement s'il reste une erreur
    if (erreurPseudo || erreurMail || erreurPwd || erreurConfirm) {
        e.preventDefault();
    }
});
