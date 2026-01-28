<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Privacy • Fun Entertainia</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body,
    html {
      height: 100%;
      overflow: hidden;
    }

    .privacy-section {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .privacy-card {
      background: white;
      padding: 40px;
      border-radius: 30px;
      box-shadow: var(--card-shadow);
      max-width: 500px;
      width: 100%;
    }

    h1 {
      color: var(--primary);
      margin-bottom: 20px;
      font-size: 1.8rem;
    }

    p {
      color: #555;
      margin-bottom: 15px;
      font-size: 1rem;
      line-height: 1.5;
    }

    .back-home {
      display: inline-block;
      margin-top: 20px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 800;
    }
  </style>
</head>

<body class="page-login">

  <div class="privacy-section">
    <div class="privacy-card">
      <h1>Privacy Policy</h1>

      <p><strong>Is my data safe?</strong><br>Yes! We only use your information to help you log in and save your
        favorites.</p>

      <p><strong>What do you collect?</strong><br>Just your username and a secret password to keep your account safe.
      </p>

      <p><strong>Education First</strong><br>This is a school project. We don't share your information with anyone.</p>

      <a href="index.php" class="back-home"><i class="fas fa-arrow-left"></i> Go Back Home</a>
    </div>
  </div>

</body>

</html>