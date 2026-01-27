<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $loginAs = $_POST['login_as'];  // "user" or "admin"

    // Fetch that user’s hashed password and role
    $stmt = $conn->prepare(
        "SELECT password, role
         FROM users
        WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword, $dbRole);
        $stmt->fetch();

        // Check password first
        if (password_verify($password, $hashedPassword)) {
            session_regenerate_id(true);
            // Now enforce the chosen login_as matches their real DB role
            if ($loginAs === 'admin') {
                if ($dbRole === 'admin') {
                    // OK: real admin logging in as admin
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'admin';
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = 'Access denied: you are not an admin.';
                    header("Location: index.php");
                    exit();
                }
            } else {
                // login_as == 'user'
                if ($dbRole === 'user' || $dbRole === 'admin') {
                    // Both users and admins can log in as users
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = $dbRole;
                    header("Location: selection.php");
                    exit();
                } else {
                    $_SESSION['error'] = 'Invalid role. Contact admin.';
                    header("Location: index.php");
                    exit();
                }
            }
        } else {
            // Wrong password
            $_SESSION['error'] = 'Invalid password. Please try again.';
            header("Location: index.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = 'No such user. Please register first.';
        header("Location: index.php");
        exit();
    }
}
?>