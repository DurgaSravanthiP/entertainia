<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

$username = $_SESSION['username'] ?? '';
?>
<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard - Fun Entertainia</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@700&family=Comic+Neue:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="selection.css">
</head>

<body>
    <div class="top-nav">
        <div class="nav-left">
            <div class="user-pill">
                Hello, <?php echo htmlspecialchars($username); ?>
            </div>
        </div>
        <div class="nav-right">
            <button class="nav-btn" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>

    <div class="heart-decoration">
        <i class="fas fa-heart"></i>
    </div>

    <div class="user-features">Premium User</div>

    <!-- Floating decorative elements -->
    <div class="floating-element"
        style="width: 100px; height: 100px; background: var(--yellow); top: 10%; left: 10%; animation-delay: 0s;"></div>
    <div class="floating-element"
        style="width: 80px; height: 80px; background: var(--green); top: 70%; left: 80%; animation-delay: 1s;"></div>
    <div class="floating-element"
        style="width: 120px; height: 120px; background: var(--light-accent); top: 30%; left: 85%; animation-delay: 2s;">
    </div>

    <div class="dashboard">
        <h1>User Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($username); ?>! Enjoy your entertainment experience.</p>

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