
// ON SCORLL ANIMATION PRESENTE SUR LE SITE WEB 

const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target); // l'animation ne se joue qu'une fois
    }
  });
}, { threshold: 0.15 }); // déclenche quand 15 % de l'élément est visible

document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));