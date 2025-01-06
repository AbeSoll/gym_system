<?php
session_start();
include '../includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['member_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch current active package for the logged-in member
$member_id = $_SESSION['member_id'];
$active_package_query = $conn->query("
    SELECT member_packages.id AS package_id, member_packages.end_date 
    FROM member_packages 
    WHERE member_id = $member_id AND status = 'active'
");
$active_package = $active_package_query->fetch_assoc();

// Fetch all membership packages from the database
$package_query = $conn->query("SELECT id, name, price, duration FROM packages");
$packages = $package_query->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym Membership Packages</title>
    <link rel="stylesheet" href="../css/member.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        h1 {
            font-size: 32px;
            margin-bottom: 20px;
        }
        .packages {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }
        .package {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            text-align: center;
        }
        .package h3 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        .package p {
            margin: 5px 0;
            font-size: 16px;
        }
        .package .price {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }
        .package button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .package button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?><br><br>
<div class="container">
    <h1>Gym Membership Packages</h1>

    <!-- Active Package Alert -->
    <?php if ($active_package): ?>
        <p style="color: red; font-weight: bold;">
            You currently have an active package. You can only purchase a new package after your current one expires on 
            <strong><?php echo htmlspecialchars($active_package['end_date']); ?></strong>.
        </p>
    <?php endif; ?>

    <div class="packages">
        <?php foreach ($packages as $package): ?>
            <div class="package">
                <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                <p>Duration: <?php echo htmlspecialchars($package['duration']); ?> month(s)</p>
                <p class="price">RM <?php echo htmlspecialchars(number_format($package['price'], 2)); ?></p>
                <button 
                    <?php if ($active_package): ?> 
                        disabled 
                        style="background: #ccc; cursor: not-allowed;" 
                    <?php else: ?>
                        onclick="proceedToPayment(<?php echo $package['id']; ?>, '<?php echo $package['name']; ?>')"
                    <?php endif; ?>
                >
                    Join
                </button>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script>
    function proceedToPayment(packageId, packageName) {
        if (confirm(`Proceed to payment for the ${packageName} package?`)) {
            window.location.href = `payment.php?package_id=${packageId}`;
        }
    }
</script>
<script src="../js/member.js"></script>
<script src="../js/scrollUpButton.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
