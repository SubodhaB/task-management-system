<?php
// Start session securely
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// InfinityFree MySQL Database Credentials
$host = "sql200.infinityfree.com";  // Correct MySQL hostname
$user = "if0_38452465"; 
$pass = "a4gsj2PLLV6VNT";  // Replace with your actual password
$dbname = "if0_38452465_task_management"; 

// Create database connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

