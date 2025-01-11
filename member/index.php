<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../auth/login.php');
    exit();
}
// Fetch member details
$member_id = $_SESSION['member_id'];
$member_query = $conn->query("SELECT * FROM members WHERE id = $member_id");
$member = $member_query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mankraft Fitness Center</title>
    <link rel="stylesheet" href="../css/member.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="hero-section">
    <div class="hero-content">
        <h1>Welcome, <?php echo $member['name']; ?></h1>
        <p>Your fitness journey starts here. Join us today and achieve your health and wellness goals!</p>
        <a href="/gym_system/member/packages.php" class="btn-primary">Survey Your Membership Plan</a>
    </div>
</div>

<div class="about-section">
    <h2>About Us</h2>
    <p>
        We are dedicated to providing a comprehensive fitness experience for our members.
        Our mission is to make health and wellness accessible to everyone through
        exceptional facilities, professional guidance, and a supportive community.
    </p>
</div>

<div class="features-section">
    <h2>Why Choose Us?</h2>
    <div class="features">
        <div class="feature-card">
            <i class="fas fa-dumbbell"></i>
            <h3>State-of-the-Art Equipment</h3>
            <p>Experience top-notch gym equipment and facilities to help you stay fit.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-user"></i>
            <h3>Personal Trainers</h3>
            <p>Get personalized training plans with the guidance of professional trainers.</p>
        </div>
        <div class="feature-card">
            <i class="fas fa-heartbeat"></i>
            <h3>Health Monitoring</h3>
            <p>Track your progress with our integrated health monitoring system.</p>
        </div>
    </div>
</div>

<!-- Scroll Up Button -->
<button id="scrollUpBtn" title="Go to top">
    <i class="fas fa-chevron-up"></i> <!-- Font Awesome icon -->
</button>
<script src="../js/member.js"></script>
<script src="../js/scrollUpButton.js"></script>
<script src="../js/main.js"></script> <!-- External JavaScript -->

<?php include '../includes/footer.php'; ?>
