<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete - admin only
if (isset($_GET['delete'])) {
    if ($_SESSION['role'] !== 'admin') {
        die("Access denied. Only admins can delete posts.");
    }
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: posts.php");
    exit();
}

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Pagination settings
$posts_per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $posts_per_page;

// Build query based on search
if ($search !== '') {
    $searchTerm = "%$search%";

    // Count total matching posts
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM posts WHERE title LIKE ? OR content LIKE ?");
    $countStmt->bind_param("ss", $searchTerm, $searchTerm);
    $countStmt->execute();
    $totalRows = $countStmt->get_result()->fetch_assoc()['total'];
    $countStmt->close();

    $stmt = $conn->prepare("SELECT * FROM posts WHERE title LIKE ? OR content LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ssii", $searchTerm, $searchTerm, $posts_per_page, $offset);
} else {
    // Count total posts
    $totalRows = $conn->query("SELECT COUNT(*) as total FROM posts")->fetch_assoc()['total'];

    $stmt = $conn->prepare("SELECT * FROM posts ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $posts_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();
$total_pages = ceil($totalRows / $posts_per_page);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> (<?php echo htmlspecialchars($_SESSION['role']); ?>)</h2>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
    </div>

    <a href="create.php" class="btn btn-primary mb-3">+ Add New Post</a>

    <form method="GET" class="d-flex mb-4">
        <input type="text" name="search" class="form-control me-2" placeholder="Search by title or content..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-secondary">Search</button>
        <?php if ($search !== ''): ?>
            <a href="posts.php" class="btn btn-link">Clear</a>
        <?php endif; ?>
    </form>

    <?php if ($result->num_rows === 0): ?>
        <p class="text-muted">No posts found.</p>
    <?php endif; ?>

    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($row['content']); ?></p>
                <small class="text-muted">Posted on <?php echo $row['created_at']; ?></small><br><br>
                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="posts.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')">Delete</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>

    <?php if ($total_pages > 1): ?>
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="posts.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>
</body>
</html>