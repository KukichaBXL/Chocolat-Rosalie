const emailVal = document.getElementById('email');
const pwdVal = document.getElementById('pwd');
const btnConnect = document.getElementById('btn-connect');

const regMail = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
const regPwd = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d).{8,}$/;

btnConnect.addEventListener('click', (e) => {
    e.preventDefault();
    getUserInput();
});

function getUserInput() {
    const email = emailVal.value;
    const password = pwdVal.value;

    emailVal.nextElementSibling.textContent =
        regMail.test(email) ? "" : "Mail invalide !";

    pwdVal.nextElementSibling.textContent =
        regPwd.test(password) ? "" : "Mot de passe invalide !";
}