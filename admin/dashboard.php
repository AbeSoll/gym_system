<?php
include '../includes/db.php';
session_start();

// Redirect if the admin is not logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Auto-update membership status based on the end_date in `member_packages`
$conn->query("
    UPDATE member_packages 
    SET status = 'expired' 
    WHERE end_date < CURDATE() AND status = 'active'
");

// Fetch dashboard data
$dashboardData = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM members) AS totalMembers,
        (SELECT SUM(amount) FROM payments WHERE payment_status = 'paid') AS totalPayments,
        (SELECT COUNT(*) FROM members WHERE status = 'active') AS activeMembers,
        (SELECT COUNT(*) FROM members WHERE status = 'inactive') AS inactiveMembers
")->fetch_assoc();

$totalMembers = $dashboardData['totalMembers'];
$totalPayments = $dashboardData['totalPayments'] ?? 0;
$activeMembers = $dashboardData['activeMembers'];
$inactiveMembers = $dashboardData['inactiveMembers'];

// Fetch monthly payments for the graph
$monthlyPaymentsQuery = $conn->query("
    SELECT MONTHNAME(payment_date) AS month, SUM(amount) AS total 
    FROM payments 
    WHERE payment_status = 'paid'
    GROUP BY MONTH(payment_date)
    ORDER BY MONTH(payment_date)
");
$monthlyPaymentsData = [];
while ($row = $monthlyPaymentsQuery->fetch_assoc()) {
    $monthlyPaymentsData[] = $row;
}

// Fetch gender data for active members pie chart
$activeGenderData = [];
$activeGenderQuery = $conn->query("
    SELECT gender, COUNT(*) AS total 
    FROM members 
    WHERE status = 'active' 
    GROUP BY gender
");
while ($row = $activeGenderQuery->fetch_assoc()) {
    $activeGenderData[$row['gender']] = $row['total'];
}

// Fetch gender data for inactive members pie chart
$inactiveGenderData = [];
$inactiveGenderQuery = $conn->query("
    SELECT gender, COUNT(*) AS total 
    FROM members 
    WHERE status = 'inactive' 
    GROUP BY gender
");
while ($row = $inactiveGenderQuery->fetch_assoc()) {
    $inactiveGenderData[$row['gender']] = $row['total'];
}

// Fetch recent active memberships
$recentMembersQuery = $conn->query("
    SELECT m.name, m.email, mp.start_date
    FROM members m
    JOIN member_packages mp ON m.id = mp.member_id
    WHERE m.status = 'active'
    ORDER BY mp.start_date DESC 
    LIMIT 5
");
$recentActiveMembers = $recentMembersQuery->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="../js/admin.js"></script>
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
    </aside>

    <main class="main-content">
        <h2>Welcome, Admin!</h2>
        <div class="stats">
            <div class="stat-card" style="background: linear-gradient(135deg,rgb(142, 255, 255) 0%,rgb(150, 173, 255) 100%);">
                <h3><i class="fas fa-users"></i> Total Members</h3>
                <p style="font-size: 1.6em; font-weight: bold;"><?php echo $totalMembers; ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg,rgb(98, 255, 203) 0%,rgb(137, 204, 255) 100%);">
                <h3><i class="fas fa-dollar-sign"></i> Total Payments</h3>
                <p style="font-size: 1.6em; font-weight: bold;">RM<?php echo number_format($totalPayments, 2); ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg,rgb(255, 176, 176) 0%,rgb(248, 255, 144) 100%);">
                <h3><i class="fas fa-user-check"></i> Active Members</h3>
                <p style="font-size: 1.6em; font-weight: bold;"><?php echo $activeMembers; ?></p>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg,rgb(203, 174, 255) 0%,rgb(255, 180, 213) 100%);">
                <h3><i class="fas fa-user-times"></i> Inactive Members</h3>
                <p style="font-size: 1.6em; font-weight: bold;">
                    <?php
                    $inactiveMembersCount = $conn->query("SELECT COUNT(*) AS total FROM members WHERE status = 'inactive'")->fetch_assoc()['total'];
                    echo $inactiveMembersCount;
                    ?>
                </p>
            </div>
        </div>

        <!-- Top Section -->
        <div class="top-section">
            <!-- Active Members Pie Chart -->
            <div class="box">
                <h3>Active Members by Gender</h3>
                <canvas id="activeGenderChart"></canvas>
            </div>

            <!-- Inactive Members Pie Chart -->
            <div class="box">
                <h3>Inactive Members by Gender</h3>
                <canvas id="inactiveGenderChart"></canvas>
            </div>

            <!-- Recent Active Members -->
            <div class="box recent-members">
                <h3>Recent Active Memberships</h3>
                <ul>
                    <?php
                    $recentMembersQuery = $conn->query("
                        SELECT m.name, m.email, mp.start_date
                        FROM members m
                        JOIN member_packages mp ON m.id = mp.member_id
                        WHERE m.status = 'active'
                        ORDER BY mp.start_date DESC 
                        LIMIT 5
                    ");
                    while ($member = $recentMembersQuery->fetch_assoc()) {
                        echo "<li><strong>{$member['name']}</strong> - {$member['email']}<br><small>Membership Start: {$member['start_date']}</small></li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
        <br>
        <!-- Bottom Section -->
        <div class="bottom-section">
            <h3>Monthly Payments</h3>
            <canvas id="paymentChart"></canvas>
        </div>
    </main>
</div>
<script>
    // Active Members by Gender
    new Chart(document.getElementById('activeGenderChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($activeGenderData)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($activeGenderData)); ?>,
                backgroundColor: ['#4bc0c0', '#36a2eb']
            }]
        }
    });

    // Inactive Members by Gender
    new Chart(document.getElementById('inactiveGenderChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($inactiveGenderData)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($inactiveGenderData)); ?>,
                backgroundColor: ['#ffcd56', '#ff6384']
            }]
        }
    });

    // Monthly Payments
    new Chart(document.getElementById('paymentChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode(array_column($monthlyPaymentsData, 'month')); ?>,
            datasets: [{
                label: 'Monthly Payments',
                data: <?php echo json_encode(array_column($monthlyPaymentsData, 'total')); ?>,
                borderColor: '#4bc0c0',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });
</script>
<footer>
    <p>&copy; <?php echo date("Y"); ?> Gym Membership System. All rights reserved.</p>
</footer>
</body>
</html>