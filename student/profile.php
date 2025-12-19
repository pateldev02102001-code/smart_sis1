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
    echo '<h2>My Profile</h2>';
    echo '<p>No student profile is linked to your account yet. Please contact the administrator.</p>';
    include '../footer.php';
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $dob = $_POST['dob'] ?? null;

    try {
        $stmt = $pdo->prepare("UPDATE students SET phone = ?, program = ?, dob = ? WHERE id = ?");
        $stmt->execute([$phone, $program, $dob, $student['id']]);
        $success = 'Profile updated successfully.';
        $student['phone'] = $phone;
        $student['program'] = $program;
        $student['dob'] = $dob;
    } catch (PDOException $e) {
        $error = 'Error updating profile: ' . $e->getMessage();
    }
}

include '../header.php';
?>
<h2>My Profile</h2>
<p><strong>Name:</strong> <?php echo htmlspecialchars($student['first_name']); ?></p>
<p><strong>Student #:</strong> <?php echo htmlspecialchars($student['student_number']); ?></p>
<p><strong>Status:</strong> <?php echo htmlspecialchars($student['status']); ?></p>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="post">
    <div class="flex">
        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone']); ?>">
        </div>
        <div>
            <label>Program</label>
            <input type="text" name="program" value="<?php echo htmlspecialchars($student['program']); ?>">
        </div>
    </div>
    <div>
        <label>Date of Birth</label>
        <input type="date" name="dob" value="<?php echo htmlspecialchars($student['dob']); ?>">
    </div>
    <button class="btn btn-primary" type="submit">Save Changes</button>
</form>
<?php include '../footer.php'; ?>
