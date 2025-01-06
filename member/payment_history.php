<?php
session_start();
include '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch member ID
$member_id = $_SESSION['member_id'];

try {
    // Fetch payment history
    $query = "
        SELECT 
            payments.id, 
            packages.name AS package_name, 
            payments.amount, 
            payments.bank_name, 
            payments.payment_date, 
            payments.payment_status 
        FROM payments
        JOIN packages ON payments.package_id = packages.id
        WHERE payments.member_id = ?
        ORDER BY payments.payment_date DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $payments = $result->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    $error_message = "An error occurred while fetching payment history. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History</title>
    <link rel="stylesheet" href="../css/member.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: #fff;
            font-weight: bold;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .status-paid {
            color: green;
            font-weight: bold;
        }
        .status-canceled {
            color: red;
            font-weight: bold;
        }
        .status-pending {
            color: orange;
            font-weight: bold;
        }
        .no-history {
            text-align: center;
            font-size: 18px;
            color: #888;
            margin-top: 20px;
        }
        .error-message {
            color: red;
            text-align: center;
            font-size: 18px;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?><br><br><br>
<div class="container">
    <h1>Payment History</h1>
    <?php if (isset($error_message)): ?>
        <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
    <?php elseif (count($payments) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Package</th>
                    <th>Amount (RM)</th>
                    <th>Bank Type</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $index => $payment): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($payment['package_name']); ?></td>
                        <td><?php echo number_format($payment['amount'], 2); ?></td>
                        <td><?php echo !empty($payment['bank_name']) ? htmlspecialchars($payment['bank_name']) : 'N/A'; ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                        <td class="<?php 
                            if ($payment['payment_status'] === 'paid') echo 'status-paid';
                            elseif ($payment['payment_status'] === 'canceled') echo 'status-canceled';
                            else echo 'status-pending';
                        ?>">
                            <?php echo ucfirst($payment['payment_status']); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-history">No payment history found.</p>
    <?php endif; ?>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Gym Membership System. All rights reserved.</p>
</footer>
<!-- Scroll Up Button -->
<button id="scrollUpBtn" title="Go to top">
    <i class="fas fa-chevron-up"></i> <!-- Font Awesome icon -->
</button>
<script src="../js/member.js"></script>
<script src="../js/scrollUpButton.js"></script>
<script src="../js/main.js"></script> <!-- External JavaScript -->
</body>
</html>