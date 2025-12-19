<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$pdo = getPDO();

// Check existing admin count
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'admin'");
$adminCount = (int)$stmt->fetchColumn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password_confirmation'] ?? '';
    $role = $_POST['role'] ?? 'student';

    if ($adminCount > 0 && $role === 'admin') {
        $role = 'student'; // only first user can be admin
    }

    if (!$name || !$email || !$password || !$confirm) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$name, $email, $hash, $role]);
            $success = 'Registration successful. You can now log in.';
        }
    }
}

include 'header.php';
?>
<h2>Register</h2>
<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>
<form method="post">
    <div>
        <label>Name</label>
        <input type="text" name="name" required>
    </div>
    <div>
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="flex">
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>
    </div>
    <div>
        <label>Role</label>
        <select name="role">
            <?php if ($adminCount == 0): ?>
                <option value="admin">Admin</option>
            <?php endif; ?>
            <option value="student">Student</option>
        </select>
        <?php if ($adminCount > 0): ?>
            <p style="font-size:12px;color:#666;">An admin already exists. New users will be students.</p>
        <?php endif; ?>
    </div>
    <button class="btn btn-primary" type="submit">Register</button>
</form>
<p>Already registered? <a href="login.php">Login</a></p>
<?php include 'footer.php'; ?>
