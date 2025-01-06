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

    $upload_dir = "../uploads/";
    $profile_picture = $member['profile_picture']; // Default to existing profile picture

    // Handle profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = uniqid() . "_" . basename($_FILES['profile_picture']['name']);
        $file_path = $upload_dir . $file_name;

        // Check file type and size (max 5MB)
        $file_type = pathinfo($file_path, PATHINFO_EXTENSION);
        if (!in_array(strtolower($file_type), ['jpg', 'jpeg', 'png'])) {
            $error = "Only JPG, JPEG, or PNG files are allowed.";
        } elseif ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
            $error = "File size must be less than 5MB.";
        } elseif (move_uploaded_file($file_tmp, $file_path)) {
            // If file upload is successful, update profile picture
            $profile_picture = $file_name;

            // Remove old profile picture if it exists
            if ($member['profile_picture'] && file_exists($upload_dir . $member['profile_picture'])) {
                unlink($upload_dir . $member['profile_picture']);
            }
        } else {
            $error = "Failed to upload the image.";
        }
    }

    // Validation and Update
    if (!isset($error)) {
        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format.";
        } elseif (!preg_match('/^[0-9]+$/', $phone)) {
            $error = "Phone number must contain only digits.";
        } else {
            $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, address = ?, profile_picture = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $name, $email, $phone, $address, $profile_picture, $member_id);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .img-account-profile {
            height: 10rem;
            width: 10rem;
            object-fit: cover;
        }
        .rounded-circle {
            border-radius: 50% !important;
        }
        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgb(33 40 50 / 15%);
        }
        .form-control {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container-xl px-4 mt-4">
    <div class="row">
        <div class="col-xl-4">
            <!-- Profile picture card -->
            <div class="card mb-4">
                <div class="card-header">Profile Picture</div>
                <div class="card-body text-center">
                    <img class="img-account-profile rounded-circle mb-2" 
                         src="<?php echo $member['profile_picture'] ? "../uploads/" . $member['profile_picture'] : '../images/default-avatar.png'; ?>" 
                         alt="Profile Picture">
                    <form id="profileForm" method="POST" enctype="multipart/form-data">
                        <input type="file" name="profile_picture" accept="image/*" class="form-control">
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <!-- Account details card -->
            <div class="card mb-4">
                <div class="card-header">Account Details</div>
                <div class="card-body">
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                        <input class="form-control" type="text" name="name" value="<?php echo htmlspecialchars($member['name']); ?>" placeholder="Full Name" required>
                        <input class="form-control" type="email" name="email" value="<?php echo htmlspecialchars($member['email']); ?>" placeholder="Email" required>
                        <input class="form-control" type="text" name="phone" value="<?php echo htmlspecialchars($member['phone']); ?>" placeholder="Phone" required>
                        <textarea class="form-control" name="address" placeholder="Address" required><?php echo htmlspecialchars($member['address']); ?></textarea>
                        <button type="button" class="btn btn-primary" id="saveChangesButton">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="confirmation-popup" id="confirmationPopup" style="display: none;">
    <div class="popup-content">
        <p>Are you sure you want to update your profile?</p>
        <button class="btn btn-success" id="confirmYes">Yes</button>
        <button class="btn btn-secondary" id="confirmNo">No</button>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
<script src="../js/main.js"></script>
<script src="../js/member.js"></script>
<script>
    // Show confirmation popup when Save Changes is clicked
    document.getElementById('saveChangesButton').addEventListener('click', function () {
        document.getElementById('confirmationPopup').style.display = 'flex';
    });

    // Handle confirmation popup actions
    document.getElementById('confirmYes').addEventListener('click', function () {
        document.getElementById('profileForm').submit();
    });

    document.getElementById('confirmNo').addEventListener('click', function () {
        document.getElementById('confirmationPopup').style.display = 'none';
    });
</script>

</body>
</html>
