<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$pageTitle = "Manage Staff";
include('header.php');
require('db-connect.php');

$errmsg = '';
$successmsg = '';
$editMode = false;
$editStaff = null;

// Handle add staff
if (isset($_POST['add_staff'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    if ($name && $email && in_array($role, ['admin', 'staff'])) {
        $stmt = $conn->prepare("INSERT INTO staff (name, email, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $role);
        if ($stmt->execute()) {
            $successmsg = ucfirst($role) . " added successfully.";
        } else {
            $errmsg = "Error adding staff/admin. Email may already exist.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required and role must be valid.";
    }
}

// Handle delete staff
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $staffId = intval($_GET['id']);
    $conn->query("DELETE FROM staff WHERE id=$staffId");
    $successmsg = "Staff/Admin deleted.";
}

// Handle edit staff (show form)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editMode = true;
    $staffId = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM staff WHERE id=$staffId LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $editStaff = $result->fetch_assoc();
    }
}

// Handle update staff
if (isset($_POST['update_staff']) && isset($_POST['staff_id'])) {
    $staffId = intval($_POST['staff_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    if ($name && $email && in_array($role, ['admin', 'staff'])) {
        $stmt = $conn->prepare("UPDATE staff SET name=?, email=?, role=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $email, $role, $staffId);
        if ($stmt->execute()) {
            $successmsg = "Staff/Admin updated successfully.";
        } else {
            $errmsg = "Error updating staff/admin.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required and role must be valid.";
    }
}

// Fetch staff
$staffList = [];
$result = $conn->query("SELECT * FROM staff ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $staffList[] = $row;
    }
}
?>

<div class="mb-4">
    <h4><?php echo $editMode ? "Edit Staff/Admin" : "Add New Staff/Admin"; ?></h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-danger"><?php echo $errmsg; ?></div>
    <?php elseif ($successmsg): ?>
        <div class="alert alert-success"><?php echo $successmsg; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Full Name" required value="<?php echo $editMode ? htmlspecialchars($editStaff['name']) : ''; ?>">
        </div>
        <div class="col-md-4">
            <input type="email" name="email" class="form-control" placeholder="Email" required value="<?php echo $editMode ? htmlspecialchars($editStaff['email']) : ''; ?>">
        </div>
        <div class="col-md-3">
            <select name="role" class="form-control" required>
                <option value="">Select Role</option>
                <option value="admin" <?php echo $editMode && $editStaff['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                <option value="staff" <?php echo $editMode && $editStaff['role'] === 'staff' ? 'selected' : ''; ?>>Staff</option>
            </select>
        </div>
        <?php if ($editMode): ?>
            <input type="hidden" name="staff_id" value="<?php echo $editStaff['id']; ?>">
            <div class="col-md-1">
                <button type="submit" name="update_staff" class="btn btn-warning w-100">Update</button>
            </div>
            <div class="col-md-1">
                <a href="manage-staff.php" class="btn btn-secondary w-100">Cancel</a>
            </div>
        <?php else: ?>
            <div class="col-md-1">
                <button type="submit" name="add_staff" class="btn btn-primary w-100">Add</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<div>
    <h4>Staff/Admin List</h4>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($staffList as $staff): ?>
            <tr>
                <td><?php echo htmlspecialchars($staff['id']); ?></td>
                <td><?php echo htmlspecialchars($staff['name']); ?></td>
                <td><?php echo htmlspecialchars($staff['email']); ?></td>
                <td><?php echo htmlspecialchars(ucfirst($staff['role'])); ?></td>
                <td>
                    <a href="?action=edit&id=<?php echo $staff['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                    <a href="?action=delete&id=<?php echo $staff['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this staff/admin?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include('footer.php');
?>
