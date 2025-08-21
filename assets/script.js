const navToggle = document.querySelector('.navbar-toggle');
const navMenu = document.querySelector('.navbar-menu');

navToggle.addEventListener('click', ()=>{
    navToggle.classList.toggle('active');
    navMenu.classList.toggle('active');
});