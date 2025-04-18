<?php
session_start();
require 'config.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['flash_message'] = "Invalid form submission.";
    header("Location: login.php");
    exit();
}

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['flash_message'] = "Username and password are required.";
        header("Location: login.php");
        exit();
    }
    
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Success - set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_id'] = $user['id'];
            
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Redirect to intended page or dashboard
            $redirect = $_SESSION['redirect_to'] ?? 'index.php';
            unset($_SESSION['redirect_to']);
            header("Location: $redirect");
            exit();
        }
    }
    
    // Invalid credentials
    $_SESSION['flash_message'] = "Invalid username or password.";
    header("Location: login.php");
    exit();
} else {
    // If not POST request, redirect to login page
    header("Location: login.php");
    exit();
}
?>