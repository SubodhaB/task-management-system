<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ensure session is not started multiple times
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once "config.php"; // Ensure config.php is properly included

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging: Check if form data is received
    if (empty($_POST)) {
        die("Error: No form data received.");
    }

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Check if database connection exists
    if (!$conn) {
        die("Database connection error: " . mysqli_connect_error());
    }

    // Validate input
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required.");
    }

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($check_stmt === false) {
        die("Error preparing check statement: " . $conn->error);
    }
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        die("Error: This email is already registered.");
    }
    $check_stmt->close();

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Prepare SQL query
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing insert statement: " . $conn->error);
    }
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        // Use JavaScript redirect instead of PHP header to avoid header issues
        echo "<script>alert('Registration successful! Redirecting to login...'); window.location.href='login.php';</script>";
        exit();
    } else {
        die("Error inserting user: " . $stmt->error);
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <form action="index.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <a href="login.php">Already have an account? Login</a>
    </div>
</body>
</html>
