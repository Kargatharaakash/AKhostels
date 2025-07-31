<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}
$pageTitle = "Reports";
include('header.php');
require('db-connect.php');

// Fetch students
$students = [];
$result = $conn->query("SELECT * FROM students ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Fetch rooms
$rooms = [];
$result = $conn->query("SELECT * FROM rooms ORDER BY id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
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

// Fetch visitors
$visitors = [];
$result = $conn->query("SELECT visitors.*, students.fullname FROM visitors LEFT JOIN students ON visitors.student_id = students.id ORDER BY visitors.id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $visitors[] = $row;
    }
}
?>

<div class="mb-4">
    <h4>Students Report</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Room ID</th>
                <th>Admission Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['id']); ?></td>
                <td><?php echo htmlspecialchars($student['fullname']); ?></td>
                <td><?php echo htmlspecialchars($student['email']); ?></td>
                <td><?php echo htmlspecialchars($student['room_id']); ?></td>
                <td><?php echo htmlspecialchars($student['admission_date']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mb-4">
    <h4>Rooms Report</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Room Number</th>
                <th>Capacity</th>
                <th>Current Occupancy</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['current_occupancy']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mb-4">
    <h4>Fees Report</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Due Date</th>
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
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="mb-4">
    <h4>Visitors Report</h4>
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Visitor Name</th>
                <th>Visit Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($visitors as $visitor): ?>
            <tr>
                <td><?php echo htmlspecialchars($visitor['id']); ?></td>
                <td><?php echo htmlspecialchars($visitor['fullname']); ?></td>
                <td><?php echo htmlspecialchars($visitor['visitor_name']); ?></td>
                <td><?php echo htmlspecialchars($visitor['visit_date']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include('footer.php');
?>
