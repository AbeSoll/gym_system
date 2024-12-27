document.addEventListener('DOMContentLoaded', function () {
    const hamburger = document.getElementById('hamburger-menu');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    hamburger.addEventListener('click', function () {
        sidebar.classList.toggle('show'); // Show/Hide sidebar
        mainContent.classList.toggle('sidebar-open'); // Adjust content margin
        this.classList.toggle('toggle'); // Animate hamburger
    });

    const logoutLink = document.querySelector('.sidebar a[href="../auth/logout.php"]');
    logoutLink.addEventListener('click', function (event) {
        event.preventDefault();
        showLogoutConfirmation();
    });

    function showLogoutConfirmation() {
        const confirmationPopup = document.createElement('div');
        confirmationPopup.classList.add('confirmation-popup');
        confirmationPopup.innerHTML = `
            <div class="popup-content">
                <p>Are you sure you want to logout?</p>
                <button id="confirm-yes">Yes</button>
                <button id="confirm-no">No</button>
            </div>
        `;
        document.body.appendChild(confirmationPopup);

        document.getElementById('confirm-yes').addEventListener('click', function () {
            window.location.href = "../auth/logout.php";
        });

        document.getElementById('confirm-no').addEventListener('click', function () {
            document.body.removeChild(confirmationPopup);
        });
    }
});
