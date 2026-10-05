const carousel = document.querySelector('.carousel');

if (carousel) {
  const viewport = carousel.querySelector('.carousel-viewport');
  const track = carousel.querySelector('.recettes-grid');
  const cards = [...track.children];
  const prev = carousel.querySelector('.carousel-prev');
  const next = carousel.querySelector('.carousel-next');
  let index = 0;

  function update() {
    const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
    const step = cards[0].offsetWidth + gap;
    const visible = Math.max(1, Math.floor((viewport.clientWidth + gap) / step));
    const max = Math.max(0, cards.length - visible);

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