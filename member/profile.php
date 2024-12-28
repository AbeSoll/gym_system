<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Location: ../auth/login.php');
    exit();
}

$member_id = $_SESSION['member_id'];
$member_query = $conn->query("SELECT * FROM members WHERE id = $member_id");
$member = $member_query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);

    if (empty($name) || empty($email) || empty($phone) || empty($address)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^[0-9]+$/', $phone)) {
        $error = "Phone number must contain only digits.";
    } else {
        $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $member_id);

        if ($stmt->execute()) {
            $success = "Profile updated successfully.";
            $member_query = $conn->query("SELECT * FROM members WHERE id = $member_id");
            $member = $member_query->fetch_assoc();
        } else {
            $error = "Failed to update profile. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../css/member.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px 150px;
            background: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group textarea {
            resize: none;
            height: 100px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        .confirmation-popup-profile {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }
        .confirmation-popup-profile .popup-content-profile {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .confirmation-popup-profile .popup-content-profile p {
            margin-bottom: 20px;
            font-size: 18px;
        }
        .confirmation-popup-profile .popup-content-profile button {
            margin: 0 10px;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .confirmation-popup-profile .popup-content-profile button#profile-confirm-yes {
            background-color: #007bff;
            color: white;
        }
        .confirmation-popup-profile .popup-content-profile button#profile-confirm-yes:hover {
            background-color: #0056b3;
        }
        .confirmation-popup-profile .popup-content-profile button#profile-confirm-no {
            background-color: #ccc;
            color: black;
        }
        .confirmation-popup-profile .popup-content-profile button#profile-confirm-no:hover {
            background-color: #999;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <a href="/gym_system/member/index.php" class="logo">Gym Membership</a>
        <div class="hamburger" id="hamburger-menu">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </div>
        <ul class="nav-links" id="nav-links">
            <li><a href="/gym_system/member/index.php"><i class="fa fa-home"></i> Home</a></li>
            <li class="dropdown">
                <a href="#" class="dropbtn"><i class="fa fa-user"></i> Member Menu <i class="fas fa-caret-down"></i></a>
                <ul class="dropdown-content">
                    <li><a href="/gym_system/member/dashboard.php">Dashboard</a></li>
                    <li><a href="/gym_system/member/profile.php">My Account</a></li>
                    <li><a href="/gym_system/member/package.php">Membership Plan</a></li>
                    <li><a href="/gym_system/member/payment.php">Payment History</a></li>
                </ul>
            </li>
            <li><a href="/gym_system/member/about.php"><i class="fa fa-info-circle"></i> About</a></li>
            <li><a href="/gym_system/member/policy.php"><i class="fa fa-shield-alt"></i> Policy</a></li>
            <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</header>
<div class="container">
    <h2>My Profile</h2>
    <?php if (isset($success)): ?>
        <p class="message success"><?php echo $success; ?></p>
    <?php elseif (isset($error)): ?>
        <p class="message error"><?php echo $error; ?></p>
    <?php endif; ?>
    <form id="profileForm" method="POST">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Phone</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <textarea id="address" name="address" required><?php echo htmlspecialchars($member['address']); ?></textarea>
        </div>
        <button type="button" class="btn" onclick="showConfirmationPopupProfile()">Update Profile</button>
    </form>
</div>
<div class="confirmation-popup-profile" id="confirmationPopupProfile">
    <div class="popup-content-profile">
        <p>Are you sure you want to update your profile?</p>
        <button id="profile-confirm-yes">Yes</button>
        <button id="profile-confirm-no">No</button>
    </div>
</div>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Gym Membership System. All rights reserved.</p>
</footer>
<script>
    const hamburger = document.getElementById('hamburger-menu');
    const navLinks = document.getElementById('nav-links');
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active');
    });

    function showConfirmationPopupProfile() {
        document.getElementById('confirmationPopupProfile').style.display = 'flex';
    }

    document.getElementById('profile-confirm-yes').addEventListener('click', function () {
        document.getElementById('profileForm').submit();
    });

    document.getElementById('profile-confirm-no').addEventListener('click', function () {
        document.getElementById('confirmationPopupProfile').style.display = 'none';
    });
</script>

<script src="../js/scrollUpButton.js"></script>
<script src="../js/main.js"></script>
<script src="../js/member.js"></script>
</body>
</html>
