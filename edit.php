<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: posts.php");
    exit();
}

$id = $_GET['id'];

// Handle form submission (update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: posts.php");
    exit();
}

// Fetch existing post data to pre-fill the form
$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();
$stmt->close();

if (!$post) {
    header("Location: posts.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head><title>Edit Post</title></head>
<body>
    <h2>Edit Post</h2>
    <form method="POST">
        Title: <br>
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required style="width: 300px;"><br><br>
        Content: <br>
        <textarea name="content" rows="5" cols="40" required><?php echo htmlspecialchars($post['content']); ?></textarea><br><br>
        <button type="submit">Update Post</button>
    </form>
    <p><a href="posts.php">Back to Posts</a></p>
</body>
</html>