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
<body onload="checkAuth()">
    <div class="app-layout">

    <aside class="sidebar">
            <button id="sidebarToggle" class="btn-toggle" aria-label="Toggle Navigation" aria-expanded="true">
                ☰
            </button>
            <nav class="sidebar-menu">
                 <img src="logo.png" alt="Kinondoni MC Logo" class="sidebar-logo"
                 width="60" height="60" style="display: block; margin: 16px auto;">  
                <a href="home.php" class="nav-item active">
                    <span class="nav-icon">🏠</span>
                    <span>Home</span>
                </a>
               
                <a href="enrolled_list.html" class="nav-item">
                    <span class="nav-icon">📊</span>
                    <span>Enrolled Records</span>
                </a>
                <button onclick="logout(); return false;" class="nav-item btn-sidebar-logout">
                    <span class="nav-icon">🚪</span>
                    <span>Sign Out</span>
                </button>
            </nav>
        </aside>
