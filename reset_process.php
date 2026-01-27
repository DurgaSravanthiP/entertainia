<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($token) || empty($newPassword)) {
        $_SESSION['error'] = 'Please provide a valid token and password.';
        header("Location: forgot_password.php");
        exit();
    }

    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE reset_token = ? AND reset_expires >= NOW()");
    $stmt->bind_param("ss", $hashed, $token);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $_SESSION['success'] = 'Password updated! Please log in with your new password.';
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['error'] = 'Invalid or expired token. Please request a new reset link.';
        header("Location: forgot_password.php");
        exit();
    }
}
?>
