<?php
require_once __DIR__ . '/../auth.php';
require_role('admin');
$pdo = getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT s.*, u.name AS user_name, u.email
                       FROM students s
                       JOIN users u ON s.user_id = u.id
                       WHERE s.id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();
if (!$student) {
    die('Student not found.');
}

$enrollStmt = $pdo->prepare("SELECT e.*, c.course_code, c.course_name
                             FROM enrollments e
                             JOIN courses c ON e.course_id = c.id
                             WHERE e.student_id = ?");
$enrollStmt->execute([$id]);
$enrollments = $enrollStmt->fetchAll();

include '../header.php';
?>
<h2>Student Details</h2>
<p><strong>Student #:</strong> <?php echo htmlspecialchars($student['student_number']); ?></p>
<p><strong>First Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></p>
<p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
<p><strong>Program:</strong> <?php echo htmlspecialchars($student['program']); ?></p>
<p><strong>Status:</strong> <?php echo htmlspecialchars($student['status']); ?></p>
<p><strong>Phone:</strong> <?php echo htmlspecialchars($student['phone']); ?></p>
<p><strong>DOB:</strong> <?php echo htmlspecialchars($student['dob']); ?></p>

<h3>Enrollments</h3>
<table>
    <thead>
        <tr>
            <th>Course</th>
            <th>Semester</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$enrollments): ?>
            <tr><td colspan="3">No enrollments.</td></tr>
        <?php else: ?>
            <?php foreach ($enrollments as $e): ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['course_code'] . ' - ' . $e['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($e['semester']); ?></td>
                    <td><?php echo htmlspecialchars($e['status']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<p><a href="students.php" class="btn btn-secondary">Back to Students</a></p>
<?php include '../footer.php'; ?>
