// Bootstrap handles navbar toggle automatically, but we can add custom functionality if needed
const navToggle = document.querySelector('.navbar-toggler');
const navMenu = document.querySelector('.navbar-collapse');

if (navToggle && navMenu) {
    // Bootstrap already handles the toggle, this is just for any additional custom behavior
    navToggle.addEventListener('click', () => {
        // Custom functionality can be added here if needed
        console.log('Navbar toggled');
    });
}