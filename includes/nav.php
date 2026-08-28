<?php
// includes/nav.php
// Common navigation bar and sidebar menu component for Kinondoni MC HQ Field Student System
require_once __DIR__ . '/../config/paths.php';
$base_url = app_base_url();

// Determine the current page name to apply 'active' styling class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Student Enrollment System - Kinondoni MC HQ</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($base_url); ?>/assets/css/style.css">
    <script>window.APP_BASE_URL = <?php echo json_encode($base_url); ?>;</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
</head>
<body>
    <div class="app-layout">

        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="<?php echo htmlspecialchars($base_url); ?>/Images/KMC.png" alt="Kinondoni MC Logo" class="sidebar-logo">
                <div class="sidebar-brand-text">
                    <h2>Kinondoni MC</h2>
                    <p>Field Student Portal</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="<?php echo htmlspecialchars($base_url); ?>/modules/dashboard/report.php" class="nav-item <?php echo ($current_page === 'report.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><img src= "<?php echo htmlspecialchars($base_url); ?>/Images/tachometer.svg" alt="Dashboard"></span>
                    <span>Dashboard</span>
                </a>

                <a href="<?php echo htmlspecialchars($base_url); ?>/modules/enrollment/enrolled_list.php" class="nav-item <?php echo ($current_page === 'enrolled_list.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><img src= "<?php echo htmlspecialchars($base_url); ?>/Images/clipboard.svg" alt="Records"></span>
                    <span>Enrolled Students</span>
                </a>

                <?php if ($current_page === 'enroll_student.php') { ?>
                    <a href="<?php echo htmlspecialchars($base_url); ?>/modules/enrollment/enroll_student.php" class="nav-item <?php echo ($current_page === 'enroll_student.php') ? 'active' : ''; ?>">
                        <span class="nav-icon"><img src="<?php echo htmlspecialchars($base_url); ?>/Images/user-plus.svg" alt="Enroll"></span>
                        <span>Enroll Student</span>
                    </a>
                <?php } ?>

                <a href="<?php echo htmlspecialchars($base_url); ?>/modules/report/review.php" class="nav-item <?php echo ($current_page === 'review.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><img src= "<?php echo htmlspecialchars($base_url); ?>/Images/file-report.svg" alt="Report"></span>
                    <span>Report</span>
                </a>

                <a href="<?php echo htmlspecialchars($base_url); ?>/modules/profile.php" class="nav-item <?php echo ($current_page === 'profile.php') ? 'active' : ''; ?>">
                    <span class="nav-icon"><img src= "<?php echo htmlspecialchars($base_url); ?>/Images/user.svg" alt="Profile"></span>
                    <span>Profile</span>
                </a>

                <button onclick="logout(); return false;" class="nav-item btn-sidebar-logout">
                    <span class="nav-icon"><img src= "<?php echo htmlspecialchars($base_url); ?>/Images/arrow-out.svg" alt="Signout"></span>
                    <span>Sign Out</span>
                </button>
            </nav>
        </aside>
