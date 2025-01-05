<?php
session_start();
include '../includes/db.php';

// Ensure member is logged in
if (!isset($_SESSION['member_id']) || !isset($_GET['package_id'])) {
    header('Location: packages.php');
    exit();
}

$member_id = $_SESSION['member_id'];
$package_id = intval($_GET['package_id']);

// Fetch package details
$package_query = $conn->query("SELECT * FROM packages WHERE id = $package_id");
$package = $package_query->fetch_assoc();

if (!$package) {
    header('Location: packages.php');
    exit();
}

// Payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_name = $_POST['bank_name'];
    $price = $package['price'];
    $start_date = date('Y-m-d');
    $end_date = date('Y-m-d', strtotime("+{$package['duration']} months"));

    $conn->begin_transaction();
    try {
        // Insert into member_packages
        $conn->query("INSERT INTO member_packages (member_id, package_id, start_date, end_date, status)
                      VALUES ($member_id, $package_id, '$start_date', '$end_date', 'active')");

        // Insert into payments
        $conn->query("INSERT INTO payments (member_id, package_id, payment_status, amount, bank_name)
                      VALUES ($member_id, $package_id, 'paid', $price, '$bank_name')");

        $conn->commit();
        header('Location: payment_history.php?success=1');
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Payment failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Billing Payment</title>
    <link rel="stylesheet" href="../css/member.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .container h2 {
            text-align: center;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        label {
            font-weight: bold;
        }
        select, input, button {
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }
        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            display: none;
            z-index: 1000;
        }
        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .popup-content button {
            margin: 5px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Billing Payment</h2>
    <p><strong>Package:</strong> <?php echo $package['name']; ?></p>
    <p><strong>Price:</strong> RM<?php echo number_format($package['price'], 2); ?></p>
    <form id="paymentForm" method="POST">
        <label for="bank_name">Choose Bank:</label>
        <select name="bank_name" id="bank_name" required>
            <option value="">Select your bank</option>
            <option value="Maybank">Maybank</option>
            <option value="CIMB Bank">CIMB Bank</option>
            <option value="Public Bank">Public Bank</option>
            <option value="RHB Bank">RHB Bank</option>
            <option value="Hong Leong Bank">Hong Leong Bank</option>
            <option value="AmBank">AmBank</option>
            <option value="Bank Islam">Bank Islam</option>
            <option value="Bank Rakyat">Bank Rakyat</option>
            <option value="Bank Simpanan Nasional">Bank Simpanan Nasional (BSN)</option>
            <option value="HSBC Bank">HSBC Bank</option>
        </select>
        <button type="button" onclick="showConfirmation()">Proceed to Payment</button>
    </form>
</div>

<div class="popup" id="confirmationPopup">
    <div class="popup-content">
        <p>Are you sure you want to proceed with the payment?</p>
        <button id="confirmYes">Yes</button>
        <button id="confirmNo">No</button>
    </div>
</div>

<script>
    function showConfirmation() {
        document.getElementById('confirmationPopup').style.display = 'flex';
    }
    document.getElementById('confirmYes').addEventListener('click', function () {
        document.getElementById('paymentForm').submit();
    });
    document.getElementById('confirmNo').addEventListener('click', function () {
        document.getElementById('confirmationPopup').style.display = 'none';
    });
</script>
</body>
</html>
