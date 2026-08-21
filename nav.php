<?php
// includes/nav.php
// Common navigation bar and sidebar menu component for Kinondoni MC HQ Field Student System

// Determine the current page name to apply 'active' styling class
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Student Enrollment System - Kinondoni MC HQ</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <div class="app-layout">

        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="LOGO.png" alt="Kinondoni MC Logo" class="sidebar-logo">
                <div class="sidebar-brand-text">
                    <h2>Kinondoni MC</h2>
                    <p>Field Student Portal</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="report.php" class="nav-item <?php echo ($current_page === 'report.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>

                <a href="enrolled_list.php" class="nav-item <?php echo ($current_page === 'enrolled_list.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">📋</span>
                    <span>Enrolled Records</span>
                </a>

                <a href="add_enrollment.php" class="nav-item <?php echo ($current_page === 'add_enrollment.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">📝</span>
                    <span>Enroll Student</span>
                </a>

                <a href="profile.php" class="nav-item <?php echo ($current_page === 'profile.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">👤</span>
                    <span>Profile</span>
                </a>

                <button onclick="logout(); return false;" class="nav-item btn-sidebar-logout">
                    <span class="nav-icon">🚪</span>
                    <span>Sign Out</span>
                </button>
            </nav>
        </aside>
