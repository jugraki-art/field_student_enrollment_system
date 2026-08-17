<?php
// includes/nav.php
// Common navigation bar for Duka Bora Inventory System

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
</head>
<body <?php //-onload="checkAuth()"?>>
    <div class="app-layout">

    <aside class="sidebar">
            <nav class="sidebar-menu">
                 <img src="logo.png" alt="Kinondoni MC Logo" class="sidebar-logo"
                 width="60" height="60" style="display: block; margin: 16px auto;">  
                
                <?php if ($current_page === 'report.php') { ?>
                    <a href="report.php" class="nav-item active">
                       <span class="nav-icon"><img src="dashboard-alt.svg" alt="Dashboard" class="nav-icon-img"></span>
                       <span>Dashboard</span>
                    </a>
                <?php } else { ?>
                    <a href="report.php" class="nav-item">
                        <span class="nav-icon"><img src="dashboard-alt.svg" alt="Dashboard" class="nav-icon-img" style="color:aliceblue;"></span>
                        <span>Dashboard</span>
                    </a>
                <?php } ?>
                <?php if ($current_page === 'profile.php') { ?>
                    <a href="profile.php" class="nav-item active">
                        <span class="nav-icon">👤</span>
                        <span>Profile</span>
                    </a>
                <?php } else { ?>
                    <a href="profile.php" class="nav-item">
                        <span class="nav-icon">👤</span>
                        <span>Profile</span>
                    </a>
                <?php } ?>
                <?php if ($current_page === 'add_enrollment.php') { ?>
                    <a href="add_enrollment.php" class="nav-item active">
                        <span class="nav-icon">📝</span>
                        <span>Enroll Student</span>
                    </a>
                <?php } ?>
                <?php if ($current_page === 'enrolled_list.php') { ?>
                    <a href="enrolled_list.php" class="nav-item active">
                        <span class="nav-icon">📊</span>
                        <span>Enrolled Records</span>
                    </a>
                <?php } else { ?>
                    <a href="enrolled_list.php" class="nav-item">
                        <span class="nav-icon">📊</span>
                        <span>Enrolled Records</span>
                    </a>
                <?php } ?>
                
                <button onclick="logout(); return false;" class="nav-item btn-sidebar-logout">
                    <span class="nav-icon">🚪</span>
                    <span>Sign Out</span>
                </button>
            </nav>
        </aside>
