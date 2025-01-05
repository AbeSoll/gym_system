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

// Fetch active package details and status
$package_query = $conn->query("
    SELECT 
        packages.name AS package_name, 
        member_packages.start_date, 
        member_packages.end_date, 
        member_packages.status AS package_status
    FROM member_packages
    JOIN packages ON member_packages.package_id = packages.id
    WHERE member_packages.member_id = $member_id
    AND member_packages.status = 'active'
    LIMIT 1
");
$active_package = $package_query->fetch_assoc();

// Fetch payment history count and total amount paid
$payment_query = $conn->query("SELECT COUNT(*) AS total_payments, SUM(amount) AS total_amount FROM payments WHERE member_id = $member_id");
$payment_data = $payment_query->fetch_assoc();
$total_payments = $payment_data['total_payments'] ?? 0;
$total_amount = $payment_data['total_amount'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../css/member.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="dashboard-container">
    <h2>Welcome, <?php echo $member['name']; ?>!</h2>
    <div class="card">
        <h3>Membership Status</h3>
        <?php if ($active_package): ?>
            <p>Status: <strong><?php echo ucfirst($active_package['package_status']); ?></strong></p>
            <p>Package: <strong><?php echo $active_package['package_name']; ?></strong></p>
            <p>Start Date: <?php echo $active_package['start_date']; ?></p>
            <p>End Date: <?php echo $active_package['end_date']; ?></p>
        <?php else: ?>
            <p>No active package. <a href="packages.php">View Plans</a></p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Quick Navigation</h3>
        <div class="actions">
            <a href="profile.php">Profile</a>
            <a href="packages.php">View Packages</a>
            <a href="payment.php">Payment History</a>
        </div>
    </div>
    <div class="card">
        <h3>Payment Summary</h3>
        <p>Total Payments Made: <?php echo $total_payments; ?></p>
        <p>Total Amount Paid: RM<?php echo number_format($total_amount, 2); ?></p>
    </div>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Gym Membership System. All rights reserved.</p>
</footer>
<script>
    // Hamburger Menu
    const hamburger = document.getElementById('hamburger-menu');
    const navLinks = document.getElementById('nav-links');
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });
</script>
<script src="../js/member.js"></script>
<script src="../js/scrollUpButton.js"></script>
<script src="../js/main.js"></script> <!-- External JavaScript -->
</body>
</html>

