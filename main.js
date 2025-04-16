
    const menuBtn = document.getElementById("menu-btn");
    const navLinks = document.getElementById("nav-links");
    const menuBtnIcon = menuBtn.querySelector("i");

    menuBtn.addEventListener("click", (e) => {
        navLinks.classList.toggle("open");
      
        const isOpen = navLinks.classList.contains("open");
        menuBtnIcon.setAttribute("class",isOpen ? "ri-close-line" : "ri-menu-line");

    });
navLinks.addEventListener("click", (e) => {
    navLinks.classList.remove("open");
    menuBtnIcon.setAttribute("class", "ri-menu-line");
})

const scrollRevealOption = {
    distance: "50px",
    duration: 1000,
    easing: "ease-in-out",
    reset: true
};


ScrollReveal().reveal(".header__img", {
    ...scrollRevealOption,
    origin: "top"
});

ScrollReveal().reveal(".header__content h1", {
    ...scrollRevealOption,
    delay: 500
});

ScrollReveal().reveal(".header__content p", {
    ...scrollRevealOption,
    delay: 1000
});

ScrollReveal().reveal(".header__links", {
    ...scrollRevealOption,
    delay: 1500
});

ScrollReveal().reveal(".steps__card",{
    ...scrollRevealOption,
    interval: 500,
});

ScrollReveal().reveal(".service__img",{
    ...scrollRevealOption,
    origin: "left",
});
ScrollReveal().reveal(".service__content  .section__subheader",{
    ...scrollRevealOption,
    delay: 500,
});
ScrollReveal().reveal(".service__content  .section__subheader",{
    ...scrollRevealOption,
    delay: 1000,
});
ScrollReveal().reveal(".service__list li", {
    ...scrollRevealOption,
    delay: 1500,
    interval: 500,
});
ScrollReveal().reveal(".experience__card", {
    duration: 1000,
    
    interval: 500,
});

ScrollReveal().reveal(".download__img", {
    ...scrollRevealOption,
    origin: "top"
});

ScrollReveal().reveal(".download__content .section__header", {
    ...scrollRevealOption,
    delay: 500
});

ScrollReveal().reveal(".download__content p", {
    ...scrollRevealOption,
    delay: 1000
});

ScrollReveal().reveal(".download__links", {
    ...scrollRevealOption,
    delay: 1500
});
