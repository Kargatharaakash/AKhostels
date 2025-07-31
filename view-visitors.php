<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
$pageTitle = "View Visitors";
include('header.php');
require('db-connect.php');

$studentId = intval($_SESSION['loginId']);
$visitors = [];
$result = $conn->query("SELECT * FROM visitors WHERE student_id=$studentId ORDER BY visit_date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $visitors[] = $row;
    }
}
?>

<div class="mb-4">
    <h4>My Visitor & Guest Log</h4>
    <?php if (empty($visitors)): ?>
        <div class="alert alert-warning">No visitor records found.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Visitor Name</th>
                    <th>Visit Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($visitors as $visitor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($visitor['visitor_name']); ?></td>
                    <td><?php echo htmlspecialchars($visitor['visit_date']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
include('footer.php');
?>
