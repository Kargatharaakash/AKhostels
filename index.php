<?php
if (!isset($_COOKIE['loginId']) || !isset($_COOKIE['role'])) {
    require("login.php");
    exit();
}

$role = $_COOKIE['role'];

if ($role === 'admin') {
    header('Location: admin-dashboard.php');
    exit();
}elseif ($role === 'student') {
    header('Location: student-dashboard.php');
    exit();
} else {
    require("login.php");
    exit();
}
?>
