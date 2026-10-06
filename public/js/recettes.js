
// VERSION DU CAROUSELLE PLUS SIMPLE ********************


const viewport = document.querySelector('.carousel-viewport');
const track = document.querySelector('.recettes-grid');
const prev = document.querySelector('.carousel-prev');
const next = document.querySelector('.carousel-next');

const pas = () => track.children[0].offsetWidth + 20;
let decalage = 0;

function update() {
  const max = track.scrollWidth - viewport.clientWidth;
  decalage = Math.min(Math.max(decalage, 0), max);
  track.style.transform = `translateX(${-decalage}px)`;
  prev.disabled = decalage === 0;
  next.disabled = decalage >= max;
}

prev.addEventListener('click', () => { decalage -= pas(); update(); });
next.addEventListener('click', () => { decalage += pas(); update(); });
window.addEventListener('resize', update);

update();