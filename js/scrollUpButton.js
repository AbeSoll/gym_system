// Get the button
const scrollUpBtn = document.getElementById("scrollUpBtn");

// Show the button when scrolling down
window.onscroll = function () {
    if (document.body.scrollTop > 200 || document.documentElement.scrollTop > 200) {
        scrollUpBtn.style.display = "flex"; // Ensure it's flex to center the icon
        scrollUpBtn.style.opacity = "1"; // Make it visible
    } else {
        scrollUpBtn.style.opacity = "0"; // Make it invisible
        setTimeout(() => (scrollUpBtn.style.display = "none"), 300); // Delay hiding to match opacity animation
    }
};

// Scroll to the top when the button is clicked
scrollUpBtn.onclick = function () {
    window.scrollTo({
        top: 0,
        behavior: "smooth", // Smooth scroll
    });
};
