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

// Fetch active package details
$package_query = $conn->query("
    SELECT packages.name AS package_name, member_packages.start_date, member_packages.end_date
    FROM member_packages
    JOIN packages ON member_packages.package_id = packages.id
    WHERE member_packages.member_id = $member_id AND member_packages.status = 'active'
    LIMIT 1
");
$active_package = $package_query->fetch_assoc();

// Fetch payment history count
$payment_query = $conn->query("SELECT COUNT(*) AS total_payments FROM payments WHERE member_id = $member_id");
$total_payments = $payment_query->fetch_assoc()['total_payments'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body{
            font-family: 'Poppins', sans-serif;
        }
        .dashboard-container {
            padding: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .card h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .card p {
            font-size: 16px;
        }
        .actions {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }
        .actions a {
            text-decoration: none;
            padding: 10px 20px;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .actions a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="navbar-left">
            <a href="dashboard.php" class="logo">Member Dashboard</a>
        </div>
        <div class="profile">
            <a href="profile.php"><i class="fas fa-user"></i> My Profile</a>
            <a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </nav>
</header>
<div class="dashboard-container">
    <h2>Welcome, <?php echo $member['name']; ?>!</h2>
    <div class="card">
        <h3>Membership Status</h3>
        <p>Status: <strong><?php echo ucfirst($member['status']); ?></strong></p>
        <?php if ($active_package): ?>
            <p>Package: <strong><?php echo $active_package['package_name']; ?></strong></p>
            <p>Start Date: <?php echo $active_package['start_date']; ?></p>
            <p>End Date: <?php echo $active_package['end_date']; ?></p>
        <?php else: ?>
            <p>No active package. <a href="package.php">View Plans</a></p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Quick Navigation</h3>
        <div class="actions">
            <a href="profile.php">Profile</a>
            <a href="package.php">View Packages</a>
            <a href="payment.php">Payment History</a>
        </div>
    </div>
    <div class="card">
        <h3>Payment Summary</h3>
        <p>Total Payments Made: <?php echo $total_payments; ?></p>
        <p>Total Amount Paid: RM<?php // Add logic to calculate total amount ?></p>
    </div>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Gym Membership System. All rights reserved.</p>
</footer>
</body>
</html>
