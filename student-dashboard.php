<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
$pageTitle = "Student Dashboard";
include('header.php');
?>

<div class="container mt-5">
    <h2 class="mb-4">Hostel Management - Student Dashboard</h2>
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="view-room.php" class="btn btn-outline-primary btn-block btn-lg">View Room Details</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="view-fees.php" class="btn btn-outline-success btn-block btn-lg">View Fee Status</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="view-visitors.php" class="btn btn-outline-secondary btn-block btn-lg">Visitor Log</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="profile.php" class="btn btn-outline-info btn-block btn-lg">My Profile</a>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
