<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}
$pageTitle = "Manage Visitors";
include('header.php');
require('db-connect.php');

$errmsg = '';
$successmsg = '';
// Fetch students for dropdown
$students = [];
$result = $conn->query("SELECT id, fullname FROM students ORDER BY fullname ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Handle add visitor
if (isset($_POST['add_visitor'])) {
    $student_id = intval($_POST['student_id']);
    $visitor_name = trim($_POST['visitor_name']);
    $visit_date = $_POST['visit_date'];
    if ($student_id && $visitor_name && $visit_date) {
        $stmt = $conn->prepare("INSERT INTO visitors (student_id, visitor_name, visit_date) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $student_id, $visitor_name, $visit_date);
        if ($stmt->execute()) {
            $successmsg = "Visitor log added successfully.";
        } else {
            $errmsg = "Error adding visitor log.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required.";
    }
}

// Handle delete visitor
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $visitorId = intval($_GET['id']);
    $conn->query("DELETE FROM visitors WHERE id=$visitorId");
    $successmsg = "Visitor log deleted.";
}

// Fetch visitors
$visitors = [];
$result = $conn->query("SELECT visitors.*, students.fullname FROM visitors LEFT JOIN students ON visitors.student_id = students.id ORDER BY visitors.visit_date ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $visitors[] = $row;
    }
}
?>

<div class="mb-4">
    <h4>Add Visitor Log</h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-danger"><?php echo $errmsg; ?></div>
    <?php elseif ($successmsg): ?>
        <div class="alert alert-success"><?php echo $successmsg; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <div class="col-md-4">
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>"><?php echo htmlspecialchars($student['fullname']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="visitor_name" class="form-control" placeholder="Visitor Name" required>
        </div>
        <div class="col-md-3">
            <input type="date" name="visit_date" class="form-control" required>
        </div>
        <div class="col-md-1">
            <button type="submit" name="add_visitor" class="btn btn-primary w-100">Add</button>
        </div>
    </form>
</div>

<div>
    <h4>Visitor & Guest Log</h4>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Visitor Name</th>
                <th>Visit Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visitors as $visitor): ?>
            <tr>
                <td><?php echo htmlspecialchars($visitor['id']); ?></td>
                <td><?php echo htmlspecialchars($visitor['fullname']); ?></td>
                <td><?php echo htmlspecialchars($visitor['visitor_name']); ?></td>
                <td><?php echo htmlspecialchars($visitor['visit_date']); ?></td>
                <td>
                    <a href="?action=delete&id=<?php echo $visitor['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this visitor log?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include('footer.php');
?>
