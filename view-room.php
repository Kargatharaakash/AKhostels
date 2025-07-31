<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
$pageTitle = "View Room";
include('header.php');
require('db-connect.php');

$studentId = intval($_SESSION['loginId']);
$room = null;
$student = null;
$errmsg = '';

$result = $conn->query("SELECT * FROM students WHERE id=$studentId LIMIT 1");
if ($result && $result->num_rows > 0) {
    $student = $result->fetch_assoc();
    if ($student['room_id']) {
        $roomId = intval($student['room_id']);
        $roomResult = $conn->query("SELECT * FROM rooms WHERE id=$roomId LIMIT 1");
        if ($roomResult && $roomResult->num_rows > 0) {
            $room = $roomResult->fetch_assoc();
        } else {
            $errmsg = "Room not found.";
        }
    } else {
        $errmsg = "You have not been assigned a room yet.";
    }
} else {
    $errmsg = "Student not found.";
}
?>

<div class="mb-4">
    <h4>My Room Details</h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-warning"><?php echo $errmsg; ?></div>
    <?php elseif ($room): ?>
        <table class="table table-bordered">
            <tr>
                <th>Room Number</th>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
            </tr>
            <tr>
                <th>Capacity</th>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
            </tr>
            <tr>
                <th>Current Occupancy</th>
                <td><?php echo htmlspecialchars($room['current_occupancy']); ?></td>
            </tr>
        </table>
    <?php endif; ?>
</div>

<?php
include('footer.php');
?>
