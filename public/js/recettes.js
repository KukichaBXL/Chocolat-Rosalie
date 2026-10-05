const carousel = document.querySelector('.carousel');

if (carousel) {// On cherche le carrousel dans la page. Le if évite une erreur si le script est chargé sur une page qui n'en a pas.
  const viewport = carousel.querySelector('.carousel-viewport'); // la fenêtre ou apparait le carouselle et masque ce qui dépasse
  const track = carousel.querySelector('.recettes-grid');// block qui contient toutes les cartes 
  const cards = [...track.children]; // tableau des <li> 
  const prev = carousel.querySelector('.carousel-prev'); // Bouton Prev
  const next = carousel.querySelector('.carousel-next'); // Bouton Next 
  let index = 0; // numéro de la carte affiché en premier 

  function update() {
    const gap = parseFloat(getComputedStyle(track).columnGap) || 0; // Lit l'écart entre les cartes (20px, défini en CSS) pour ne pas l'écrire en dur dans le JS.
    const step = cards[0].offsetWidth + gap; // Distance à parcourir pour passer à la carte suivante : largeur d'une carte + l'écart.
    const visible = Math.max(1, Math.floor((viewport.clientWidth + gap) / step)); // Combien de cartes entières tiennent dans la fenêtre. Math.max(1, ...) garantit au moins 1.
    const max = Math.max(0, cards.length - visible); // Index maximum. Avec 5 cartes dont 3 visibles, on ne peut avancer que de 2, sinon on verrait du vide.

    index = Math.min(Math.max(index, 0), max);
    track.style.transform = `translateX(${-index * step}px)`;
    prev.disabled = index === 0;
    next.disabled = index >= max;
  }

  prev.addEventListener('click', () => { index--; update(); });
  next.addEventListener('click', () => { index++; update(); });
  window.addEventListener('resize', update);

  // swipe tactile
  let startX = 0;
  viewport.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
  viewport.addEventListener('touchend', (e) => {
    const diff = e.changedTouches[0].clientX - startX;
    if (Math.abs(diff) > 50) {
      index += diff < 0 ? 1 : -1;
      update();
    }
  });

  update();
}


// VERSION DU CAROUSELLE PLUS SIMPLE ********************


// const viewport = document.querySelector('.carousel-viewport');
// const card = viewport.querySelector('.recette-card');
// const pas = () => card.offsetWidth + 20; // largeur d'une carte + gap

// document.querySelector('.carousel-prev').addEventListener('click', () => {
//   viewport.scrollBy({ left: -pas() });
// });

// document.querySelector('.carousel-next').addEventListener('click', () => {
//   viewport.scrollBy({ left: pas() });
// });