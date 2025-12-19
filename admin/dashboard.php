<?php
require_once __DIR__ . '/../auth.php';
require_role('admin');
$pdo = getPDO();

$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalCourses = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$totalEnrollments = (int)$pdo->query("SELECT COUNT(*) FROM enrollments")->fetchColumn();
$activeStudents = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status = 'active'")->fetchColumn();

include '../header.php';
?>
<h2>Admin Dashboard</h2>
<div class="stats">
    <div class="card">
        <h3>Total Students</h3>
        <p><?php echo $totalStudents; ?></p>
    </div>
    <div class="card">
        <h3>Active Students</h3>
        <p><?php echo $activeStudents; ?></p>
    </div>
    <div class="card">
        <h3>Total Courses</h3>
        <p><?php echo $totalCourses; ?></p>
    </div>
    <div class="card">
        <h3>Total Enrollments</h3>
        <p><?php echo $totalEnrollments; ?></p>
    </div>
</div>
<p>Use the navigation menu to manage students, courses, and enrollments.</p>
<?php include '../footer.php'; ?>
