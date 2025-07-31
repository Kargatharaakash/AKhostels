<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}
$pageTitle = "Manage Fees";
include('header.php');
require('db-connect.php');

$errmsg = '';
$successmsg = '';
$editMode = false;
$editFee = null;

// Fetch students for dropdown
$students = [];
$result = $conn->query("SELECT id, fullname FROM students ORDER BY fullname ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Handle add fee
if (isset($_POST['add_fee'])) {
    $student_id = intval($_POST['student_id']);
    $amount = floatval($_POST['amount']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    if ($student_id && $amount > 0 && $status && $due_date) {
        $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, status, due_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $student_id, $amount, $status, $due_date);
        if ($stmt->execute()) {
            $successmsg = "Fee record added successfully.";
        } else {
            $errmsg = "Error adding fee record.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required and amount must be positive.";
    }
}

// Handle delete fee
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $feeId = intval($_GET['id']);
    $conn->query("DELETE FROM fees WHERE id=$feeId");
    $successmsg = "Fee record deleted.";
}

// Handle edit fee (show form)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editMode = true;
    $feeId = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM fees WHERE id=$feeId LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $editFee = $result->fetch_assoc();
    }
}

// Handle update fee
if (isset($_POST['update_fee']) && isset($_POST['fee_id'])) {
    $feeId = intval($_POST['fee_id']);
    $student_id = intval($_POST['student_id']);
    $amount = floatval($_POST['amount']);
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];
    if ($student_id && $amount > 0 && $status && $due_date) {
        $stmt = $conn->prepare("UPDATE fees SET student_id=?, amount=?, status=?, due_date=? WHERE id=?");
        $stmt->bind_param("idssi", $student_id, $amount, $status, $due_date, $feeId);
        if ($stmt->execute()) {
            $successmsg = "Fee record updated successfully.";
        } else {
            $errmsg = "Error updating fee record.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required and amount must be positive.";
    }
}

// Fetch fees
$fees = [];
$result = $conn->query("SELECT fees.*, students.fullname FROM fees LEFT JOIN students ON fees.student_id = students.id ORDER BY fees.id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fees[] = $row;
    }
}
?>

<div class="mb-4">
    <h4><?php echo $editMode ? "Edit Fee Record" : "Add New Fee Record"; ?></h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-danger"><?php echo $errmsg; ?></div>
    <?php elseif ($successmsg): ?>
        <div class="alert alert-success"><?php echo $successmsg; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <div class="col-md-3">
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>" <?php echo $editMode && $editFee['student_id'] == $student['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($student['fullname']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount" min="0" required value="<?php echo $editMode ? htmlspecialchars($editFee['amount']) : ''; ?>">
        </div>
        <div class="col-md-2">
            <select name="status" class="form-control" required>
                <option value="">Status</option>
                <option value="Paid" <?php echo $editMode && $editFee['status'] === 'Paid' ? 'selected' : ''; ?>>Paid</option>
                <option value="Unpaid" <?php echo $editMode && $editFee['status'] === 'Unpaid' ? 'selected' : ''; ?>>Unpaid</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="due_date" class="form-control" required value="<?php echo $editMode ? htmlspecialchars($editFee['due_date']) : ''; ?>">
        </div>
        <?php if ($editMode): ?>
            <input type="hidden" name="fee_id" value="<?php echo $editFee['id']; ?>">
            <div class="col-md-1">
                <button type="submit" name="update_fee" class="btn btn-warning w-100">Update</button>
            </div>
            <div class="col-md-1">
                <a href="manage-fees.php" class="btn btn-secondary w-100">Cancel</a>
            </div>
        <?php else: ?>
            <div class="col-md-2">
                <button type="submit" name="add_fee" class="btn btn-primary w-100">Add</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<div>
    <h4>Fee Records</h4>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($fees as $fee): ?>
            <tr>
                <td><?php echo htmlspecialchars($fee['id']); ?></td>
                <td><?php echo htmlspecialchars($fee['fullname']); ?></td>
                <td><?php echo htmlspecialchars($fee['amount']); ?></td>
                <td><?php echo htmlspecialchars($fee['status']); ?></td>
                <td><?php echo htmlspecialchars($fee['due_date']); ?></td>
                <td>
                    <a href="?action=edit&id=<?php echo $fee['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                    <a href="?action=delete&id=<?php echo $fee['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this fee record?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include('footer.php');
?>
