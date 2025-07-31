<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
$pageTitle = "Admin Dashboard";
include('header.php');
?>

<div class="container mt-5">
    <h2 class="mb-4">Hostel Management - Admin Dashboard</h2>
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="manage-students.php" class="btn btn-outline-primary btn-block btn-lg">Manage Students</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="manage-rooms.php" class="btn btn-outline-success btn-block btn-lg">Manage Rooms</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="manage-fees.php" class="btn btn-outline-warning btn-block btn-lg">Manage Fees</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="manage-visitors.php" class="btn btn-outline-secondary btn-block btn-lg">Visitor Log</a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="reports.php" class="btn btn-outline-dark btn-block btn-lg">Reports</a>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
