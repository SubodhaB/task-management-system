<?php
session_start();
include "config.php"; // Ensure this contains a valid $conn database connection

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// CSRF Token Generation
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST["csrf_token"]) || $_POST["csrf_token"] !== $_SESSION["csrf_token"]) {
        die("Invalid CSRF token!");
    }

    // Securely fetch inputs
    $title = trim((string) $_POST["title"]);
    $description = trim((string) $_POST["description"]);
    $user_id = (int) $_SESSION["user_id"];

    // Validate input
    if (empty($title) || empty($description)) {
        echo "Please fill in all fields.";
        exit();
    }

    // Prepare and execute SQL query
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, title, description) VALUES (?, ?, ?)");
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("iss", $user_id, $title, $description);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: tasks.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
        $stmt->close();
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
            <input type="text" name="title" placeholder="Task Title" required>
            <textarea name="description" placeholder="Task Description" required></textarea>
            <button type="submit">Add Task</button>
        </form>
        <a href="tasks.php">Back to Tasks</a>
    </div>
</body>
</html>
