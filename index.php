<?php include 'header.php'; ?>
<h2>Welcome to the Smart Student Information System</h2>
<p>
    This system allows an institute to manage student records, courses, and enrollments
    using a simple web interface. Admins can manage data, and students can log in to see
    their profile and enrolled courses.
</p>

<?php if (!$user): ?>
    <p>
        <a href="login.php" class="btn btn-primary">Login</a>
        <a href="register.php" class="btn btn-secondary">Register</a>
    </p>
<?php else: ?>
    <?php if ($user['role'] === 'admin'): ?>
        <p><a href="admin/dashboard.php" class="btn btn-primary">Go to Admin Dashboard</a></p>
    <?php else: ?>
        <p><a href="student/dashboard.php" class="btn btn-primary">Go to Student Dashboard</a></p>
    <?php endif; ?>
<?php endif; ?>

<?php include 'footer.php'; ?>
