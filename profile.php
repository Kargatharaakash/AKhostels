<?php
session_start();
$pageTitle = "My Profile";
include('header.php');
require('db-connect.php');

$errmsg = '';
$successmsg = '';

if (!isset($_SESSION['loginId']) || !isset($_SESSION['role'])) {
    echo '<div class="alert alert-danger">You must be logged in to view this page.</div>';
    include('footer.php');
    exit();
}

$loginId = intval($_SESSION['loginId']);
$role = $_SESSION['role'];
$user = null;

// Handle update
if (isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    if ($role === 'student') {
        $admission_date = $_POST['admission_date'];
        if ($name && $email && $admission_date) {
            $stmt = $conn->prepare("UPDATE students SET fullname=?, email=?, admission_date=? WHERE id=?");
            $stmt->bind_param("sssi", $name, $email, $admission_date, $loginId);
            if ($stmt->execute()) {
                $successmsg = "Profile updated successfully.";
            } else {
                $errmsg = "Error updating profile.";
            }
            $stmt->close();
        } else {
            $errmsg = "All fields are required.";
        }
    } elseif ($role === 'staff') {
        if ($name && $email) {
            $stmt = $conn->prepare("UPDATE staff SET name=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $email, $loginId);
            if ($stmt->execute()) {
                $successmsg = "Profile updated successfully.";
            } else {
                $errmsg = "Error updating profile.";
            }
            $stmt->close();
        } else {
            $errmsg = "All fields are required.";
        }
    } elseif ($role === 'admin') {
        if ($name && $email) {
            $stmt = $conn->prepare("UPDATE admins SET name=?, email=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $email, $loginId);
            if ($stmt->execute()) {
                $successmsg = "Profile updated successfully.";
            } else {
                $errmsg = "Error updating profile.";
            }
            $stmt->close();
        } else {
            $errmsg = "All fields are required.";
        }
    }
}

// Fetch user details
if ($role === 'student') {
    $result = $conn->query("SELECT * FROM students WHERE id=$loginId LIMIT 1");
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
}elseif ($role === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $loginId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
    $stmt->close();
}
?>

<div class="mb-4">
    <h4>My Profile</h4>
    <?php if ($errmsg): ?>
        <div class="alert alert-danger"><?php echo $errmsg; ?></div>
    <?php elseif ($successmsg): ?>
        <div class="alert alert-success"><?php echo $successmsg; ?></div>
    <?php endif; ?>

    <?php if ($user): ?>
    <form method="post" class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Full Name</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($role === 'student' ? $user['fullname'] : $user['name']); ?>" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <?php if ($role === 'student'): ?>
        <div class="col-md-4">
            <label class="form-label">Admission Date</label>
            <input type="date" name="admission_date" class="form-control" value="<?php echo htmlspecialchars($user['admission_date']); ?>" required>
        </div>
        <?php else: ?>
        <div class="col-md-4">
            <label class="form-label">Role</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($role); ?>" disabled>
        </div>
        <?php endif; ?>
        <div class="col-md-12">
            <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
    <?php else: ?>
        <div class="alert alert-warning">User not found.</div>
    <?php endif; ?>
</div>

<?php
include('footer.php');
?>
