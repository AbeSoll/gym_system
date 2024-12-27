document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("registerForm");

    form.addEventListener("submit", (e) => {
        const name = document.getElementById("name").value.trim();
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const phone = document.getElementById("phone").value.trim();
        const address = document.getElementById("address").value.trim();

        let errorMessage = "";

        // Basic validation
        if (name === "") errorMessage += "Name is required.\n";
        if (!email.includes("@") || email === "") errorMessage += "Enter a valid email address.\n";
        if (password.length < 6) errorMessage += "Password must be at least 6 characters long.\n";
        if (phone === "" || isNaN(phone)) errorMessage += "Enter a valid phone number.\n";
        if (address === "") errorMessage += "Address is required.\n";

        if (errorMessage) {
            alert(errorMessage);
            e.preventDefault();
        }
    });
});
