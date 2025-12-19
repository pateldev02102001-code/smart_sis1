<?php
require_once __DIR__ . '/../auth.php';
require_role('admin');
$pdo = getPDO();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)($_POST['student_id'] ?? 0);
    $course_id = (int)($_POST['course_id'] ?? 0);
    $semester = trim($_POST['semester'] ?? '');
    $status = $_POST['status'] ?? 'enrolled';

    if (!$student_id || !$course_id || !$semester) {
        $error = 'Student, course, and semester are required.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO enrollments (student_id, course_id, semester, status, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$student_id, $course_id, $semester, $status]);
            $success = 'Enrollment added.';
        } catch (PDOException $e) {
            $error = 'Error adding enrollment: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM enrollments WHERE id = ?");
    $stmt->execute([$id]);
    $success = 'Enrollment deleted.';
}

$students = $pdo->query("SELECT id, student_number, first_name FROM students ORDER BY student_number")->fetchAll();
$courses = $pdo->query("SELECT id, course_code, course_name FROM courses ORDER BY course_code")->fetchAll();

$sql = "SELECT e.*, s.student_number, s.first_name, c.course_code, c.course_name
        FROM enrollments e
        JOIN students s ON e.student_id = s.id
        JOIN courses c ON e.course_id = c.id
        ORDER BY e.created_at DESC";
$enrollments = $pdo->query($sql)->fetchAll();

include '../header.php';
?>
<h2>Enrollment Management</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<h3>Add Enrollment</h3>
<form method="post">
    <div class="flex">
        <div>
            <label>Student</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?php echo $s['id']; ?>">
                        <?php echo htmlspecialchars($s['student_number'] . ' - ' . $s['first_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Course</label>
            <select name="course_id" required>
                <option value="">-- Select Course --</option>
                <?php foreach ($courses as $c): ?>
                    <option value="<?php echo $c['id']; ?>">
                        <?php echo htmlspecialchars($c['course_code'] . ' - ' . $c['course_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Semester</label>
            <input type="text" name="semester" placeholder="e.g. Winter 2026" required>
        </div>
        <div>
            <label>Status</label>
            <select name="status">
                <option value="enrolled">Enrolled</option>
                <option value="completed">Completed</option>
                <option value="dropped">Dropped</option>
            </select>
        </div>
    </div>
    <button class="btn btn-primary" type="submit">Add Enrollment</button>
</form>

<h3>Existing Enrollments</h3>
<table>
    <thead>
        <tr>
            <th>Student</th>
            <th>Course</th>
            <th>Semester</th>
            <th>Status</th>
            <th>Added On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$enrollments): ?>
            <tr><td colspan="6">No enrollments yet.</td></tr>
        <?php else: ?>
            <?php foreach ($enrollments as $e): ?>
                <tr>
                    <td><?php echo htmlspecialchars($e['student_number'] . ' - ' . $e['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($e['course_code'] . ' - ' . $e['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($e['semester']); ?></td>
                    <td><?php echo htmlspecialchars($e['status']); ?></td>
                    <td><?php echo htmlspecialchars($e['created_at']); ?></td>
                    <td>
                        <a class="btn btn-sm btn-danger" href="enrollments.php?delete=<?php echo $e['id']; ?>" onclick="return confirm('Delete this enrollment?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php include '../footer.php'; ?>
