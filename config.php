<?php
// Database configuration
$db_host = "localhost";      // Usually localhost for local development
$db_name = "dashboard";      // Your database name
$db_user = "root";           // Default username for XAMPP/WAMP is usually root
$db_pass = "";               // Default password for XAMPP is often empty, WAMP might use "root"

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to avoid special character issues
$conn->set_charset("utf8mb4");
?>
