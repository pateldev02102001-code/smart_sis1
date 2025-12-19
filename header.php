<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// This must match your folder name inside htdocs
$baseUrl = '/smart_sis1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Smart Student Information System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background:#f4f6f8; margin:0; }
        header { background:#1f3b6d; color:white; padding:10px 20px; }
        header h1 { margin:0; font-size:20px; }
        nav a { color:white; margin-right:15px; text-decoration:none; font-size:14px; }
        nav a:hover { text-decoration:underline; }
        .container { max-width:1100px; margin:20px auto; background:white; padding:20px;
                     box-shadow:0 2px 5px rgba(0,0,0,0.1); border-radius:4px; }
        .btn { display:inline-block; padding:6px 12px; border-radius:4px; text-decoration:none;
               font-size:14px; border:1px solid transparent; cursor:pointer; }
        .btn-primary { background:#1f3b6d; color:white; }
        .btn-secondary { background:#e0e3e8; color:#333; }
        .btn-danger { background:#b3261e; color:white; }
        .btn-sm { padding:4px 8px; font-size:12px; }
        table { width:100%; border-collapse:collapse; margin-top:10px; }
        th, td { border:1px solid #ddd; padding:8px; text-align:left; font-size:14px; }
        th { background:#f1f3f6; }
        form.inline { display:inline; }
        input[type=text], input[type=email], input[type=password], input[type=date], select, textarea {
            padding:6px; width:100%; box-sizing:border-box; margin-bottom:10px;
        }
        .flex { display:flex; gap:10px; flex-wrap:wrap; }
        .flex > div { flex:1 1 200px; }
        .stats { display:flex; gap:15px; flex-wrap:wrap; margin:15px 0; }
        .card { flex:1 1 200px; background:#f8fafc; padding:10px; border-radius:4px; border:1px solid #e2e8f0; }
        .card h3 { margin:0 0 5px 0; font-size:16px; }
        .text-right { text-align:right; }
        .text-center { text-align:center; }
        .alert { padding:10px; border-radius:4px; margin-bottom:10px; }
        .alert-success { background:#d1fae5; color:#065f46; }
        .alert-error { background:#fee2e2; color:#b91c1c; }
    </style>
</head>
<body>
<header>
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h1>Smart Student Information System</h1>
        <nav>
            <a href="<?php echo $baseUrl; ?>/index.php">Home</a>
            <?php if ($user): ?>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?php echo $baseUrl; ?>/admin/dashboard.php">Admin Dashboard</a>
                    <a href="<?php echo $baseUrl; ?>/admin/students.php">Students</a>
                    <a href="<?php echo $baseUrl; ?>/admin/courses.php">Courses</a>
                    <a href="<?php echo $baseUrl; ?>/admin/enrollments.php">Enrollments</a>
                <?php elseif ($user['role'] === 'student'): ?>
                    <a href="<?php echo $baseUrl; ?>/student/dashboard.php">My Dashboard</a>
                    <a href="<?php echo $baseUrl; ?>/student/profile.php">My Profile</a>
                <?php endif; ?>
                <span style="margin-right:10px;">Hello, <?php echo htmlspecialchars($user['name']); ?>
                    (<?php echo htmlspecialchars($user['role']); ?>)</span>
                <a href="<?php echo $baseUrl; ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?php echo $baseUrl; ?>/login.php">Login</a>
                <a href="<?php echo $baseUrl; ?>/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<div class="container">
