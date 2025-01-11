<?php
include '../includes/db.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $identifier = trim($_POST['identifier']);
    $password = trim($_POST['password']);

    if (empty($identifier) || empty($password)) {
        $error = "Please enter both identifier and password.";
    } else {
        // Initialize login flag
        $login_success = false;

        // Check if user is an admin
        $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($admin_id, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['role'] = 'admin';
                $_SESSION['admin_id'] = $admin_id;
                $login_success = true;
                header("Location: /gym_system/admin/dashboard.php");
                exit();
            }
        }
        $stmt->close();

        // If not admin, check member
        if (!$login_success) {
            $stmt = $conn->prepare("SELECT id, password FROM members WHERE email = ?");
            $stmt->bind_param("s", $identifier);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($member_id, $hashed_password);
                $stmt->fetch();

                if (password_verify($password, $hashed_password)) {
                    $_SESSION['role'] = 'member';
                    $_SESSION['member_id'] = $member_id;
                    header("Location: /gym_system/member/index.php");
                    exit();
                } else {
                    $error = "Invalid password. Please try again.";
                }
            } else {
                $error = "No account found with this identifier.";
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <a href="/gym_system/index.php" class="logo">Mankraft Fitness Center</a>
            <button class="hamburger" id="hamburger-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <ul class="nav-links" id="nav-links">
                <li><a href="/gym_system/index.php"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="/gym_system/about.php"><i class="fa fa-info-circle"></i> About</a></li>
                <li><a href="/gym_system/policy.php" class="active"><i class="fa fa-shield-alt"></i> Policy</a></li>
                <li><a href="/gym_system/auth/register.php"><i class="fa fa-user"></i> Sign up</a></li>
            </ul>
        </nav>
    </header>
    <div class="form-wrapper">
        <div class="form-container">
            <form method="POST">
                <h2>Login to Your Account</h2>

                <?php if (isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>

                <input type="text" name="identifier" placeholder="Email or Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>

                <center>
                <p class="register-link">
                    Don't have an account? <a href="/gym_system/auth/register.php">Sign up</a>
                </p>
                </center>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="../js/main.js"></script>
</body>
</html>
