<?php require_once __DIR__ . '/nav.php'; ?>

        <div class="main-content">
            <header class="navbar">
                <button id="sidebarToggle" class="btn-toggle" aria-label="Toggle Navigation" aria-expanded="true">
                    <svg class="toggle-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                        <rect x="3" y="6" width="18" height="2" rx="1"></rect>
                        <rect x="3" y="11" width="18" height="2" rx="1"></rect>
                        <rect x="3" y="16" width="18" height="2" rx="1"></rect>
                    </svg>
                </button>
                <div></div>
                <div class="logo-area">
                    <h1>Enrollment Dashboard</h1>
                    <p>Overview of field student enrollments</p>
                </div>
                <div class="user-badge">
                    <span>Training Officer</span>
                </div>
            </header>

            <div class="main-container">
                <?php
                // Database connection
                $host = "localhost";
                $user = "root";
                $pass = "";
                $db   = "kinondoni_pt_db";

                $conn = new mysqli($host, $user, $pass, $db);
                if ($conn->connect_error) {
                    echo '<section class="card"><h2>Database Error</h2><p>Unable to connect to database.</p></section>';
                } else {
                    // Total enrollments
                    $total_q = $conn->query("SELECT COUNT(*) AS total FROM field_students");
                    $total = $total_q ? intval($total_q->fetch_assoc()['total']) : 0;

                    // By education level
                    $levels_q = $conn->query("SELECT edu_level, COUNT(*) AS cnt FROM field_students GROUP BY edu_level");
                    $levels = [];
                    if ($levels_q) {
                        while ($r = $levels_q->fetch_assoc()) { $levels[] = $r; }
                    }

                    // By year of study
                    $years_q = $conn->query("SELECT year_of_study, COUNT(*) AS cnt FROM field_students GROUP BY year_of_study");
                    $years = [];
                    if ($years_q) {
                        while ($r = $years_q->fetch_assoc()) { $years[] = $r; }
                    }

                    // Enrollments by month (based on start_date)
                    $trend_q = $conn->query("SELECT DATE_FORMAT(start_date, '%Y-%m') AS ym, COUNT(*) AS cnt FROM field_students GROUP BY ym ORDER BY ym ASC");
                    $trend = [];
                    if ($trend_q) {
                        while ($r = $trend_q->fetch_assoc()) { $trend[] = $r; }
                    }

                    // By institution (top 6)
                    $inst_q = $conn->query("SELECT institution, COUNT(*) AS cnt FROM field_students GROUP BY institution ORDER BY cnt DESC LIMIT 6");
                    $institutions = [];
                    if ($inst_q) {
                        while ($r = $inst_q->fetch_assoc()) { $institutions[] = $r; }
                    }
                }
                ?>

                <section class="card metrics-grid">
                    <div class="metric-card">
                        <div>
                            <span class="metric-label">Total Enrollments</span>
                            <h2><?php echo number_format($total); ?></h2>
                        </div>
                    </div>
                    <!--<div class="metric-card">
                        <div>
                            <span class="metric-label">Education Levels</span>
                            <h2><?php echo number_format(array_sum(array_column($levels, 'cnt'))); ?></h2>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div>
                            <span class="metric-label">Years of Study</span>
                            <h2><?php echo number_format(array_sum(array_column($years, 'cnt'))); ?></h2>
                        </div>
                    </div>-->
                </section>

                <section class="card" style="margin-top:12px">
                    <h3>Enrollments Over Time</h3>
                    <div class="chart-panel">
                        <canvas id="trendChart" class="chart-small"></canvas>
                    </div>
                </section>

                <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:12px;">
                    <section class="card" style="flex:1 1 320px;">
                        <h3>By Education Level</h3>
                        <div class="chart-panel">
                            <canvas id="levelChart" class="chart-small"></canvas>
                        </div>
                    </section>

                    <section class="card" style="flex:1 1 320px;">
                        <h3>By Year of Study</h3>
                        <div class="chart-panel">
                            <canvas id="yearChart" class="chart-small"></canvas>
                        </div>
                    </section>

                    <section class="card" style="flex:1 1 320px;">
                        <h3>Top Institutions</h3>
                        <div class="chart-panel">
                            <canvas id="instChart" class="chart-small"></canvas>
                        </div>
                    </section>
                </div>

            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-panel { overflow: hidden; }
        .chart-small { display: block; width: 100%; height: 160px !important; }
    </style>
    <script>
        // Prepare data from PHP
        const levelData = <?php echo json_encode(array_values(array_column($levels, 'cnt')) ?: []); ?>;
        const levelLabels = <?php echo json_encode(array_values(array_column($levels, 'edu_level')) ?: []); ?>;

        const yearData = <?php echo json_encode(array_values(array_column($years, 'cnt')) ?: []); ?>;
        const yearLabels = <?php echo json_encode(array_values(array_column($years, 'year_of_study')) ?: []); ?>;

        const trendLabels = <?php echo json_encode(array_column($trend, 'ym') ?: []); ?>;
        const trendData = <?php echo json_encode(array_column($trend, 'cnt') ?: []); ?>;

        const instLabels = <?php echo json_encode(array_column($institutions, 'institution') ?: []); ?>;
        const instData = <?php echo json_encode(array_column($institutions, 'cnt') ?: []); ?>;

        function createBar(ctx, labels, data, label, color) {
            return new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{ label: label, data: data, backgroundColor: color }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // Trend (line)
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: { labels: trendLabels, datasets: [{ label: 'Enrollments', data: trendData, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,0.08)', fill: true }] },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Level
        createBar(document.getElementById('levelChart').getContext('2d'), levelLabels, levelData, 'Students', '#10b981');

        // Year
        createBar(document.getElementById('yearChart').getContext('2d'), yearLabels, yearData, 'Students', '#f59e0b');

        // Institutions (doughnut)
        const instCtx = document.getElementById('instChart').getContext('2d');
        new Chart(instCtx, {
            type: 'doughnut',
            data: { labels: instLabels, datasets: [{ data: instData, backgroundColor: ['#ef4444','#3b82f6','#10b981','#f59e0b','#8b5cf6','#06b6d4'] }] },
            options: { responsive: true, maintainAspectRatio: false }
        });
    </script>

    <script src="app.js"></script>
    <script src="login.js"></script>
</body>
</html>
