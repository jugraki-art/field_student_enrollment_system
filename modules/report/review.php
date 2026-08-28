<?php 
require_once __DIR__ . '/../../includes/nav.php';
?>

<div class="main-content">
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>

    <div class="main-container">
        <!-- Page Header & Action Controls -->
        <div class="report-header-section card mb-4">
            <div class="report-title-area">
                <h2>Field Student Enrollment & Placement Report</h2>
                <p class="text-muted">Kinondoni Municipal Council HQ &mdash; Practical Training Monitoring & Analytics</p>
            </div>
            <div class="report-actions">
                <button onclick="window.print()" class="btn btn-secondary action-btn">
                    🖨️ Print / Save PDF
                </button>
                <button onclick="exportTableToCSV('kinondoni_field_report.csv')" class="btn btn-primary action-btn">
                    📥 Export CSV
                </button>
            </div>
        </div>

        <?php
        // Database connection & Data Aggregation
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "kinondoni_pt_db";

        // Filter parameters
        $filter_edu_level   = isset($_GET['edu_level']) ? trim($_GET['edu_level']) : '';
        $filter_institution = isset($_GET['institution']) ? trim($_GET['institution']) : '';
        $filter_status      = isset($_GET['status']) ? trim($_GET['status']) : '';
        $filter_start_date  = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $filter_end_date    = isset($_GET['end_date']) ? trim($_GET['end_date']) : '';

        $total           = 0;
        $diploma_count   = 0;
        $degree_count    = 0;
        $certificate_cnt = 0;
        $active_count    = 0;
        $completed_count = 0;
        
        $levels       = [];
        $years        = [];
        $trend        = [];
        $institutions = [];
        $distinct_inst= [];
        $report_data  = [];

        $conn = @new mysqli($host, $user, $pass, $db);
        if ($conn && !$conn->connect_error) {
            $conn->set_charset("utf8mb4");

            // Fetch distinct institutions for filter dropdown
            $inst_list_q = $conn->query("SELECT DISTINCT institution FROM field_students WHERE institution IS NOT NULL AND institution != '' ORDER BY institution ASC");
            if ($inst_list_q) {
                while ($row = $inst_list_q->fetch_assoc()) {
                    $distinct_inst[] = $row['institution'];
                }
            }

            // Summary Metrics
            $total_q = $conn->query("SELECT COUNT(*) AS total FROM field_students");
            $total   = $total_q ? intval($total_q->fetch_assoc()['total']) : 0;

            $cert_q = $conn->query("SELECT COUNT(*) AS cnt FROM field_students WHERE edu_level = 'Certificate'");
            $certificate_cnt = $cert_q ? intval($cert_q->fetch_assoc()['cnt']) : 0;

            $diploma_q = $conn->query("SELECT COUNT(*) AS cnt FROM field_students WHERE edu_level = 'Diploma'");
            $diploma_count = $diploma_q ? intval($diploma_q->fetch_assoc()['cnt']) : 0;

            $degree_q = $conn->query("SELECT COUNT(*) AS cnt FROM field_students WHERE edu_level LIKE '%Degree%' OR edu_level LIKE '%Bachelor%'");
            $degree_count = $degree_q ? intval($degree_q->fetch_assoc()['cnt']) : max(0, $total - ($certificate_cnt + $diploma_count));

            $active_q = $conn->query("SELECT COUNT(*) AS cnt FROM field_students WHERE end_date >= CURDATE()");
            $active_count = $active_q ? intval($active_q->fetch_assoc()['cnt']) : 0;
            $completed_count = max(0, $total - $active_count);

            // Groupings for Charts
            $levels_q = $conn->query("SELECT edu_level, COUNT(*) AS cnt FROM field_students WHERE edu_level IS NOT NULL AND edu_level != '' GROUP BY edu_level ORDER BY cnt DESC");
            if ($levels_q) {
                while ($r = $levels_q->fetch_assoc()) { $levels[] = $r; }
            }

            $years_q = $conn->query("SELECT year_of_study, COUNT(*) AS cnt FROM field_students WHERE year_of_study IS NOT NULL GROUP BY year_of_study ORDER BY year_of_study ASC");
            if ($years_q) {
                while ($r = $years_q->fetch_assoc()) { $years[] = $r; }
            }

            $trend_q = $conn->query("SELECT DATE_FORMAT(start_date, '%Y-%m') AS ym, COUNT(*) AS cnt FROM field_students WHERE start_date IS NOT NULL GROUP BY ym ORDER BY ym ASC");
            if ($trend_q) {
                while ($r = $trend_q->fetch_assoc()) { $trend[] = $r; }
            }

            $inst_q = $conn->query("SELECT institution, COUNT(*) AS cnt FROM field_students WHERE institution IS NOT NULL AND institution != '' GROUP BY institution ORDER BY cnt DESC LIMIT 6");
            if ($inst_q) {
                while ($r = $inst_q->fetch_assoc()) { $institutions[] = $r; }
            }

            // Build Filtered Detailed Query for the Report Table
            $where = ["1=1"];
            if (!empty($filter_edu_level)) {
                $where[] = "edu_level = '" . $conn->real_escape_string($filter_edu_level) . "'";
            }
            if (!empty($filter_institution)) {
                $where[] = "institution = '" . $conn->real_escape_string($filter_institution) . "'";
            }
            if (!empty($filter_start_date)) {
                $where[] = "start_date >= '" . $conn->real_escape_string($filter_start_date) . "'";
            }
            if (!empty($filter_end_date)) {
                $where[] = "end_date <= '" . $conn->real_escape_string($filter_end_date) . "'";
            }
            if ($filter_status === 'Active') {
                $where[] = "end_date >= CURDATE()";
            } elseif ($filter_status === 'Completed') {
                $where[] = "end_date < CURDATE()";
            }

            $where_sql = implode(" AND ", $where);
            $report_q = $conn->query("SELECT * FROM field_students WHERE $where_sql ORDER BY created_at DESC LIMIT 5");
            if ($report_q) {
                while ($row = $report_q->fetch_assoc()) {
                    $report_data[] = $row;
                }
            }

            $conn->close();
        }
        ?>

        <!-- Filter Controls Form -->
        <section class="card filter-card mb-4">
            <div class="card-header">
                <h3>Filter Report Parameters</h3>
            </div>
            <form method="GET" action="" class="filter-form">
                <div class="filter-grid">
                    <div class="form-group">
                        <label for="edu_level">Education Level</label>
                        <select name="edu_level" id="edu_level" class="form-control">
                            <option value="">All Levels</option>
                            <option value="Certificate" <?php echo ($filter_edu_level === 'Certificate') ? 'selected' : ''; ?>>Certificate</option>
                            <option value="Diploma" <?php echo ($filter_edu_level === 'Diploma') ? 'selected' : ''; ?>>Diploma</option>
                            <option value="Bachelor Degree" <?php echo ($filter_edu_level === 'Bachelor Degree' || $filter_edu_level === 'Degree') ? 'selected' : ''; ?>>Bachelor Degree</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="institution">Institution</label>
                        <select name="institution" id="institution" class="form-control">
                            <option value="">All Institutions</option>
                            <?php foreach ($distinct_inst as $inst_name): ?>
                                <option value="<?php echo htmlspecialchars($inst_name); ?>" <?php echo ($filter_institution === $inst_name) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($inst_name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Placement Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Active" <?php echo ($filter_status === 'Active') ? 'selected' : ''; ?>>Active</option>
                            <option value="Completed" <?php echo ($filter_status === 'Completed') ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="start_date">From (Start Date)</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?php echo htmlspecialchars($filter_start_date); ?>">
                    </div>

                    <div class="form-group">
                        <label for="end_date">To (End Date)</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?php echo htmlspecialchars($filter_end_date); ?>">
                    </div>

                    <div class="form-group filter-actions">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">Apply Filters</button>
                    </div>
                </div>
            </form>
        </section>

        <!-- KPI Metric Summary Cards -->
        <div class="metrics-grid mb-4">
            <div class="metric-card card">
                <div class="metric-icon icon-blue">👥</div>
                <div class="metric-info">
                    <span class="metric-label">Total Registered</span>
                    <h2><?php echo number_format($total); ?></h2>
                </div>
            </div>

            <div class="metric-card card">
                <div class="metric-icon icon-green">🟢</div>
                <div class="metric-info">
                    <span class="metric-label">Active Placements</span>
                    <h2><?php echo number_format($active_count); ?></h2>
                </div>
            </div>

            <div class="metric-card card">
                <div class="metric-icon icon-slate">⚪</div>
                <div class="metric-info">
                    <span class="metric-label">Completed Placements</span>
                    <h2><?php echo number_format($completed_count); ?></h2>
                </div>
            </div>

            <div class="metric-card card">
                <div class="metric-icon icon-purple">🎓</div>
                <div class="metric-info">
                    <span class="metric-label">Undergraduate</span>
                    <h2><?php echo number_format($degree_count); ?></h2>
                </div>
            </div>

            <div class="metric-card card">
                <div class="metric-icon icon-cyan">📜</div>
                <div class="metric-info">
                    <span class="metric-label">Certificates & Diplomas</span>
                    <h2><?php echo number_format($certificate_cnt + $diploma_count); ?></h2>
                </div>
            </div>

            <div class="metric-card card">
                <div class="metric-icon icon-amber">🏛️</div>
                <div class="metric-info">
                    <span class="metric-label">Institutions Represented</span>
                    <h2><?php echo number_format(count($distinct_inst) ?: count($institutions)); ?></h2>
                </div>
            </div>
        </div>

        <!-- Trend Line Chart -->
        <section class="card mb-4">
            <div class="card-header">
                <h3>Placement Trends Over Time</h3>
                <small class="text-muted">Monthly intake of field practical training students</small>
            </div>
            <div class="chart-panel" style="position: relative; height: 280px;">
                <canvas id="trendChart"></canvas>
            </div>
        </section>

        <!-- Visual Analytics Grid -->
        <div class="charts-grid mb-4">
            <section class="card">
                <div class="card-header">
                    <h3>By Education Level</h3>
                    <small class="text-muted">Proportion by academic qualification</small>
                </div>
                <div class="chart-panel" style="position: relative; height: 200px;">
                    <canvas id="levelChart"></canvas>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h3>By Year of Study</h3>
                    <small class="text-muted">Student academic progression</small>
                </div>
                <div class="chart-panel" style="position: relative; height: 200px;">
                    <canvas id="yearChart"></canvas>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <h3>Top Institutions</h3>
                    <small class="text-muted">Institutions with highest placement numbers</small>
                </div>
                <div class="chart-panel" style="position: relative; height: 260px;">
                    <canvas id="instChart"></canvas>
                </div>
            </section>
        </div>

        <!-- Tabular Detailed Report Breakdown -->
        <section class="card report-table-card">
            <div class="card-header d-flex justify-between align-center">
                <div>
                    <h3>Recent Student Placements Summary</h3>
                    <small class="text-muted">Showing <?php echo count($report_data); ?> record(s) of the recent enrolled student(s) matching current criteria</small>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table report-table" id="reportTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Institution</th>
                            <th>Education Level</th>
                            <th>Year of Study</th>
                            <th>Department / Unit</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($report_data)): ?>
                            <?php foreach ($report_data as $idx => $student): 
                                $is_active = isset($student['end_date']) && (strtotime($student['end_date']) >= strtotime(date('Y-m-d')));
                                $name = $student['full_name'] ?? ($student['first_name'] . ' ' . ($student['last_name'] ?? ''));
                            ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><strong><?php echo htmlspecialchars($name ?: 'N/A'); ?></strong></td>
                                    <td><?php echo htmlspecialchars($student['institution'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['edu_level'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(isset($student['year_of_study']) ? 'Year ' . $student['year_of_study'] : 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['department'] ?? $student['assigned_dept'] ?? 'General Admin'); ?></td>
                                    <td><?php echo htmlspecialchars($student['start_date'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($student['end_date'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge <?php echo $is_active ? 'badge-success' : 'badge-secondary'; ?>">
                                            <?php echo $is_active ? 'Active' : 'Completed'; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">No enrollment records found matching the specified criteria.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
            </div>
        </section>

        <!-- Quick Navigation to Full Enrolled Records Page -->
        <a href="<?php echo htmlspecialchars($base_url); ?>/modules/enrollment/enrolled_list.php" 
          class="btn btn-primary mt-3" 
           style="display: block; width: fit-content; margin-left: auto; padding: 0.5rem 1rem; font-size: 0.9rem; border-radius: 6px; background-color: #2563eb; color: #ffffff; text-decoration: none; font-weight: 500; transition: background-color 0.2s ease-in-out;">
           📋 View Full Enrolled Students Records &rarr;
       </a>
       
    </div>
</div>

<style>
/* Additional Report Page Styling */
.report-header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.25rem 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}
.report-title-area h2 {
    margin: 0 0 0.25rem 0;
    font-size: 1.45rem;
    color: #1e293b;
}
.report-actions {
    display: flex;
    gap: 0.75rem;
}
.filter-card {
    padding: 1.25rem 1.5rem;
}
.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    align-items: flex-end;
}
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
}
.form-group label {
    font-size: 0.85rem;
    font-weight: 600;
    color: #475569;
}
.form-control {
    padding: 0.55rem 0.75rem;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    font-size: 0.9rem;
    background-color: #fff;
}
.badge {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    font-size: 0.78rem;
    font-weight: 600;
    border-radius: 9999px;
}
.badge-success {
    background-color: #dcfce7;
    color: #166534;
}
.badge-secondary {
    background-color: #f1f5f9;
    color: #475569;
}
.table-responsive {
    overflow-x: auto;
}
.report-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0.5rem;
    font-size: 0.9rem;
}
.report-table th, .report-table td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e2e8f0;
    text-align: left;
}
.report-table th {
    background-color: #f8fafc;
    color: #334155;
    font-weight: 600;
}
.mb-4 {
    margin-bottom: 1.5rem;
}
.text-muted {
    color: #64748b;
}

/* Official Print Stylesheet */
@media print {
    nav, header, .report-actions, .filter-card, .btn {
        display: none !important;
    }
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data passed from PHP
    const levelLabels = <?php echo json_encode(array_values(array_column($levels, 'edu_level')) ?: ['Certificate', 'Diploma', 'Bachelor Degree']); ?>;
    const levelData   = <?php echo json_encode(array_values(array_column($levels, 'cnt')) ?: [$certificate_cnt, $diploma_count, $degree_count]); ?>;

    const yearLabels  = <?php echo json_encode(array_map(function($y){ return 'Year ' . $y; }, array_column($years, 'year_of_study')) ?: ['Year 1', 'Year 2', 'Year 3', 'Year 4']); ?>;
    const yearData    = <?php echo json_encode(array_values(array_column($years, 'cnt')) ?: [4, 6, 5, 2]); ?>;

    const trendLabels = <?php echo json_encode(array_column($trend, 'ym') ?: ['2026-05', '2026-06', '2026-07', '2026-08']); ?>;
    const trendData   = <?php echo json_encode(array_map('intval', array_column($trend, 'cnt')) ?: [3, 7, 12, 16]); ?>;

    const instLabels  = <?php echo json_encode(array_column($institutions, 'institution') ?: ['UDSM', 'DIT', 'IFM', 'CBE', 'MUST']); ?>;
    const instData    = <?php echo json_encode(array_map('intval', array_column($institutions, 'cnt')) ?: []); ?>;

    function renderCharts() {
        // Trend Line Chart
        const trendCtx = document.getElementById('trendChart')?.getContext('2d');
        if (trendCtx) {
            new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Enrollments',
                        data: trendData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.12)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        // Education Level Doughnut Chart
        const levelCtx = document.getElementById('levelChart')?.getContext('2d');
        if (levelCtx) {
            new Chart(levelCtx, {
                type: 'doughnut',
                data: {
                    labels: levelLabels,
                    datasets: [{
                        data: levelData,
                        backgroundColor: ['#06b6d4', '#3b82f6', '#8b5cf6', '#10b981']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // Year Bar Chart
        const yearCtx = document.getElementById('yearChart')?.getContext('2d');
        if (yearCtx) {
            new Chart(yearCtx, {
                type: 'bar',
                data: {
                    labels: yearLabels,
                    datasets: [{
                        label: 'Enrolled Students',
                        data: yearData,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 } }
                    }
                }
            });
        }

        // Top Institutions Bar/Doughnut Chart
        const instCtx = document.getElementById('instChart')?.getContext('2d');
        if (instCtx) {
            new Chart(instCtx, {
                type: 'doughnut',
                data: {
                    labels: instLabels,
                    datasets: [{
                        data: instData,
                        backgroundColor: ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    }

    // CSV Export Utility Function
    function exportTableToCSV(filename) {
        let csv = [];
        const rows = document.querySelectorAll("#reportTable tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                let text = cols[j].innerText.replace(/"/g, '""').trim();
                row.push('"' + text + '"');
            }
            csv.push(row.join(","));
        }

        const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
        const downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }

    document.addEventListener('DOMContentLoaded', renderCharts);
</script>

<script src="<?php echo htmlspecialchars($base_url ?? ''); ?>/assets/js/login.js"></script>
<script src="<?php echo htmlspecialchars($base_url ?? ''); ?>/assets/js/app.js"></script>
</body>
</html>
