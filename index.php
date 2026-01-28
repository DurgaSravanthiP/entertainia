<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Fun Entertainia</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body,
    html {
      height: 100%;
      overflow: hidden;
    }

    .hero-section {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .landing-card {
      background: white;
      padding: 40px;
      border-radius: 30px;
      box-shadow: var(--card-shadow);
      max-width: 500px;
      width: 100%;
      text-align: center;
    }

    .landing-logo {
      font-size: 4rem;
      margin-bottom: 20px;
      color: var(--primary);
    }

    .landing-title {
      font-size: 2.2rem;
      color: var(--primary);
      margin-bottom: 15px;
    }

    .landing-desc {
      font-size: 1.1rem;
      color: #666;
      margin-bottom: 30px;
    }

    .btn-group {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .landing-footer {
      margin-top: 30px;
      font-weight: 700;
    }
  </style>
</head>

<body class="page-login">

  <div class="hero-section">
    <div class="landing-card">
      <i class="fas fa-play-circle landing-logo"></i>
      <h1 class="landing-title">Fun Entertainia</h1>
      <p class="landing-desc">
        Watch cartoons and listen to music you love.
        Everything is easy and fun!
      </p>

      <div class="btn-group">
        <a href="login.php" class="btn-primary"
          style="display: block; width: 100%; text-decoration: none; text-align: center;">Login</a>
        <a href="register.php" class="btn-primary"
          style="display: block; width: 100%; text-decoration: none; text-align: center; background: #fff; color: var(--primary); border: 2px solid var(--primary);">Register</a>
      </div>

      <div class="landing-footer">
        <a href="privacy.php">Privacy Policy</a>
      </div>
    </div>
  </div>

</body>

</html>