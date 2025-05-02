// Simple script to show dynamic features like form validation, smooth scroll, or testimonials slider
document.addEventListener("DOMContentLoaded", () => {
    // Example of a smooth scroll effect for "Book Now" button
    const bookNowButton = document.querySelector('.cta-button');
    bookNowButton.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector('#car-filters').scrollIntoView({ behavior: 'smooth' });
    });
});
