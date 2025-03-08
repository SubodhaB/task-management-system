<?php
// Start session securely
session_start();
include "config.php";

// Redirect if not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// Ensure CSRF token exists
try {
    if (!isset($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
} catch (Exception $e) {
    die("Error generating CSRF token: " . $e->getMessage());
}

// Ensure database connection is valid
if (!isset($conn) || !$conn instanceof mysqli) {
    error_log("Database connection failed.");
    die("Database connection issue. Please try again later.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate CSRF Token
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        die("Invalid CSRF token!");
    }

    // Sanitize and validate input
    $title = isset($_POST["title"]) ? trim($_POST["title"]) : "";
    $description = isset($_POST["description"]) ? trim($_POST["description"]) : "";
    $user_id = $_SESSION["user_id"];

    if (empty($title) || empty($description)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Prepare and execute query safely
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
    
    if (!$stmt) {
        error_log("Error preparing statement: " . $conn->error);
        die("Server error. Please try again later.");
    }

    $stmt->bind_param("iss", $user_id, $title, $description);

    if ($stmt->execute()) {
        // Regenerate CSRF token after successful submission
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        header("Location: tasks.php");
        exit();
    } else {
        error_log("Error inserting task: " . $stmt->error);
        echo "Error saving task. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Add New Task</h2>
        <form action="add_task.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <input type="text" name="title" placeholder="Task Title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
            <textarea name="description" placeholder="Task Description"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
            <button type="submit">Add Task</button>
        </form>
        <a href="tasks.php">Back to Tasks</a>
    </div>
</body>
</html>
