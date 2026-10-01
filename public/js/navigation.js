// menu repliable : le bouton "Menu" n'apparaît que sur les petits écrans

const boutonMenu = document.querySelector(".nav-toggle");
const menu = document.querySelector(".main-nav");

boutonMenu.addEventListener("click", () => {
    const ouvert = menu.classList.toggle("ouvert");
    boutonMenu.setAttribute("aria-expanded", ouvert);
});
