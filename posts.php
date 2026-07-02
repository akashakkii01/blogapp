<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: posts.php");
    exit();
}

$result = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head><title>Posts</title></head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
    <p><a href="logout.php">Logout</a></p>
    <p><a href="create.php">+ Add New Post</a></p>

    <hr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="margin-bottom: 20px;">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo htmlspecialchars($row['content']); ?></p>
            <small>Posted on <?php echo $row['created_at']; ?></small><br>
            <a href="edit.php?id=<?php echo $row['id']; ?>">Edit</a> |
            <a href="posts.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this post?')">Delete</a>
        </div>
    <?php endwhile; ?>
</body>
</html>