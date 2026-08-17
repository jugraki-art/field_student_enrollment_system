<?php 
         require_once __DIR__ . '/nav.php';
         require_once __DIR__ . '/header.php';
         ?>

        

        <div class="main-content">
            <div class="main-container">
                <!-- Dynamic Records Table -->
                <section class="card table-card">
                    <div class="table-header">
                        <h2>Enrolled Field Students Records</h2>
                        <div class="table-actions">
                            <input type="text" id="searchInput" placeholder="Search by name or uni..." onkeyup="filterTable()">
                            <button class="btn btn-secondary" onclick="exportCSV()">Export CSV</button>
                            <button class="btn btn-secondary"><a href="add_enrollment.php">+Enrollment</a></button>
                        </div>
                    </div>

                    <div class="table-wrapper">
                        <table id="recordsTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Institution</th>
                                    <th>Level/Year</th>
                                    <th>Training Period</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentTableBody">
                                <!-- JavaScript injects dynamic rows here -->
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>

            


            

    <script src="app.js"></script>
</body>
</html>
