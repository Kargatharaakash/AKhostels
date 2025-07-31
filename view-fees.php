<?php
session_start();
if (!isset($_SESSION['loginId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}
$pageTitle = "View Fees";
include('header.php');
require('db-connect.php');

$studentId = intval($_SESSION['loginId']);
$fees = [];
$result = $conn->query("SELECT * FROM fees WHERE student_id=$studentId ORDER BY due_date DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $fees[] = $row;
    }
}
?>

<div class="mb-4">
    <h4>My Fee Payment Status & History</h4>
    <?php if (empty($fees)): ?>
        <div class="alert alert-warning">No fee records found.</div>
    <?php else: ?>
        <table class="table table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Due Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fees as $fee): ?>
                <tr>
                    <td><?php echo htmlspecialchars($fee['amount']); ?></td>
                    <td><?php echo htmlspecialchars($fee['status']); ?></td>
                    <td><?php echo htmlspecialchars($fee['due_date']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
include('footer.php');
?>
