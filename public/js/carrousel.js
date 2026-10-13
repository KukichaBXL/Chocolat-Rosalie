/* Carrousels du site 
   1) page /recettes : défilement par translateX (.carousel-viewport)
   2) page recette   : avis + autres recettes (.carrousel, scroll-snap, défilement automatique) */

/* 1) Carrousel de la page /recettes  */
(function () {
    const viewport = document.querySelector('.carousel-viewport');
    const track = document.querySelector('.recettes-grid');
    const prev = document.querySelector('.carousel-prev');
    const next = document.querySelector('.carousel-next');
    if (!viewport || !track || !prev || !next || !track.children.length) { return; }

    const pas = () => track.children[0].offsetWidth + 20;
    let decalage = 0;

    function update() {
        const max = Math.max(track.scrollWidth - viewport.clientWidth, 0);
        decalage = Math.min(Math.max(decalage, 0), max);
        track.style.transform = `translateX(${-decalage}px)`;
        prev.disabled = decalage === 0;
        next.disabled = decalage >= max;
    }

    prev.addEventListener('click', () => { decalage -= pas(); update(); });
    next.addEventListener('click', () => { decalage += pas(); update(); });
    window.addEventListener('resize', update);
    update();
})();

/* Carrousels de la page recette */
document.querySelectorAll('.carrousel').forEach((carrousel) => {
    const piste = carrousel.querySelector('.carrousel-piste');
    const precedent = carrousel.querySelector('.carrousel-prec');
    const suivant = carrousel.querySelector('.carrousel-suiv');
    if (!piste || !precedent || !suivant) { return; }

    function aller(sens) {
        piste.scrollBy({ left: sens * piste.clientWidth, behavior: 'smooth' });
    }

    function majFleches() {
        const trop = piste.scrollWidth - piste.clientWidth > 4;
        precedent.hidden = !trop;
        suivant.hidden = !trop;
        precedent.disabled = piste.scrollLeft < 4;
        suivant.disabled = piste.scrollLeft + piste.clientWidth >= piste.scrollWidth - 4;
    }

    precedent.addEventListener('click', () => aller(-1));
    suivant.addEventListener('click', () => aller(1));
    piste.addEventListener('scroll', majFleches, { passive: true });
    window.addEventListener('resize', majFleches);
    piste.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') { aller(-1); }
        if (e.key === 'ArrowRight') { aller(1); }
    });
    /* nouvel avis ajouté en direct : on revient au début */
    new MutationObserver(() => { piste.scrollTo({ left: 0 }); majFleches(); })
        .observe(piste, { childList: true });
    majFleches();

    /* défilement automatique : toutes les 5 s, retour au début à la fin.
       Il s'arrête quand la souris ou le doigt est dessus, quand on tape dans la page,
       et si le visiteur a demandé moins d'animations. */
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
    let pause = false;
    ['mouseenter', 'focusin', 'touchstart'].forEach((nom) => carrousel.addEventListener(nom, () => { pause = true; }, { passive: true }));
    ['mouseleave', 'focusout', 'touchend'].forEach((nom) => carrousel.addEventListener(nom, () => { pause = false; }, { passive: true }));
    setInterval(() => {
        const nbPages = Math.round(piste.scrollWidth / piste.clientWidth);
        if (pause || document.hidden || nbPages < 2) { return; }
        const fin = piste.scrollLeft + piste.clientWidth >= piste.scrollWidth - 4;
        piste.scrollTo({ left: fin ? 0 : piste.scrollLeft + piste.clientWidth, behavior: 'smooth' });
    }, 5000);
});
