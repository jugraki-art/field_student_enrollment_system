<?php 
require_once __DIR__ . '/../../includes/nav.php';
?>

<div class="main-content">
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>

    <div class="main-container">
        <!-- Records Table Card -->
        <section class="card table-card">
            <div class="table-header">
                <div>
                    <h2>Enrolled Field Students Records</h2>
                    <p class="muted">Manage, search, filter, and export student placement records.</p>
                </div>
                <div class="table-actions">
                    <input type="text" id="searchInput" placeholder="Search by name or institution..." onkeyup="filterTable()" oninput="filterTable()">
                    <button class="btn btn-secondary" onclick="exportCSV()">
                        <span>📥 Export CSV</span>
                    </button>
                    <a href="<?php echo htmlspecialchars($base_url); ?>/modules/enrollment/add_enrollment.php" class="btn btn-primary" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                        <span>+ Enroll Student</span>
                    </a>
                </div>
            </div>

            <div id="tableAlert" class="alert-box" style="display: none;"></div>

            <div class="table-wrapper">
                <table id="recordsTable">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Institution</th>
                            <th>Level / Year</th>
                            <th>Training Period</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        <tr>
                            <td colspan="7" style="text-align:center; padding:30px; color:#94a3b8;">
                                Loading enrolled student records...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script src="<?php echo htmlspecialchars($base_url); ?>/assets/js/login.js"></script>
<script src="<?php echo htmlspecialchars($base_url); ?>/assets/js/app.js"></script>
</body>
</html>
