<?php
require_once __DIR__ . '/../auth.php';
require_role('admin');
$pdo = getPDO();

$search = trim($_GET['search'] ?? '');
$status = $_GET['status'] ?? '';

$sql = "SELECT s.*, u.email FROM students s
        JOIN users u ON s.user_id = u.id
        WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (s.student_number LIKE ? OR s.first_name LIKE ?)";
    $like = "%{$search}%";
    $params[] = $like;
    $params[] = $like;
}

if ($status !== '') {
    $sql .= " AND s.status = ?";
    $params[] = $status;
}

$sql .= " ORDER BY s.student_number ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

include '../header.php';
?>
<h2>Students</h2>
<p><a href="student_form.php" class="btn btn-primary btn-sm">Add New Student</a></p>

<form method="get" class="flex">
    <div>
        <label>Search (Student # or First Name)</label>
        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>">
    </div>
    <div>
        <label>Status</label>
        <select name="status">
            <option value="">All</option>
            <option value="active" <?php if($status==='active') echo 'selected'; ?>>Active</option>
            <option value="inactive" <?php if($status==='inactive') echo 'selected'; ?>>Inactive</option>
            <option value="graduated" <?php if($status==='graduated') echo 'selected'; ?>>Graduated</option>
        </select>
    </div>
    <div style="align-self:flex-end;">
        <button class="btn btn-secondary" type="submit">Filter</button>
    </div>
</form>

<table>
    <thead>
        <tr>
            <th>Student #</th>
            <th>First Name</th>
            <th>Email</th>
            <th>Program</th>
            <th>Status</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!$students): ?>
            <tr><td colspan="7" class="text-center">No students found.</td></tr>
        <?php else: ?>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?php echo htmlspecialchars($s['student_number']); ?></td>
                    <td><?php echo htmlspecialchars($s['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($s['email']); ?></td>
                    <td><?php echo htmlspecialchars($s['program']); ?></td>
                    <td><?php echo htmlspecialchars($s['status']); ?></td>
                    <td><?php echo htmlspecialchars($s['phone']); ?></td>
                    <td>
                        <a class="btn btn-sm btn-secondary" href="student_form.php?id=<?php echo $s['id']; ?>">Edit</a>
                        <a class="btn btn-sm btn-secondary" href="view_student.php?id=<?php echo $s['id']; ?>">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
<?php include '../footer.php'; ?>
