<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<style>
.role-corner-badge {
    position: absolute;
    top: -18px;
    left: -18px;
    z-index: 1050;
    min-width: 60px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1rem;
    border-radius: 19px 19px 19px 19px / 50% 50% 50% 50%;
    background: #ffc107;
    color: #212529;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    border: 2px solid #fff;
    pointer-events: none;
    text-transform: capitalize;
    letter-spacing: 0;
    padding: 0 10px;
    white-space: nowrap;
}
@media (max-width: 991px) {
    .role-corner-badge { left: -10px; width: 32px; height: 32px; font-size: 0.9rem; }
}
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary rounded mb-4 position-relative">
    <?php
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
    $loggedIn = isset($_SESSION['loginId']) && $role;
    if ($loggedIn) {
        $roleLabel = '';
        if ($role === 'admin') $roleLabel = 'Admin';
        elseif ($role === 'student') $roleLabel = 'Student';
        // Place the badge outside the navbar, top left
        echo '<span class="role-corner-badge">' . htmlspecialchars($roleLabel) . '</span>';
    }
    ?>
    <div class="container-fluid position-relative">
        <a class="navbar-brand" href="index.php">Hostel Management System</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <?php
                if ($loggedIn) {
                    // Dashboard link
                    if ($role === 'admin') {
                        echo '<li class="nav-item"><a class="nav-link" href="admin-dashboard.php">Dashboard</a></li>';
                    }elseif ($role === 'student') {
                        echo '<li class="nav-item"><a class="nav-link" href="student-dashboard.php">Dashboard</a></li>';
                    }
                    // Students
                    if (in_array($role, ['admin', 'staff'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="manage-students.php">Students</a></li>';
                    } elseif ($role === 'student') {
                        echo '<li class="nav-item"><a class="nav-link" href="view-room.php">Room</a></li>';
                    }
                    // Rooms
                    if (in_array($role, ['admin', 'staff'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="manage-rooms.php">Rooms</a></li>';
                    }
                    // Fees
                    if (in_array($role, ['admin', 'staff'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="manage-fees.php">Fees</a></li>';
                    } elseif ($role === 'student') {
                        echo '<li class="nav-item"><a class="nav-link" href="view-fees.php">Fees</a></li>';
                    }
                    // Visitors
                    if (in_array($role, ['admin', 'staff'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="manage-visitors.php">Visitors</a></li>';
                    } elseif ($role === 'student') {
                        echo '<li class="nav-item"><a class="nav-link" href="view-visitors.php">Visitors</a></li>';
                    }
                    // Reports
                    if (in_array($role, ['admin', 'staff'])) {
                        echo '<li class="nav-item"><a class="nav-link" href="reports.php">Reports</a></li>';
                    }
                    // Profile (all roles)
                    echo '<li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>';
                }
                ?>
            </ul>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <?php
                if ($loggedIn) {
                    echo '<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>';
                } else {
                    echo '<li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
</nav>
