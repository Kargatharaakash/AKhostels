<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit();
}
$pageTitle = "Manage Rooms";
include('header.php');
require('db-connect.php');

$errmsg = '';
$successmsg = '';
$editMode = false;
$editRoom = null;

// Handle add room
if (isset($_POST['add_room'])) {
    $room_number = trim($_POST['room_number']);
    $capacity = intval($_POST['capacity']);
    if ($room_number && $capacity > 0) {
        $stmt = $conn->prepare("INSERT INTO rooms (room_number, capacity, current_occupancy) VALUES (?, ?, 0)");
        $stmt->bind_param("si", $room_number, $capacity);
        if ($stmt->execute()) {
            $successmsg = "Room added successfully.";
        } else {
            $errmsg = "Error adding room. Room number may already exist.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required and capacity must be positive.";
    }
}

// Handle delete room
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $roomId = intval($_GET['id']);
    $conn->query("DELETE FROM rooms WHERE id=$roomId");
    $successmsg = "Room deleted.";
}

// Handle edit room (show form)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editMode = true;
    $roomId = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM rooms WHERE id=$roomId LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $editRoom = $result->fetch_assoc();
    }
}

// Handle update room
if (isset($_POST['update_room']) && isset($_POST['room_id'])) {
    $roomId = intval($_POST['room_id']);
    $room_number = trim($_POST['room_number']);
    $capacity = intval($_POST['capacity']);
    if ($room_number && $capacity > 0) {
        $stmt = $conn->prepare("UPDATE rooms SET room_number=?, capacity=? WHERE id=?");
        $stmt->bind_param("sii", $room_number, $capacity, $roomId);
        if ($stmt->execute()) {
            $successmsg = "Room updated successfully.";
        } else {
            $errmsg = "Error updating room.";
        }
        $stmt->close();
    } else {
        $errmsg = "All fields are required and capacity must be positive.";
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
?>

<div class="mb-4">
    <h4><?php echo $editMode ? "Edit Room" : "Add New Room"; ?></h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-danger"><?php echo $errmsg; ?></div>
    <?php elseif ($successmsg): ?>
        <div class="alert alert-success"><?php echo $successmsg; ?></div>
    <?php endif; ?>
    <form method="post" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="room_number" class="form-control" placeholder="Room Number" required value="<?php echo $editMode ? htmlspecialchars($editRoom['room_number']) : ''; ?>">
        </div>
        <div class="col-md-4">
            <input type="number" name="capacity" class="form-control" placeholder="Capacity" min="1" required value="<?php echo $editMode ? htmlspecialchars($editRoom['capacity']) : ''; ?>">
        </div>
        <?php if ($editMode): ?>
            <input type="hidden" name="room_id" value="<?php echo $editRoom['id']; ?>">
            <div class="col-md-2">
                <button type="submit" name="update_room" class="btn btn-warning w-100">Update</button>
            </div>
            <div class="col-md-2">
                <a href="manage-rooms.php" class="btn btn-secondary w-100">Cancel</a>
            </div>
        <?php else: ?>
            <div class="col-md-2">
                <button type="submit" name="add_room" class="btn btn-primary w-100">Add</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<div>
    <h4>Room List</h4>
    <table class="table table-striped table-bordered align-middle">
        <thead class="table-primary">
            <tr>
                <th>ID</th>
                <th>Room Number</th>
                <th>Capacity</th>
                <th>Current Occupancy</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
            <tr>
                <td><?php echo htmlspecialchars($room['id']); ?></td>
                <td><?php echo htmlspecialchars($room['room_number']); ?></td>
                <td><?php echo htmlspecialchars($room['capacity']); ?></td>
                <td><?php echo htmlspecialchars($room['current_occupancy']); ?></td>
                <td>
                    <a href="?action=edit&id=<?php echo $room['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                    <a href="?action=delete&id=<?php echo $room['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this room?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
include('footer.php');
?>
