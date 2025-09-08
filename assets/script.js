const navToggle = document.querySelector('.navbar-toggle');
const navMenu = document.querySelector('.navbar-menu');

if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
        navToggle.classList.toggle('active');
        navMenu.classList.toggle('active');
        console.log('Navbar toggled');
    });
}