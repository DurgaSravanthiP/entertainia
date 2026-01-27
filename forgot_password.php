<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
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
                <h2>Reset your password</h2>
                <p>Enter your username and new password.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="message success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="forgot_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input class="form-control" type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                <div class="form-group password-wrap">
                    <label for="new_password">New Password</label>
                    <input class="form-control" type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
                    <button type="button" class="password-toggle" data-target="new_password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                <button type="submit" class="btn-primary">Update Password</button>
                <div class="auth-footer">
                    <a href="index.php">Return to login</a>
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
