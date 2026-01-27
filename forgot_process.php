<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $newPassword = $_POST['new_password'] ?? '';

    if ($newPassword === '') {
        $_SESSION['error'] = 'Please enter a new password.';
        header("Location: forgot_password.php");
        exit();
    }

    $userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $userStmt->bind_param("s", $username);
    $userStmt->execute();
    $userStmt->store_result();

    if ($userStmt->num_rows === 0) {
        $_SESSION['error'] = 'No user found with that username.';
        header("Location: forgot_password.php");
        exit();
    }
    $userStmt->close();

    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE username = ?");
    $stmt->bind_param("ss", $hashed, $username);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success'] = 'Password updated. Please login with your new password.';
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = 'Something went wrong while updating your password.';
        header("Location: forgot_password.php");
        exit();
    }
}
?>
