let menuIcon = document.querySelector('#menu-icon');
let navigtion = document.querySelector('.navigation')

menuIcon.onclick = () => {
  menuIcon.classList.toggle('bx-x')
  navigtion.classList.toggle('active')
}

let sections = document.querySelectorAll('section');
let navLinks = document.querySelectorAll('header nav a');

window.addEventListener('scroll', () => {
  let top = window.scrollY;

  sections.forEach(sec => {
    let offset = sec.offsetTop - 200;
    let height = sec.offsetHeight;
    let id = sec.getAttribute('id');

    if (top >= offset && top < offset + height) {
      navLinks.forEach(link => {
        link.classList.remove('active');
      });
      document.querySelector(`header nav a[attr="${id}"]`).classList.add('active');
    }
  });
  let header = document.querySelector('header');

header.classList.toggle('sticky', window.scrollY > 100);
menuIcon.classList.remove('bx-x');
navigtion.classList.remove('active');
});



const links = document.querySelectorAll('a[attr]');
links.forEach((link) => {
  link.addEventListener('click', (event) => {
    event.preventDefault(); // Impede que o link seja seguido

    const targetSectionId = link.getAttribute('attr'); // Obtém o valor do atributo "attr" do link
    const targetSection = document.getElementById(targetSectionId); // Obtém a seção de destino usando o ID correspondente

    if (targetSection) {
      // Role a página até a seção de destino usando o método `scrollIntoView`
      targetSection.scrollIntoView({
        behavior: 'smooth', // Role suavemente
        block: 'start', // Alinhe o início da seção no topo da janela
      });
    }
  });
});