<?php 
require_once __DIR__ . '/../../includes/nav.php';
?>

<div class="main-content">
    <?php require_once __DIR__ . '/../../includes/header.php'; ?>

    <div class="main-container">
        <!-- Breadcrumb / Header Bar -->
        <div class="page-header">
            <div>
                <h2>Enroll New Field Student</h2>
                <p class="muted">Enter student personal and practical training placement details.</p>
            </div>
            <a href="<?php echo htmlspecialchars($base_url); ?>/modules/enrollment/enrolled_list.php" class="btn btn-secondary">
                <span>📋 View All Enrolled Records</span>
            </a>
        </div>

        <!-- Enrollment Form Card -->
        <section class="card form-card">
            <div id="formAlert" class="alert-box" style="display: none;"></div>

            <form id="enrollmentForm" novalidate>
                <div class="form-group">
                    <label for="fullName">Full Name <span class="req">*</span></label>
                    <input type="text" id="fullName" required placeholder="e.g. Juma Rashidi">
                </div>

                <div class="form-group">
                    <label for="institution">Uni / Institute / College <span class="req">*</span></label>
                    <input type="text" id="institution" required placeholder="e.g. UDSM, DIT, IFM, CBE, MUST">
                </div>
                <div class="form-group">
                    <label for="program">Program of Study <span class="req">*</span></label>
                    <input type="text" id="program" required placeholder="e.g. Computer Science, Business Administration">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="eduLevel">Level of Education <span class="req">*</span></label>
                        <select id="eduLevel" required>
                            <option value="Certificate">Certificate</option>
                            <option value="Diploma">Diploma</option>
                            <option value="Degree" selected>Degree</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="yearOfStudy">Year of Study <span class="req">*</span></label>
                        <select id="yearOfStudy" required>
                            <option value="Year 1">Year 1</option>
                            <option value="Year 2" selected>Year 2</option>
                            <option value="Year 3">Year 3</option>
                            <option value="Year 4">Year 4</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="startDate">Starting Day <span class="req">*</span></label>
                        <input type="date" id="startDate" required>
                    </div>

                    <div class="form-group">
                        <label for="endDate">Ending Day <span class="req">*</span></label>
                        <input type="date" id="endDate" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone">Contact Phone Info <span class="req">*</span></label>
                    <input type="tel" id="phone" required placeholder="e.g. 0712345678">
                    <small class="muted">Must be a valid 10-digit phone number (e.g., 0712345678)</small>
                </div>

                <div class="form-actions">
                    <button type="submit" id="btnSubmit" class="btn btn-primary">
                        <span>✨ Enroll Field Student</span>
                    </button>
                </div>
            </form>
        </section>
    </div>
</div>

<script src="<?php echo htmlspecialchars($base_url); ?>/assets/js/login.js"></script>
<script src="<?php echo htmlspecialchars($base_url); ?>/assets/js/app.js"></script>
</body>
</html>
