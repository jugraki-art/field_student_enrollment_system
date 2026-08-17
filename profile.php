
         
                    <?php
                    $host = "localhost";
$user = "root";
$pass = "";
$db   = "kinondoni_pt_db";

$message = '';
$messageType = 'info';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    $message = 'Database connection failed: ' . $conn->connect_error;
    $messageType = 'error';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
    }
}
                    require_once __DIR__ . '/nav.php';
                    require_once __DIR__ . '/header.php';
                    ?>


                        <div class="main-content">
                            <div class="main-container">
                                <section class="card profile-card">
                                    <div class="profile-grid">
                                        <div class="profile-photo">
                                            <img id="profileAvatar" src="user.svg" alt="avatar" width="140" height="140" style="border-radius:8px; object-fit:cover;">
                                        </div>
                                        <div class="profile-info">
                                            <h2 id="displayName"><?php $username ?></h2>
                                            <p class="muted" id="displayRole"><b>Role:</b> Training Officer</p>
                                            <p id="displayDept"><b>Department:</b> IT & Training Office</p>
                                            <p id="displayEmail"><b>Email:</b> training@kinondoni.go.tz</p>
                                            <p id="displayPhone"><b>Phone:</b> 0712345678</p>

                                            <div style="margin-top:12px;">
                                                <button id="editBtn" class="btn btn-secondary">Edit Profile</button>
                                                <button id="saveBtn" class="btn btn-primary" style="display:none;">Save</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="profile-bio">
                                        <h3>About</h3>
                                        <p id="displayBio">Responsible for coordinating field student training placements and managing enrollment records for Kinondoni Municipal Council HQ.</p>
                                    </div>

                                    <form id="profileForm" style="display:none; margin-top:12px;">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="inputName">Full name</label>
                                                <input id="inputName" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label for="inputRole">Role</label>
                                                <input id="inputRole" type="text">
                                            </div>
                                        </div>
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="inputDept">Department</label>
                                                <input id="inputDept" type="text">
                                            </div>
                                            <div class="form-group">
                                                <label for="inputPhone">Phone</label>
                                                <input id="inputPhone" type="text">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="inputEmail">Email</label>
                                            <input id="inputEmail" type="email">
                                        </div>
                                        <div class="form-group">
                                            <label for="inputBio">Bio</label>
                                            <textarea id="inputBio" rows="4"></textarea>
                                        </div>
                                    </form>
                                </section>
                            </div>
                        </div>

                        <script>
                        // Populate training officer profile from sessionStorage/localStorage
                        document.addEventListener('DOMContentLoaded', () => {
                            const defaults = {
                                name: sessionStorage.getItem('loggedInUser') || 'Training Officer',
                                role: 'Training Officer',
                                dept: 'IT & Training Office',
                                email: 'training@kinondoni.go.tz',
                                phone: '0712345678',
                                bio: 'Responsible for coordinating field student training placements and managing enrollment records for Kinondoni Municipal Council HQ.'
                            };

                            // Load saved profile (if any)
                            const saved = JSON.parse(localStorage.getItem('profile_training_officer') || '{}');
                            const profile = Object.assign({}, defaults, saved);

                            // Display fields
                            document.getElementById('displayName').textContent = profile.name;
                            document.getElementById('displayRole').textContent = 'Role: ' + profile.role;
                            document.getElementById('displayDept').textContent = 'Department: ' + profile.dept;
                            document.getElementById('displayEmail').textContent = 'Email: ' + profile.email;
                            document.getElementById('displayPhone').textContent = 'Phone: ' + profile.phone;
                            document.getElementById('displayBio').textContent = profile.bio;

                            // Prefill edit inputs
                            document.getElementById('inputName').value = profile.name;
                            document.getElementById('inputRole').value = profile.role;
                            document.getElementById('inputDept').value = profile.dept;
                            document.getElementById('inputEmail').value = profile.email;
                            document.getElementById('inputPhone').value = profile.phone;
                            document.getElementById('inputBio').value = profile.bio;

                            const editBtn = document.getElementById('editBtn');
                            const saveBtn = document.getElementById('saveBtn');
                            const profileForm = document.getElementById('profileForm');

                            editBtn.addEventListener('click', () => {
                                profileForm.style.display = 'block';
                                saveBtn.style.display = 'inline-block';
                                editBtn.style.display = 'none';
                                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
                            });

                            saveBtn.addEventListener('click', () => {
                                const updated = {
                                    name: document.getElementById('inputName').value.trim() || defaults.name,
                                    role: document.getElementById('inputRole').value.trim() || defaults.role,
                                    dept: document.getElementById('inputDept').value.trim() || defaults.dept,
                                    email: document.getElementById('inputEmail').value.trim() || defaults.email,
                                    phone: document.getElementById('inputPhone').value.trim() || defaults.phone,
                                    bio: document.getElementById('inputBio').value.trim() || defaults.bio
                                };

                                localStorage.setItem('profile_training_officer', JSON.stringify(updated));

                                // Update display
                                document.getElementById('displayName').textContent = updated.name;
                                document.getElementById('displayRole').textContent = 'Role: ' + updated.role;
                                document.getElementById('displayDept').textContent = 'Department: ' + updated.dept;
                                document.getElementById('displayEmail').textContent = 'Email: ' + updated.email;
                                document.getElementById('displayPhone').textContent = 'Phone: ' + updated.phone;
                                document.getElementById('displayBio').textContent = updated.bio;

                                profileForm.style.display = 'none';
                                saveBtn.style.display = 'none';
                                editBtn.style.display = 'inline-block';
                                alert('Profile saved locally.');
                            });
                        });
                        </script>

                        <script src="app.js"></script>
                    </body>
                    </html>
