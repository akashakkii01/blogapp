<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (empty($title) || empty($content)) {
        $error = "Title and content cannot be empty.";
    } elseif (strlen($title) > 255) {
        $error = "Title is too long (max 255 characters).";
    } else {
        $stmt = $conn->prepare("INSERT INTO posts (title, content) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $content);
        $stmt->execute();
        $stmt->close();

        header("Location: posts.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Post</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px;">
    <h2 class="mb-4">Add New Post</h2>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" maxlength="255" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Post</button>
        <a href="posts.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
function validateForm() {
    const title = document.getElementById('title').value.trim();
    const content = document.getElementById('content').value.trim();
    if (title === '' || content === '') {
        alert("Title and content cannot be empty.");
        return false;
    }
    return true;
}
</script>
</body>
</html>