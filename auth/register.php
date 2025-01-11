<?php
include '../includes/db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $gender = trim($_POST['gender']);
    $accept_terms = isset($_POST['accept_terms']);

    // Validate inputs
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($address) || empty($gender)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif (!$accept_terms) {
        $error = "You must accept the terms and policies.";
    } else {
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Validate email uniqueness
        $check_email = $conn->prepare("SELECT email FROM members WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $error = "Email is already registered. Please use another email.";
        } else {
            // Insert new member into the database
            $stmt = $conn->prepare("INSERT INTO members (name, email, password, phone, address, gender, status) VALUES (?, ?, ?, ?, ?, ?, 'inactive')");
            $stmt->bind_param("ssssss", $name, $email, $password_hashed, $phone, $address, $gender);

            if ($stmt->execute()) {
                $success = "Registration successful! Redirecting to login...";
                header("Refresh: 2; url=login.php");
            } else {
                $error = "Error: Unable to register. Please try again later.";
            }

            $stmt->close();
        }

        $check_email->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../js/validation.js" defer></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <div class="form-wrapper">
        <div class="form-container">
            <form id="registerForm" method="POST">
                <h2>Create an Account</h2>

                <?php if (isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php elseif (isset($success)): ?>
                    <p class="success"><?php echo $success; ?></p>
                <?php endif; ?>

                <input type="text" name="name" id="name" placeholder="Full Name" required>
                <input type="email" name="email" id="email" placeholder="Email Address" required>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <input type="text" name="phone" id="phone" placeholder="Phone Number" required>
                <textarea name="address" id="address" placeholder="Address" required></textarea>

                <div class="gender-selection">
                    <label>Gender:</label>
                    <div class="gender-options">
                        <input type="radio" name="gender" id="male" value="male" required>
                        <label for="male">Male</label>
                        <input type="radio" name="gender" id="female" value="female" required>
                        <label for="female">Female</label>
                    </div>
                </div>

                <div class="checkbox-container">
                    <input type="checkbox" name="accept_terms" id="accept_terms" required>
                    <label for="accept_terms">
                        I accept the <a href="/gym_system/policy.php">terms and policies</a>
                    </label>
                </div>
                <div class="checkbox-container">
                    <input type="checkbox" name="accept_promotions" id="accept_promotions" checked>
                    <label for="accept_promotions">
                        I would like to receive promotions and updates.
                    </label>
                </div>
                <button type="submit">Register</button>

                <center>
                <p class="login-link">
                    Already have an account? <a href="/gym_system/auth/login.php">Login</a>
                </p>
                </center>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="../js/main.js"></script> <!-- External JavaScript -->
</body>
</html>