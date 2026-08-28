<?php
require_once __DIR__ . '/nav.php';
?>

<div class="main-content">
    <?php require_once __DIR__ . '/header.php'; ?>

    <div class="main-container">
        <div class="page-header">
            <div>
                <h2>User Profile & Account</h2>
                <p class="muted">Manage official Training Officer details and portal settings.</p>
            </div>
        </div>

        <section class="card profile-card">
            <div id="profileAlert" class="alert-box" style="display: none;"></div>

            <div class="profile-grid">
                <div class="profile-photo">
                    <img id="profileAvatar" src="user.svg" alt="Profile Avatar" width="120" height="120" style="border-radius:12px; border:3px solid #e2e8f0;">
                </div>
                <div class="profile-info">
                    <h2 id="displayName">Training Officer</h2>
                    <p class="muted" id="displayRole"><b>Role:</b> Training Officer</p>
                    <p id="displayDept"><b>Department:</b> IT & Training Office</p>
                    <p id="displayEmail"><b>Email:</b> training@kinondoni.go.tz</p>
                    <p id="displayPhone"><b>Phone:</b> 0712345678</p>

                    <div style="margin-top:16px;">
                        <button id="editBtn" class="btn btn-secondary">Edit Profile</button>
                        <button id="saveBtn" class="btn btn-primary" style="display:none; width:auto; padding: 8px 20px;">Save Profile</button>
                    </div>
                </div>
            </div>

            <div class="profile-bio" style="margin-top: 24px;">
                <h3>About Officer</h3>
                <p id="displayBio" style="margin-top: 8px; color: #475569; line-height: 1.6;">
                    Responsible for coordinating field student practical training placements and managing enrollment records for Kinondoni Municipal Council HQ.
                </p>
            </div>

            <form id="profileForm" style="display:none; margin-top:24px; border-top:1px solid #e2e8f0; padding-top:20px;">
                <h3 style="margin-bottom: 15px; color:#1e293b;">Edit Information</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="inputName">Full Name</label>
                        <input id="inputName" type="text" placeholder="e.g. Training Officer">
                    </div>
                    <div class="form-group">
                        <label for="inputRole">Role / Position</label>
                        <input id="inputRole" type="text" placeholder="e.g. Training Officer">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="inputDept">Department</label>
                        <input id="inputDept" type="text" placeholder="e.g. IT & Training Office">
                    </div>
                    <div class="form-group">
                        <label for="inputPhone">Phone Number</label>
                        <input id="inputPhone" type="text" placeholder="e.g. 0712345678">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail">Official Email</label>
                    <input id="inputEmail" type="email" placeholder="e.g. training@kinondoni.go.tz">
                </div>
                <div class="form-group">
                    <label for="inputBio">Profile Bio</label>
                    <textarea id="inputBio" rows="4" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px;"></textarea>
                </div>
            </form>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const defaults = {
        name: sessionStorage.getItem('loggedInUser') || 'Training Officer',
        role: 'Training Officer',
        dept: 'IT & Training Office',
        email: 'training@kinondoni.go.tz',
        phone: '0712345678',
        bio: 'Responsible for coordinating field student practical training placements and managing enrollment records for Kinondoni Municipal Council HQ.'
    };

    const saved = JSON.parse(localStorage.getItem('profile_training_officer') || '{}');
    const profile = Object.assign({}, defaults, saved);

    document.getElementById('displayName').textContent = profile.name;
    document.getElementById('displayRole').innerHTML = '<b>Role:</b> ' + profile.role;
    document.getElementById('displayDept').innerHTML = '<b>Department:</b> ' + profile.dept;
    document.getElementById('displayEmail').innerHTML = '<b>Email:</b> ' + profile.email;
    document.getElementById('displayPhone').innerHTML = '<b>Phone:</b> ' + profile.phone;
    document.getElementById('displayBio').textContent = profile.bio;

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
    });

    saveBtn.addEventListener('click', (e) => {
        e.preventDefault();
        const updated = {
            name: document.getElementById('inputName').value.trim() || defaults.name,
            role: document.getElementById('inputRole').value.trim() || defaults.role,
            dept: document.getElementById('inputDept').value.trim() || defaults.dept,
            email: document.getElementById('inputEmail').value.trim() || defaults.email,
            phone: document.getElementById('inputPhone').value.trim() || defaults.phone,
            bio: document.getElementById('inputBio').value.trim() || defaults.bio
        };

        localStorage.setItem('profile_training_officer', JSON.stringify(updated));

        document.getElementById('displayName').textContent = updated.name;
        document.getElementById('displayRole').innerHTML = '<b>Role:</b> ' + updated.role;
        document.getElementById('displayDept').innerHTML = '<b>Department:</b> ' + updated.dept;
        document.getElementById('displayEmail').innerHTML = '<b>Email:</b> ' + updated.email;
        document.getElementById('displayPhone').innerHTML = '<b>Phone:</b> ' + updated.phone;
        document.getElementById('displayBio').textContent = updated.bio;

        profileForm.style.display = 'none';
        saveBtn.style.display = 'none';
        editBtn.style.display = 'inline-block';
        alert('Profile saved successfully.');
    });
});
</script>

<script src="login.js"></script>
<script src="app.js"></script>
</body>
</html>
