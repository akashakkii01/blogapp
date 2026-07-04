<?php
include 'db.php';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = "Username can only contain letters, numbers, and underscores.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->bind_param("s", $username);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = "Username already taken.";
        }
        $check->close();
    }

    if ($error === '') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'editor')");
        $stmt->bind_param("ss", $username, $hashedPassword);
        if ($stmt->execute()) {
            $success = "Registration successful! You can now login.";
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 400px;">
    <h2 class="mb-4">Register</h2>

    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?> <a href="login.php">Login here</a></div>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" minlength="3" pattern="[a-zA-Z0-9_]+" title="Letters, numbers, underscores only" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" minlength="6" required>
            <small class="text-muted">At least 6 characters</small>
        </div>
        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
    <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>
function validateForm() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    if (username.length < 3) {
        alert("Username must be at least 3 characters.");
        return false;
    }
    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }
    return true;
}
</script>
</body>
</html>