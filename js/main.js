// Get the hamburger button and nav links
const hamburgerMenu = document.getElementById("hamburger-menu");
const navLinks = document.getElementById("nav-links");

// Toggle the "active" class on the hamburger and nav links
hamburgerMenu.addEventListener("click", () => {
    hamburgerMenu.classList.toggle("active");
    navLinks.classList.toggle("active");
});

// Toggle collapsible policy sections
const policySections = document.querySelectorAll(".policy-section");

policySections.forEach((section) => {
    section.addEventListener("click", () => {
        const content = section.querySelector("p");
        if (section.classList.contains("active")) {
            content.style.maxHeight = null;
            content.style.paddingTop = null;
        } else {
            content.style.maxHeight = content.scrollHeight + "px";
            content.style.paddingTop = "10px";
        }
        section.classList.toggle("active");
    });
});

// Add interactive effects to navbar links
const navLinksItems = document.querySelectorAll(".nav-links li a");

navLinksItems.forEach((link) => {
    link.addEventListener("mouseenter", () => {
        link.style.color = "#002c51"; // Change color on hover
    });

    link.addEventListener("mouseleave", () => {
        link.style.color = ""; // Reset color when not hovering
    });
});