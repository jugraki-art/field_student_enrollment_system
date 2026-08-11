<!--<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Field Student Enrollment System - Kinondoni MC HQ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body onload="checkAuth()">
    <div class="app-layout">-->
        
        <?php require_once __DIR__ . '/nav.php'; ?>

       
        <div class="main-content">
            <header class="navbar">
                <div class="logo-area">
                    <h1>Field Student Enrollment Portal</h1>
                    <p>Kinondoni Municipal Council HQ - IT Department</p>
                </div>
                <div class="user-badge">
                    <span>Training Officer</span>
                </div>
            </header>

            <div class="main-container">
                <section class="card">
                    <h2>Welcome to the Field Enrollment System</h2>
                    <p style="margin-bottom: 16px; color: #475569; line-height: 1.6;">
                        Manage student enrollment activities efficiently from one welcoming dashboard.
                        Use the navigation panel to start enrollment, review records, or sign out securely.
                    </p>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <a href="index.html" class="btn btn-primary" style="width: auto; margin-top: 0; text-decoration: none; display: inline-block;">Enroll Student</a>
                        <a href="index.html#recordsTable" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">View Records</a>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
    <script src="login.js"></script>
</body>
</html>