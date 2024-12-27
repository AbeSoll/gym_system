<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM members WHERE id='$user_id'");
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <h2>Welcome, <?php echo $user['name']; ?></h2>
        <p>Email: <?php echo $user['email']; ?></p>
        <p>Phone: <?php echo $user['phone']; ?></p>
        <p>Address: <?php echo $user['address']; ?></p>
        <a href="profile.php" class="btn">Update Profile</a>
        <a href="payment.php" class="btn">Make Payment</a>
        <a href="../auth/logout.php" class="btn">Logout</a>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
