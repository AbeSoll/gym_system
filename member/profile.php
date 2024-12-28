<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM members WHERE id='$user_id'");
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $sql = "UPDATE members SET name='$name', phone='$phone', address='$address' WHERE id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="form-container">
        <form method="POST">
            <h2>Update Profile</h2>
            <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
            <textarea name="address" required><?php echo $user['address']; ?></textarea>
            <button type="submit">Update</button>
            <?php if (isset($success)) echo "<p>$success</p>"; ?>
            <?php if (isset($error)) echo "<p>$error</p>"; ?>
        </form>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>
