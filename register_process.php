<?php
// register_process.php
session_start();

// Database connection
require 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check if username already exists
    $checkStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        $_SESSION['error'] = 'Username already taken. Please choose another.';
        $checkStmt->close();
        header("Location: register.php");
        exit();
    }
    $checkStmt->close();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user into database
    try {
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $role = 'user';  // Default role is user
        $stmt->bind_param("sss", $username, $hashedPassword, $role);

        if ($stmt->execute()) {
            // Redirect to login page after successful registration
            $_SESSION['success'] = 'Registration successful! Please login.';
            header("Location: index.php");
            exit();
        } else {
            $_SESSION['error'] = "Error creating user. Please try again.";
            header("Location: register.php");
            exit();
        }
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        // Fallback for race conditions
        if ($e->getCode() == 1062) { // Duplicate entry code
            $_SESSION['error'] = 'Username already taken. Please choose another.';
            header("Location: register.php");
            exit();
        } else {
            throw $e;
        }
    }
}
?>