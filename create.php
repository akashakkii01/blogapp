<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
    $stmt->bind_param("ss", $title, $content);
    $stmt->execute();
    $stmt->close();

    header("Location: posts.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Add Post</title></head>
<body>
    <h2>Add New Post</h2>
    <form method="POST">
        Title: <br>
        <input type="text" name="title" required style="width: 300px;"><br><br>
        Content: <br>
        <textarea name="content" rows="5" cols="40" required></textarea><br><br>
        <button type="submit">Save Post</button>
    </form>
    <p><a href="posts.php">Back to Posts</a></p>
</body>
</html>