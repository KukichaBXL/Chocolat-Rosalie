
// ON SCORLL ANIMATION PRESENTE SUR LE SITE WEB 

const observer = new IntersectionObserver((entries) => { // On déclare une variable Intersection... qui permet d'observer les changements de manière asynchrone sur la cible aui est la page web
  entries.forEach((entry) => {
    if (entry.isIntersecting) { // isIntersecting est une propriété de Intersection qui renvoie true ou false dépendent de ce qui est afficher sur la page 
      entry.target.classList.add('visible'); // si True on rend visible les éléments de la partie de la page qui s'affiche devant nous
      observer.unobserve(entry.target); // l'animation ne se joue qu'une fois
    }
  });
}, { threshold: 0.15 }); // déclenche quand 15 % de l'élément est visible

document.querySelectorAll('.reveal').forEach((el) => observer.observe(el));


// MAP Location and display (seulement sur la page qui a un élément #map)
if (document.getElementById('map')) {
  var map = L.map('map').setView([50.8503, 4.3517], 13);

  L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  L.marker([50.8503, 4.3517]).addTo(map) // pointer sur la map 
      .bindPopup('Nous sommes ici!')
      .openPopup();
}
