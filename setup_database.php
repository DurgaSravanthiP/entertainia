<?php
// setup_database.php

$servername = "localhost";
$username = "root";
$password = "";

// 1. Connect to MySQL Server (without specifying database)
$conn = new mysqli($servername, $username, $password);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h3>Starting Database Setup...</h3>";

// 2. Create Database
$sql = "CREATE DATABASE IF NOT EXISTS funhub";
if ($conn->query($sql) === TRUE) {
    echo "Database 'funhub' created or already exists.<br>";
} else {
    die("Error creating database: " . $conn->error);
}

// 3. Select Database
$conn->select_db("funhub");

// 4. Create Users Table (with reset columns)
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_expires DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'users' created or already exists.<br>";
} else {
    echo "Error creating table: " . $conn->error . "<br>";
}

// 5. Create Favorites table
$sql = "CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    movie_title VARCHAR(150) NOT NULL,
    song_title VARCHAR(150) NOT NULL,
    artist VARCHAR(150) DEFAULT NULL,
    poster TEXT DEFAULT NULL,
    embed_url TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_favorites_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uniq_user_song (user_id, movie_title, song_title)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'favorites' created or already exists.<br>";
} else {
    echo "Error creating favorites table: " . $conn->error . "<br>";
}

// 5. Create Default Admin (Optional: password 'admin123')
$adminUser = 'admin';
$adminPass = password_hash('admin123', PASSWORD_DEFAULT); // Default password
$checkAdmin = "SELECT * FROM users WHERE username = '$adminUser'";
$result = $conn->query($checkAdmin);

if ($result->num_rows == 0) {
    $insertAdmin = "INSERT INTO users (username, password, role) VALUES ('$adminUser', '$adminPass', 'admin')";
    if ($conn->query($insertAdmin) === TRUE) {
        echo "Default admin account created (Username: <b>admin</b>, Password: <b>admin123</b>).<br>";
    } else {
        echo "Error creating admin: " . $conn->error . "<br>";
    }
} else {
    echo "Admin account already exists.<br>";
}

echo "<h3>Setup Completed Successfully!</h3>";
echo "<p><a href='index.php'>Go to Login Page</a></p>";

$conn->close();
?>