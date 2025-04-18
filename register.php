<?php
session_start();
require 'config.php';

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || 
    $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['signup_error'] = "Invalid form submission.";
    header("Location: signup.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $confirm = $_POST['confirm_password'];

  if ($password !== $confirm) {
    $_SESSION['signup_error'] = "Passwords do not match.";
    header("Location: signup.php");
    exit();
  }

  if (strlen($password) < 6) {
    $_SESSION['signup_error'] = "Password must be at least 6 characters.";
    header("Location: signup.php");
    exit();
  }

  $checkUser = $conn->prepare("SELECT id FROM users WHERE username = ?");
  $checkUser->bind_param("s", $username);
  $checkUser->execute();
  $checkUser->store_result();

  if ($checkUser->num_rows > 0) {
    $_SESSION['signup_error'] = "Username already exists.";
    header("Location: signup.php");
    exit();
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $insert = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
  $insert->bind_param("ss", $username, $hashedPassword);

  if ($insert->execute()) {
    $_SESSION['flash_message'] = "Account created successfully! Please login.";
    header("Location: login.php");
  } else {
    $_SESSION['signup_error'] = "Something went wrong. Try again.";
    header("Location: signup.php");
  }
  
  $conn->close();
} else {
  // If not a POST request, redirect to signup form
  header("Location: signup.php");
  exit();
}
?>