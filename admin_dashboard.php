<?php
session_start();
require 'db_connect.php';

// Check if admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html");
    exit();
}

// Count users
$sql = "SELECT COUNT(*) as total_users FROM users WHERE role = 'user'";
$result = $conn->query($sql);
$total_users = 0;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $total_users = $row['total_users'];
}
?>
<!DOCTYPE html>

<head>
    <title>Admin Dashboard - Fun Entertainia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700&family=Comic+Neue:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="admin_dashboard.css">
</head>

<body>
    <div class="top-nav">
        <div class="nav-left">
            <div class="user-pill">Admin Dashboard</div>
        </div>
        <div class="nav-right">
            <button class="nav-btn secondary" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>

    <div class="crown-decoration">
        <i class="fas fa-crown"></i>
    </div>

    <div class="admin-features">Admin Privileges</div>

    <!-- Floating decorative elements -->
    <div class="floating-element"
        style="width: 100px; height: 100px; background: var(--yellow); top: 10%; left: 10%; animation-delay: 0s;"></div>
    <div class="floating-element"
        style="width: 80px; height: 80px; background: var(--pink); top: 70%; left: 80%; animation-delay: 1s;"></div>
    <div class="floating-element"
        style="width: 120px; height: 120px; background: var(--light-accent); top: 30%; left: 85%; animation-delay: 2s;">
    </div>

    <div class="dashboard">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin! You have full control privileges.</p>
        <div style="margin-bottom: 20px; font-size: 1.5rem; color: var(--primary); font-weight: bold;">
            Total Registered Users: <?php echo $total_users; ?>
        </div>

        <div class="btn-group">
            <a href="cartoon.php" class="btn btn-primary">
                <i class="fas fa-film"></i> Cartoon Page
            </a>
            <a href="music.php" class="btn btn-primary">
                <i class="fas fa-music"></i> Music Page
            </a>

        </div>
    </div>
</body>

</html>