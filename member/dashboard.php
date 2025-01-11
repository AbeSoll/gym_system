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

// Determine member status
$status_query = $conn->query("
    SELECT 
        CASE 
            WHEN EXISTS (
                SELECT 1 
                FROM member_packages 
                WHERE member_id = $member_id AND status = 'active'
            ) THEN 'active'
            WHEN EXISTS (
                SELECT 1 
                FROM member_packages 
                WHERE member_id = $member_id AND status = 'expired'
            ) THEN 'expired'
            ELSE 'inactive'
        END AS current_status
");
$current_status = $status_query->fetch_assoc()['current_status'];

// Fetch active package details
$active_package_query = $conn->query("
    SELECT 
        packages.name AS package_name, 
        member_packages.start_date, 
        member_packages.end_date 
    FROM member_packages
    JOIN packages ON member_packages.package_id = packages.id
    WHERE member_packages.member_id = $member_id
    AND member_packages.status = 'active'
    LIMIT 1
");
$active_package = $active_package_query->fetch_assoc();

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
    <style>
        .status-active { color: green; font-weight: bold; }
        .status-inactive { color: red; font-weight: bold; }
        .status-expired { color: orange; font-weight: bold; }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($member['name']); ?>!</h2>
    <div class="card">
        <h3>Membership Status</h3>
        <p>Status: 
            <strong class="status-<?php echo $current_status; ?>">
                <?php echo ucfirst($current_status); ?>
            </strong>
        </p>
        <?php if ($current_status === 'active' && $active_package): ?>
            <p>Package: <strong><?php echo htmlspecialchars($active_package['package_name']); ?></strong></p>
            <p>Start Date: <?php echo htmlspecialchars($active_package['start_date']); ?></p>
            <p>End Date: <?php echo htmlspecialchars($active_package['end_date']); ?></p>
        <?php elseif ($current_status === 'inactive'): ?>
            <p>No active package. <a href="packages.php">View Plans</a></p>
        <?php elseif ($current_status === 'expired'): ?>
            <p>Your last package has expired. <a href="packages.php">Renew Membership</a></p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3>Quick Navigation</h3>
        <div class="actions">
            <a href="profile.php">Profile</a>
            <a href="packages.php">View Packages</a>
            <a href="payment_history.php">Payment History</a>
        </div>
    </div>
    <div class="card">
        <h3>Payment Summary</h3>
        <?php if ($total_payments > 0): ?>
            <p>Total Payments Attempt: <?php echo $total_payments; ?></p>
            <p>Total Amount Paid: RM<?php echo number_format($total_amount, 2); ?></p>
        <?php else: ?>
            <p>No payments made yet. <a href="packages.php">Join a package</a> to start your fitness journey!</p>
        <?php endif; ?>
    </div>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Mankraft Fitness Center. All rights reserved.</p>
</footer>
<script src="../js/member.js"></script>
<script src="../js/scrollUpButton.js"></script>
<script src="../js/main.js"></script> <!-- External JavaScript -->
</body>
</html>