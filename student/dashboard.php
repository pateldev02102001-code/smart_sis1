<?php
require_once __DIR__ . '/../auth.php';
require_role('student');
$pdo = getPDO();

$user = current_user();
$stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
$stmt->execute([$user['id']]);
$student = $stmt->fetch();

if (!$student) {
    include '../header.php';
    echo '<h2>Student Dashboard</h2>';
    echo '<p>No student profile is linked to your account yet. Please contact the administrator.</p>';
    include '../footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ?");
$stmt->execute([$student['id']]);
$totalEnrollments = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ? AND status = 'completed'");
$stmt->execute([$student['id']]);
$completed = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE student_id = ? AND status = 'enrolled'");
$stmt->execute([$student['id']]);
$current = (int)$stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT e.*, c.course_code, c.course_name
    FROM enrollments e
    JOIN courses c ON e.course_id = c.id
    WHERE e.student_id = ? AND e.status = 'enrolled'");
$stmt->execute([$student['id']]);
$currentCourses = $stmt->fetchAll();

include '../header.php';
?>
<h2>Student Dashboard</h2>
<p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></p>
<p><strong>Student #:</strong> <?php echo htmlspecialchars($student['student_number']); ?></p>
<p><strong>Program:</strong> <?php echo htmlspecialchars($student['program']); ?></p>
<p><strong>Status:</strong> <?php echo htmlspecialchars($student['status']); ?></p>

<div class="stats">
    <div class="card">
        <h3>Total Enrollments</h3>
        <p><?php echo $totalEnrollments; ?></p>
    </div>
    <div class="card">
        <h3>Current Courses</h3>
        <p><?php echo $current; ?></p>
    </div>
    <div class="card">
        <h3>Completed Courses</h3>
        <p><?php echo $completed; ?></p>
    </div>
</div>

<h3>Current Enrolled Courses</h3>
<table>
    <thead>
        <tr>
            <th>Course</th>
            <th>Semester</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$currentCourses): ?>
            <tr><td colspan="3">You are not currently enrolled in any courses.</td></tr>
        <?php else: ?>
            <?php foreach ($currentCourses as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['semester']); ?></td>
                    <td><?php echo htmlspecialchars($c['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<p><a href="profile.php" class="btn btn-secondary">Update My Profile</a></p>
<?php include '../footer.php'; ?>
