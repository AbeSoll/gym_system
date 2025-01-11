<?php
session_start();
include '../includes/db.php';

// Redirect if the admin is not logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Retrieve admin ID from session
$admin_id = $_SESSION['admin_id']; // Assuming admin ID is stored in session

// Hardcoded descriptions and benefits
$packageDescriptions = [
    'Basic' => [
        'Access to gym equipment only',
        'Open gym hours',
        'Locker facilities'
    ],
    'Pro' => [
        'Access to gym equipment and group classes',
        'Priority booking for classes',
        'Access to nutrition workshops',
        'Locker facilities'
    ],
    'Premium' => [
        'All access: equipment, classes, and personal trainer sessions',
        'Unlimited guest passes',
        'Access to premium locker rooms',
        'Free workout apparel',
        'Nutrition and wellness consultations'
    ]
];

// Handle Add/Update Package
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $name = $_POST['name'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];

    if ($id) {
        // Update package
        $stmt = $conn->prepare("UPDATE packages SET name = ?, price = ?, duration = ?, admin_id = ? WHERE id = ?");
        $stmt->bind_param("sdiii", $name, $price, $duration, $admin_id, $id);
        $stmt->execute();
    } else {
        // Add new package
        $stmt = $conn->prepare("INSERT INTO packages (name, price, duration, admin_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdii", $name, $price, $duration, $admin_id);
        $stmt->execute();
    }
    header('Location: manage_package.php');
    exit();
}

// Handle Delete Package
if (isset($_GET['delete'])) {
    $package_id = intval($_GET['delete']);
    $conn->query("DELETE FROM packages WHERE id = $package_id");
    header('Location: manage_package.php');
    exit();
}

// Fetch all packages associated with the logged-in admin
$packages = $conn->query("SELECT * FROM packages WHERE admin_id = $admin_id")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container_package {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            flex: 1; /* Allow this container to take up remaining space */
        }
        .package {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 12px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-edit {
            background-color: #007bff;
        }
        .btn-delete {
            background-color: #dc3545;
        }
        .btn-add {
            background-color: #28a745;
            margin-bottom: 20px;
        }
        .btn:hover {
            opacity: 0.9;
        }
        /* Popup Styling */
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
            z-index: 1000;
            display: none;
        }
        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            width: 400px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .popup-content input, .popup-content select {
            width: 100%;
            margin: 10px 0;
            padding: 10px 1px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .popup-content button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .popup-content button.close {
            background-color: #dc3545;
            color: white;
        }
        .popup-content button.cancel {
            background-color:rgb(2, 2, 255);
            color: white;
        }
        .popup-content button.save {
            background-color: #28a745;
            color: white;
        }
        .radio-group {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px; /* Adjust the spacing between radio buttons */
        }

        .radio-group label {
            margin: 0;
            font-size: 1rem;
        }

        .radio-group input[type="radio"] {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<header>
    <nav class="navbar">
        <div class="navbar-left">
            <button class="hamburger" id="hamburger-menu">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <a href="dashboard.php" class="logo">Admin Dashboard</a>
        </div>
        <div class="profile">
            <a href="#"><i class="fas fa-user"></i> Admin</a>
        </div>
    </nav>
</header>
<div class="dashboard-container">
<aside class="sidebar" id="sidebar">
    <ul><br>
        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="view_members.php"><i class="fas fa-users"></i> List Members</a></li>
        <li><a href="payments.php"><i class="fas fa-dollar-sign"></i> View Payments</a></li>
        <li><a href="manage_package.php"><i class="fas fa-cubes"></i> Manage Packages</a></li>
        <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside><br>
<div class="container_package">
    <h2>Manage Membership Packages</h2>
    <button class="btn btn-add" onclick="showForm()">+ Add Package</button>

    <?php foreach ($packages as $package): ?>
        <div class="package">
            <h3><?php echo htmlspecialchars($package['name']); ?></h3>
            <p><strong>Price:</strong> RM<?php echo number_format($package['price'], 2); ?></p>
            <p><strong>Duration:</strong> <?php echo $package['duration']; ?> month(s)</p>
            <p><strong>Benefits:</strong></p>
            <ul>
                <?php foreach ($packageDescriptions[$package['name']] as $benefit): ?>
                    <li><?php echo htmlspecialchars($benefit); ?></li>
                <?php endforeach; ?>
            </ul>
            <button class="btn btn-edit" onclick="showForm(<?php echo htmlspecialchars(json_encode($package)); ?>)">Edit</button>
            <button class="btn btn-delete" onclick="showDeletePopup(<?php echo $package['id']; ?>)">Delete</button>
        </div>
    <?php endforeach; ?>
</div>
<?php include '../includes/footer.php'; ?>
<!-- Popup for Adding/Editing Packages -->
<div class="popup" id="packagePopup">
    <div class="popup-content">
        <h3 id="popupTitle">Add Package</h3>
        <form id="packageForm" method="POST">
            <input type="hidden" name="id" id="packageId">
            <label>Package Name:</label>
            <div class="radio-group">
                <label>
                    <input type="radio" name="name" value="Basic" id="basic" required> Basic
                </label>
                <label>
                    <input type="radio" name="name" value="Pro" id="pro"> Pro
                </label>
                <label>
                    <input type="radio" name="name" value="Premium" id="premium"> Premium
                </label>
            </div>
            <input type="number" name="price" id="packagePrice" placeholder="Price (RM)" step="0.01" required>
            <input type="number" name="duration" id="packageDuration" placeholder="Duration (months)" required>
            <button type="submit" class="btn save">Save</button>
            <button type="button" class="btn close" onclick="closeForm()">Close</button>
        </form>
    </div>
</div>

<!-- Popup for Deleting Packages -->
<div class="popup" id="deletePopup">
    <div class="popup-content">
        <p>Are you sure you want to delete this package?</p>
        <form method="GET">
            <input type="hidden" name="delete" id="deleteId">
            <button type="submit" class="btn btn-delete">Delete</button>
            <button type="button" class="btn cancel" onclick="closeDeletePopup()">Cancel</button>
        </form>
    </div>
</div>
<script src="../js/admin.js"></script>
<script>
    function showForm(packageData = null) {
        const popup = document.getElementById('packagePopup');
        const form = document.getElementById('packageForm');
        const title = document.getElementById('popupTitle');

        if (packageData) {
            document.getElementById('packageId').value = packageData.id;
            document.querySelector(`input[name="name"][value="${packageData.name}"]`).checked = true;
            document.getElementById('packagePrice').value = packageData.price;
            document.getElementById('packageDuration').value = packageData.duration;
            title.innerText = 'Edit Package';
        } else {
            form.reset();
            title.innerText = 'Add Package';
        }

        popup.style.display = 'flex';
    }

    function closeForm() {
        document.getElementById('packagePopup').style.display = 'none';
    }

    function showDeletePopup(id) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deletePopup').style.display = 'flex';
    }

    function closeDeletePopup() {
        document.getElementById('deletePopup').style.display = 'none';
    }
</script>
</body>
</html>
