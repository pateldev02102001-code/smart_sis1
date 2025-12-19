<?php
require_once __DIR__ . '/../auth.php';
require_role('admin');
$pdo = getPDO();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

$student = [
    'user_id' => '',
    'student_number' => '',
    'first_name' => '',
    'dob' => '',
    'program' => '',
    'phone' => '',
    'status' => 'active',
];

$error = '';
$success = '';

if ($isEdit) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    if (!$existing) {
        die('Student not found.');
    }
    $student = $existing;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student['user_id'] = (int)($_POST['user_id'] ?? 0);
    $student['student_number'] = trim($_POST['student_number'] ?? '');
    $student['first_name'] = trim($_POST['first_name'] ?? '');
    $student['dob'] = $_POST['dob'] ?? null;
    $student['program'] = trim($_POST['program'] ?? '');
    $student['phone'] = trim($_POST['phone'] ?? '');
    $student['status'] = $_POST['status'] ?? 'active';

    if (!$student['user_id'] || !$student['student_number'] || !$student['first_name']) {
        $error = 'User, student number, and first name are required.';
    } else {
        if ($isEdit) {
            $sql = "UPDATE students SET user_id = ?, student_number = ?, first_name = ?, dob = ?, program = ?, phone = ?, status = ? WHERE id = ?";
            $params = [$student['user_id'], $student['student_number'], $student['first_name'], $student['dob'], $student['program'], $student['phone'], $student['status'], $id];
        } else {
            $sql = "INSERT INTO students (user_id, student_number, first_name, dob, program, phone, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $params = [$student['user_id'], $student['student_number'], $student['first_name'], $student['dob'], $student['program'], $student['phone'], $student['status']];
        }

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $success = $isEdit ? 'Student updated successfully.' : 'Student created successfully.';
            if (!$isEdit) {
                $student = [
                    'user_id' => '',
                    'student_number' => '',
                    'first_name' => '',
                    'dob' => '',
                    'program' => '',
                    'phone' => '',
                    'status' => 'active',
                ];
            }
        } catch (PDOException $e) {
            $error = 'Error saving student: ' . $e->getMessage();
        }
    }
}

$users = $pdo->query("SELECT id, name, email FROM users ORDER BY name ASC")->fetchAll();

include '../header.php';
?>
<h2><?php echo $isEdit ? 'Edit Student' : 'Add New Student'; ?></h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post">
    <div>
        <label>User Account</label>
        <select name="user_id" required>
            <option value="">-- Select User --</option>
            <?php foreach ($users as $u): ?>
                <option value="<?php echo $u['id']; ?>" <?php if($student['user_id']==$u['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($u['name'] . ' (' . $u['email'] . ')'); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="flex">
        <div>
            <label>Student Number</label>
            <input type="text" name="student_number" value="<?php echo htmlspecialchars($student['student_number']); ?>" required>
        </div>
        <div>
            <label>Status</label>
            <select name="status">
                <option value="active" <?php if($student['status']==='active') echo 'selected'; ?>>Active</option>
                <option value="inactive" <?php if($student['status']==='inactive') echo 'selected'; ?>>Inactive</option>
                <option value="graduated" <?php if($student['status']==='graduated') echo 'selected'; ?>>Graduated</option>
            </select>
        </div>
    </div>
    <div class="flex">
        <div>
            <label>First Name</label>
            <input type="text" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
        </div>
        <div>
            <label>Date of Birth</label>
            <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>">
        </div>
    </div>
    <div class="flex">
        <div>
            <label>Program</label>
            <input type="text" name="program" value="<?php echo htmlspecialchars($student['program']); ?>">
        </div>
        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
        </div>
    </div>
    <button class="btn btn-primary" type="submit"><?php echo $isEdit ? 'Update' : 'Create'; ?></button>
    <a href="students.php" class="btn btn-secondary">Back to List</a>
</form>
<?php include '../footer.php'; ?>
