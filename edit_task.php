<?php
session_start();
include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: tasks.php");
    exit();
}

$id = $_GET["id"];
$sql = "SELECT * FROM tasks WHERE id = $id AND user_id = ".$_SESSION["user_id"];
$result = $conn->query($sql);
$task = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $status = $_POST["status"];

    $update_sql = "UPDATE tasks SET title='$title', description='$description', status='$status' WHERE id=$id";
    if ($conn->query($update_sql) === TRUE) {
        header("Location: tasks.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Edit Task</h2>
        <form action="edit_task.php?id=<?php echo $id; ?>" method="POST">
            <input type="text" name="title" value="<?php echo $task['title']; ?>" required>
            <textarea name="description"><?php echo $task['description']; ?></textarea>
            <select name="status">
                <option value="pending" <?php if ($task['status'] == "pending") echo "selected"; ?>>Pending</option>
                <option value="completed" <?php if ($task['status'] == "completed") echo "selected"; ?>>Completed</option>
            </select>
            <button type="submit">Update Task</button>
        </form>
        <a href="tasks.php">Back to Tasks</a>
    </div>
</body>
</html>
