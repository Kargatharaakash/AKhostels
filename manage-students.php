<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}
$pageTitle = "Manage Students";
include('header.php');
require('db-connect.php');

// Handle add, edit, delete actions here (simplified for demo)
$action = isset($_GET['action']) ? $_GET['action'] : '';
$editId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$errmsg = '';
$successmsg = '';

// Add student
if (isset($_POST['add_student'])) {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $admission_date = $_POST['admission_date'];
    if ($fullname && $email && $city && $phone && $admission_date) {
        $stmt = $conn->prepare("INSERT INTO students (fullname, email, city, phone, admission_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $fullname, $email, $city, $phone, $admission_date);
        if ($stmt->execute()) {
            $successmsg = "Student added successfully.";
        } else {
            $errmsg = "Error adding student.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required.";
    }
}

// Delete student
if ($action === 'delete' && $editId) {
    $conn->query("DELETE FROM students WHERE id=$editId");
    $successmsg = "Student deleted.";
}

// Handle room assignment
if (isset($_POST['assign_room'])) {
    $student_id = intval($_POST['student_id']);
    $room_id = intval($_POST['room_id']);
    // Check room capacity
    $room = $conn->query("SELECT capacity, current_occupancy FROM rooms WHERE id=$room_id")->fetch_assoc();
    if ($room && $room['current_occupancy'] < $room['capacity']) {
        // Unassign student from previous room (if any)
        $prev = $conn->query("SELECT room_id FROM students WHERE id=$student_id")->fetch_assoc();
        if ($prev && $prev['room_id']) {
            $conn->query("UPDATE rooms SET current_occupancy = current_occupancy - 1 WHERE id=" . intval($prev['room_id']));
        }
        // Assign new room
        $conn->query("UPDATE students SET room_id=$room_id WHERE id=$student_id");
        $conn->query("UPDATE rooms SET current_occupancy = current_occupancy + 1 WHERE id=$room_id");
        $successmsg = "Room assigned successfully.";
    } else {
        $errmsg = "Room is full or does not exist.";
    }
}

// Fetch students
$students = [];
$result = $conn->query("SELECT * FROM students ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch rooms for assignment
$rooms = [];
$result = $conn->query("SELECT * FROM rooms ORDER BY room_number ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }
}
?>

<div class="mb-4">
    <h4>Add New Student</h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-danger"><?php echo $errmsg; ?></div>
    <?php elseif ($successmsg): ?>
        <div class="alert alert-success"><?php echo $successmsg; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <div class="col-md-3">
            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
        </div>
        <div class="col-md-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="col-md-2">
            <input type="date" name="admission_date" class="form-control" required>
        </div>
        <div class="col-md-1">
            <button type="submit" name="add_student" class="btn btn-primary w-100">Add</button>
        </div>
    </form>
</div>

<div class="mb-4">
    <h4>Assign Room to Student</h4>
    <form method="post" class="row g-3">
        <div class="col-md-4">
            <select name="student_id" class="form-control" required>
                <option value="">Select Student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo $student['id']; ?>">
                        <?php echo htmlspecialchars($student['fullname']) . " (" . htmlspecialchars($student['email']) . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <select name="room_id" class="form-control" required>
                <option value="">Select Room</option>
                <?php foreach ($rooms as $room): ?>
                    <option value="<?php echo $room['id']; ?>">
                        <?php echo htmlspecialchars($room['room_number']) . " (Capacity: " . $room['capacity'] . ", Occupied: " . $room['current_occupancy'] . ")"; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" name="assign_room" class="btn btn-success w-100">Assign Room</button>
        </div>
    </form>
</div>

<div>
    <h4>Student List</h4>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Admission Date</th>
                <th>Room</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td><?php echo htmlspecialchars($student['admission_date']); ?></td>
                <td>
                    <?php
                    if ($student['room_id']) {
                        $room = $conn->query("SELECT room_number FROM rooms WHERE id=" . intval($student['room_id']))->fetch_assoc();
                        echo $room ? htmlspecialchars($room['room_number']) : "N/A";
                    } else {
                        echo "Not Assigned";
                    }
                    ?>
                </td>
                <td>
                    <a href="?action=delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this student?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
