// --- Navbar Scroll and Mobile Toggle Logic ---
const navbar = document.getElementById('main-navbar');
const burger = document.getElementById('burger-menu');
const navLinks = document.getElementById('nav-links');
const hero = document.getElementById('hero');

// 1. Scroll Effect for Navbar
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});

// 2. Mobile Menu Toggle
burger.addEventListener('click', () => {
    burger.classList.toggle('toggle');
    navLinks.classList.toggle('nav-active');
});

// Close mobile menu when a link is clicked
document.querySelectorAll('#nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        // Only close if the menu is open (on mobile)
        if (window.innerWidth <= 768) {
            burger.classList.remove('toggle');
            navLinks.classList.remove('nav-active');
        }
    });
});