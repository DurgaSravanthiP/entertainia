<?php
session_start();
$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login • Fun Entertainia</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="page-login">
  <div class="auth-wrapper">
    <div class="auth-card">
      <div class="auth-header">
        <h2>Welcome back</h2>
        <p>Sign in to continue to Fun Entertainia</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="message error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
        <div class="message success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form action="login_process.php" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input class="form-control" id="username" type="text" name="username" placeholder="Enter your username"
            required />
        </div>
        <div class="form-group password-wrap">
          <label for="password">Password</label>
          <input class="form-control" id="password" type="password" name="password" placeholder="Enter your password"
            required />
          <button type="button" class="password-toggle" data-target="password">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>

        <div class="form-group role-select">
          <label><input type="radio" name="login_as" value="user" checked> User</label>
          <label><input type="radio" name="login_as" value="admin"> Admin</label>
        </div>

        <div class="auth-actions">
          <button type="submit" class="btn-primary">Log In</button>
          <div class="link-inline">
            <a href="forgot_password.php">Forgot Password?</a>
          </div>
        </div>

        <div class="auth-footer">
          New here? <a href="register.php">Create an account</a>
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