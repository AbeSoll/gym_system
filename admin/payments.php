<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Pagination and sorting variables
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'payment_date';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

try {
    // Fetch payments with filters, search, pagination, and membership dates
    $query = "
        SELECT payments.*, members.name, members.email, member_packages.start_date, member_packages.end_date
        FROM payments
        JOIN members ON payments.member_id = members.id
        LEFT JOIN member_packages ON payments.member_id = member_packages.member_id
        WHERE 
            (members.name LIKE '%$search%' OR 
             members.email LIKE '%$search%' OR 
             payments.amount LIKE '%$search%' OR 
             payments.payment_status LIKE '%$search%')";

    if ($status_filter) {
        $query .= " AND payments.payment_status = '$status_filter'";
    }

    $query .= " ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Error fetching payment records: " . $conn->error);
    }

    // Total number of payments for pagination
    $total_query = "
        SELECT COUNT(*) as total
        FROM payments
        JOIN members ON payments.member_id = members.id
        WHERE 
            (members.name LIKE '%$search%' OR 
             members.email LIKE '%$search%' OR 
             payments.amount LIKE '%$search%' OR 
             payments.payment_status LIKE '%$search%')";
    if ($status_filter) {
        $total_query .= " AND payments.payment_status = '$status_filter'";
    }

    $total_result = $conn->query($total_query);
    if (!$total_result) {
        throw new Exception("Error fetching total payments count: " . $conn->error);
    }

    $total_payments = $total_result->fetch_assoc()['total'];
    $total_pages = ceil($total_payments / $limit);

    // Calculate summary statistics
    $summary_query = "SELECT 
                        SUM(amount) AS total_paid, 
                        COUNT(*) AS total_payments 
                      FROM payments WHERE payment_status = 'paid'";
    $summary_result = $conn->query($summary_query);
    if (!$summary_result) {
        throw new Exception("Error fetching payment summary: " . $conn->error);
    }

    $summary_data = $summary_result->fetch_assoc();
    $total_paid = $summary_data['total_paid'] ?? 0;
    $total_payment_count = $summary_data['total_payments'] ?? 0;

    // Determine the next sort order
    $next_order = $sort_order === 'asc' ? 'desc' : 'asc';
} catch (Exception $e) {
    echo "<div class='error'>An error occurred: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Payments</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .container {
            padding: 20px;
        }
        .search-filter {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .search-filter input, .search-filter select {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .search-filter button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .search-filter button:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            text-align: left;
            padding: 15px;
            border-bottom: 1px solid #ddd;
            font-size: 15px;
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 5px;
            border-radius: 5px;
            color: #007bff;
        }
        .pagination a.active {
            background-color: #007bff;
            color: white;
            border: none;
        }
        .status-paid {
            color: green;
        }
        .status-canceled {
            color: red;
        }
        table td:nth-child(4), table th:nth-child(4) {
            text-align: right;
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
    </aside>

    <main class="main-content">
        <div class="container">
            <h2>Payment Records</h2>
            <div class="summary">
                <p><strong>Total Payments:</strong> <?php echo $total_payment_count; ?></p>
                <p><strong>Total Amount Paid:</strong> RM<?php echo number_format($total_paid, 2); ?></p>
            </div>
            <div class="search-filter">
                <form method="GET">
                <input type="text" name="search" placeholder="Search" value="<?php echo $search; ?>">
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="paid" <?php echo $status_filter === 'paid' ? 'selected' : ''; ?>>Paid</option>
                        <option value="canceled" <?php echo $status_filter === 'canceled' ? 'selected' : ''; ?>>Canceled</option>
                    </select>
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="sortable"><a href="?search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&sort=name&order=<?php echo $next_order; ?>">Member Name <i class="fas fa-sort-<?php echo $sort_column === 'name' ? $sort_order : 'asc'; ?>"></i></a></th>
                        <th class="sortable"><a href="?search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&sort=email&order=<?php echo $next_order; ?>">Email <i class="fas fa-sort-<?php echo $sort_column === 'email' ? $sort_order : 'asc'; ?>"></i></a></th>
                        <th class="sortable"><a href="?search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&sort=amount&order=<?php echo $next_order; ?>">Amount (RM) <i class="fas fa-sort-<?php echo $sort_column === 'amount' ? $sort_order : 'asc'; ?>"></i></a></th>
                        <th class="sortable"><a href="?search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&sort=payment_status&order=<?php echo $next_order; ?>">Status <i class="fas fa-sort-<?php echo $sort_column === 'payment_status' ? $sort_order : 'asc'; ?>"></i></a></th>
                        <th class="sortable"><a href="?search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&sort=payment_date&order=<?php echo $next_order; ?>">Payment Date <i class="fas fa-sort-<?php echo $sort_column === 'payment_date' ? $sort_order : 'asc'; ?>"></i></a></th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $counter = $offset + 1; ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo $row['name']; ?></td>
                                <td><?php echo $row['email']; ?></td>
                                <td><?php echo $row['amount'] > 0 ? "RM" . number_format($row['amount'], 2) : "N/A"; ?></td>
                                <td class="status-<?php echo $row['payment_status']; ?>"><?php echo ucfirst($row['payment_status']); ?></td>
                                <td><?php echo $row['payment_date']; ?></td>
                                <td><?php echo $row['start_date'] ?: 'N/A'; ?></td>
                                <td><?php echo $row['end_date'] ?: 'N/A'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" style="text-align: center;">No payment records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?search=<?php echo $search; ?>&status=<?php echo $status_filter; ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </main>
</div>
<script src="../js/admin.js"></script>
</body>
</html>
