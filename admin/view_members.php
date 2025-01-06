<?php
include '../includes/db.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit();
}

// Fetch members with pagination and sorting
$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['status']) ? $_GET['status'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$sort_order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'desc' : 'asc';

$query = "
    SELECT 
        m.*, 
        IFNULL(mp.status, 'inactive') AS membership_status 
    FROM members m
    LEFT JOIN member_packages mp ON m.id = mp.member_id AND mp.status = 'active'
    WHERE (m.name LIKE '%$search%' OR m.email LIKE '%$search%')
";
if ($filter) {
    $query .= " AND mp.status = '$filter'";
}
$query .= " ORDER BY $sort_column $sort_order LIMIT $limit OFFSET $offset";
$result = $conn->query($query);

// Get total number of members for pagination
$total_query = "
    SELECT COUNT(*) as total 
    FROM members m
    LEFT JOIN member_packages mp ON m.id = mp.member_id AND mp.status = 'active'
    WHERE (m.name LIKE '%$search%' OR m.email LIKE '%$search%')
";
if ($filter) {
    $total_query .= " AND mp.status = '$filter'";
}
$total_result = $conn->query($total_query);
$total_members = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_members / $limit);

// Determine the next sort order
$next_order = $sort_order === 'asc' ? 'desc' : 'asc';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Members</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../css/admin.css">
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
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 15px; /* Reduce font size */
        }
        table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        table tr:hover {
            background-color: #f1f1f1;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions a {
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
            font-size: 12px;
        }
        .actions .edit {
            background-color: #28a745;
        }
        .actions .delete {
            background-color: #dc3545;
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
        .status-active {
            color: green;
            font-weight: bold;
        }
        .status-inactive {
            color: red;
            font-weight: bold;
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
            <a href="#"><i class="fas fa-user"></i> Admin Profile</a>
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
            <h2>Registered Members List</h2>
            <div class="search-filter">
                <form method="GET">
                    <input type="text" name="search" placeholder="Search by name or email" value="<?php echo $search; ?>">
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    </select>
                    <button type="submit"><i class="fas fa-search"></i> Search</button>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>
                            <a href="?search=<?php echo $search; ?>&status=<?php echo $filter; ?>&sort=name&order=<?php echo $next_order; ?>">
                                Name <i class="fas fa-sort-<?php echo $sort_column === 'name' ? $sort_order : 'asc'; ?>"></i>
                            </a>
                        </th>
                        <th>
                            <a href="?search=<?php echo $search; ?>&status=<?php echo $filter; ?>&sort=email&order=<?php echo $next_order; ?>">
                                Email <i class="fas fa-sort-<?php echo $sort_column === 'email' ? $sort_order : 'asc'; ?>"></i>
                            </a>
                        </th>
                        <th>
                            <a href="?search=<?php echo $search; ?>&status=<?php echo $filter; ?>&sort=phone&order=<?php echo $next_order; ?>">
                                Phone <i class="fas fa-sort-<?php echo $sort_column === 'phone' ? $sort_order : 'asc'; ?>"></i>
                            </a>
                        </th>
                        <th>
                            Address
                        </th>
                        <th>Status</th>
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
                                <td><?php echo $row['phone']; ?></td>
                                <td><?php echo $row['address']; ?></td>
                                <td class="<?php echo $row['membership_status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                    <?php echo ucfirst($row['membership_status']); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No members found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?search=<?php echo $search; ?>&status=<?php echo $filter; ?>&page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </main>
</div>
<script src="../js/admin.js"></script>
</body>
</html>