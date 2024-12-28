<?php
include '../includes/db.php';
session_start();

// Check if user is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get payment amount from form
    $amount = $_POST['amount'];

    // Insert payment into the database
    $sql = "INSERT INTO payments (member_id, payment_status, amount) VALUES ('$user_id', 'paid', '$amount')";
    if ($conn->query($sql) === TRUE) {
        $success = "Payment successful!";
    } else {
        $error = "Error processing payment: " . $conn->error;
    }
}

// Fetch all payments for the logged-in user
$payment_results = $conn->query("SELECT * FROM payments WHERE member_id = '$user_id'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Make Payment</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Make a Payment</h2>

        <!-- Payment Form -->
        <form method="POST">
            <label for="amount">Enter Payment Amount (RM):</label>
            <input type="number" name="amount" id="amount" placeholder="E.g. 100" required>
            <button type="submit">Pay Now</button>
        </form>

        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <!-- Display Payment History -->
        <h3>Your Payment History</h3>
        <table>
            <tr>
                <th>Payment ID</th>
                <th>Amount (RM)</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
            <?php while ($payment = $payment_results->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $payment['id']; ?></td>
                    <td><?php echo $payment['amount']; ?></td>
                    <td><?php echo ucfirst($payment['payment_status']); ?></td>
                    <td><?php echo date("d-m-Y H:i:s", strtotime($payment['payment_date'])); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
