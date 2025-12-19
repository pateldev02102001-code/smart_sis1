<?php
require_once __DIR__ . '/../auth.php';
require_role('admin');
$pdo = getPDO();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $course_code = trim($_POST['course_code'] ?? '');
    $course_name = trim($_POST['course_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $credits = (int)($_POST['credits'] ?? 3);

    if (!$course_code || !$course_name) {
        $error = 'Course code and name are required.';
    } else {
        try {
            if ($id > 0) {
                $stmt = $pdo->prepare("UPDATE courses SET course_code = ?, course_name = ?, description = ?, credits = ? WHERE id = ?");
                $stmt->execute([$course_code, $course_name, $description, $credits, $id]);
                $success = 'Course updated successfully.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO courses (course_code, course_name, description, credits, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$course_code, $course_name, $description, $credits]);
                $success = 'Course created successfully.';
            }
        } catch (PDOException $e) {
            $error = 'Error saving course: ' . $e->getMessage();
        }
    }
}

if (isset($_GET['delete'])) {
    $deleteId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM courses WHERE id = ?");
    $stmt->execute([$deleteId]);
    $success = 'Course deleted.';
}

$courses = $pdo->query("SELECT * FROM courses ORDER BY course_code ASC")->fetchAll();

$editCourse = ['id' => 0, 'course_code' => '', 'course_name' => '', 'description' => '', 'credits' => 3];
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$editId]);
    $c = $stmt->fetch();
    if ($c) $editCourse = $c;
}

include '../header.php';
?>
<h2>Courses</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<h3><?php echo $editCourse['id'] ? 'Edit Course' : 'Add New Course'; ?></h3>
<form method="post">
    <input type="hidden" name="id" value="<?php echo (int)$editCourse['id']; ?>">
    <div class="flex">
        <div>
            <label>Course Code</label>
            <input type="text" name="course_code" value="<?php echo htmlspecialchars($editCourse['course_code']); ?>" required>
        </div>
        <div>
            <label>Course Name</label>
            <input type="text" name="course_name" value="<?php echo htmlspecialchars($editCourse['course_name']); ?>" required>
        </div>
        <div>
            <label>Credits</label>
            <input type="number" name="credits" value="<?php echo htmlspecialchars($editCourse['credits']); ?>" min="1" max="10">
        </div>
    </div>
    <div>
        <label>Description</label>
        <textarea name="description" rows="3"><?php echo htmlspecialchars($editCourse['description']); ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit"><?php echo $editCourse['id'] ? 'Update' : 'Create'; ?></button>
</form>

<h3>Course List</h3>
<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Credits</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$courses): ?>
            <tr><td colspan="5">No courses found.</td></tr>
        <?php else: ?>
            <?php foreach ($courses as $c): ?>
                <tr>
                    <td><?php echo htmlspecialchars($c['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($c['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($c['credits']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($c['description'])); ?></td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="courses.php?edit=<?php echo $c['id']; ?>">Edit</a>
                        <a class="btn btn-sm btn-danger" href="courses.php?delete=<?php echo $c['id']; ?>" onclick="return confirm('Delete this course?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php include '../footer.php'; ?>
