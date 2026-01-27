<?php
session_start();
require 'db_connect.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error'] = 'Invalid reset link.';
    header("Location: forgot_password.php");
    exit();
}

$stmt = $conn->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires >= NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $_SESSION['error'] = 'This reset link is invalid or has expired. Please request a new one.';
    header("Location: forgot_password.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | FunHub</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="page-login">
    <div class="auth-wrapper">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Reset Password</h2>
                <p>Create a new password for your account.</p>
            </div>

            <?php if (!empty($_SESSION['error'])): ?>
                <div class="message error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form action="reset_process.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="form-group password-wrap">
                    <label for="new_password">New Password</label>
                    <input class="form-control" type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    <button type="button" class="password-toggle" data-target="new_password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                <button type="submit" class="btn-primary">Reset Password</button>
                <div class="auth-footer">
                    <a href="index.php">Back to login</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.querySelectorAll('.password-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !isPassword);
                    icon.classList.toggle('fa-eye-slash', isPassword);
                }
            });
        });
    </script>
</body>
</html>