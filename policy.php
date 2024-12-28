<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policy</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Hero Section */
        .policy-hero-section {
            position: relative;
            background: url('images/policy-hero.jpg') no-repeat center center/cover;
            height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 20px;
            z-index: 0;
            overflow: hidden;
        }

        .policy-hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* Black overlay with 50% opacity */
            z-index: 0;
        }

        .policy-hero-content {
            position: relative;
            z-index: 1;
        }

        .policy-hero-content h1 {
            font-size: 48px;
            text-shadow: 0px 4px 6px rgba(0, 0, 0, 0.6);
        }

        .policy-hero-content p {
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
            text-shadow: 0px 4px 6px rgba(0, 0, 0, 0.6);
        }

        /* Policy Content */
        .policy-content {
            padding: 50px 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .policy-section {
            margin-bottom: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .policy-section h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #007bff;
        }

        .policy-section p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease, padding 0.5s ease;
        }

        /* Active State for Collapsible */
        .policy-section.active p {
            max-height: 200px; /* Adjust based on content */
            padding-top: 10px;
        }

        /* Scroll Up Button */
        #scrollUpBtn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99;
            background-color: rgba(128, 128, 128, 0.7);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 15px;
            width: 50px;
            height: 50px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out, background-color 0.3s ease;
        }

        #scrollUpBtn:hover {
            background-color: rgba(128, 128, 128, 0.9);
            transform: scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .policy-hero-section {
                height: 50vh;
            }

            .policy-hero-content h1 {
                font-size: 36px;
            }

            .policy-hero-content p {
                font-size: 16px;
            }

            .policy-section h2 {
                font-size: 22px;
            }

            .policy-section p {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .policy-hero-section {
                height: 40vh;
            }

            .policy-hero-content h1 {
                font-size: 28px;
            }

            .policy-hero-content p {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .policy-hero-section {
                height: 30vh;
            }

            .policy-hero-content h1 {
                font-size: 24px;
            }

            .policy-hero-content p {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <a href="/gym_system/index.php" class="logo">Gym Membership</a>
        <button class="hamburger" id="hamburger-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <ul class="nav-links" id="nav-links">
            <li><a href="/gym_system/index.php"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="/gym_system/about.php"><i class="fa fa-info-circle"></i> About</a></li>
            <li><a href="/gym_system/policy.php" class="active"><i class="fa fa-shield-alt"></i> Policy</a></li>
            <li><a href="/gym_system/auth/login.php"><i class="fa fa-sign-in-alt"></i> Login</a></li>
        </ul>
    </nav>
</header>

<div class="policy-hero-section">
    <div class="policy-hero-content">
        <h1>Our Policies</h1>
        <p>Learn about our policies to ensure a safe and fair experience for all members.</p>
    </div>
</div>

<main class="policy-content">
    <section class="policy-section collapsible">
        <h2>Privacy Policy</h2>
        <p>
            Your privacy is important to us. We collect personal information to provide a better service and ensure a safe experience. All personal data is handled securely and never shared without consent.
        </p>
    </section>

    <section class="policy-section collapsible">
        <h2>Terms & Conditions</h2>
        <p>
            By using our Gym Membership System, you agree to adhere to our rules and regulations. Violating any terms may result in suspension or termination of membership.
        </p>
    </section>

    <section class="policy-section collapsible">
        <h2>Membership Policies</h2>
        <p>
            Memberships are non-transferable. Any misuse of membership privileges may result in penalties or suspension.
        </p>
    </section>

    <section class="policy-section collapsible">
        <h2>Payment & Refund Policies</h2>
        <p>
            All payments are final. Refunds may be issued at the discretion of management in exceptional cases. Please contact our support team for assistance.
        </p>
    </section>

    <section class="policy-section collapsible">
        <h2>Contact Us</h2>
        <p>
            For any questions or concerns about our policies, please email us at <a href="mailto:support@gymmembership.com">support@gymmembership.com</a>.
        </p>
    </section>
</main>

<!-- Scroll Up Button -->
<button id="scrollUpBtn" title="Go to top">
    <i class="fas fa-chevron-up"></i>
</button>

<script src="js/scrollUpButton.js"></script>
<script src="js/main.js"></script>
<?php include 'includes/footer.php'; ?>
</body>
</html>