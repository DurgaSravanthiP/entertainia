<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $loginAs = $_POST['login_as'];  // "user" or "admin"

    // 1. Try fetching from users table
    $stmt = $conn->prepare("SELECT password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    $found = false;
    $hashedPassword = '';
    $dbRole = '';

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword, $dbRole);
        $stmt->fetch();
        $found = true;
    } else {
        // 2. Try fetching from admins table
        $stmt_admin = $conn->prepare("SELECT password, 'admin' as role FROM admins WHERE username = ?");
        $stmt_admin->bind_param("s", $username);
        $stmt_admin->execute();
        $stmt_admin->store_result();

        if ($stmt_admin->num_rows === 1) {
            $stmt_admin->bind_result($hashedPassword, $dbRole);
            $stmt_admin->fetch();
            $found = true;
        }
    }

    if ($found) {
        // Check password
        if (password_verify($password, $hashedPassword)) {
            session_regenerate_id(true);

            if ($loginAs === 'admin') {
                if ($dbRole === 'admin') {
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'admin';
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    $_SESSION['error'] = 'Access denied: you are not an admin.';
                    header("Location: login.php");
                    exit();
                }
            } else {
                // login_as == 'user'
                $_SESSION['username'] = $username;
                $_SESSION['role'] = $dbRole;
                header("Location: selection.php");
                exit();
            }
        } else {
            $_SESSION['error'] = 'Invalid password. Please try again.';
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = 'No account found with that username.';
        header("Location: login.php");
        exit();
    }
}
?>