// dark mode : le bouton lune / soleil change le thème et le mémorise
// Chargé par l'en-tête (header.php) : fonctionne donc sur TOUTES les pages, y compris fiche recette et admin.
(() => {
    const boutonTheme = document.querySelector(".theme-toggle");
    if (!boutonTheme) return;
    const iconeTheme = boutonTheme.querySelector("i");

    function afficherIcone() {
        const sombre = document.documentElement.dataset.theme === "dark";
        if (iconeTheme) iconeTheme.className = sombre ? "fa fa-sun-o" : "fa fa-moon-o";
    }

    function changerTheme() {
        const sombre = document.documentElement.dataset.theme !== "dark";
        if (sombre) {
            document.documentElement.dataset.theme = "dark";
        } else {
            delete document.documentElement.dataset.theme;
        }
        try {
            localStorage.setItem("theme", sombre ? "dark" : "light");
        } catch (e) {}
        afficherIcone();
    }

    boutonTheme.addEventListener("click", () => {
        // le fondu est désactivé si le visiteur a demandé moins d'animations dans son système
        const mouvementReduit = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
        if (document.startViewTransition && !mouvementReduit) {
            document.startViewTransition(changerTheme); // fondu entre l'ancien et le nouveau thème
        } else {
            changerTheme();
        }
    });

    afficherIcone();
})();
