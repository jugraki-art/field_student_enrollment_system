// Field Student Enrollment System - Main Client-Side Logic (app.js)

let currentStudentsList = [];

document.addEventListener('DOMContentLoaded', () => {
    // Run authentication check first
    if (typeof checkAuth === 'function') {
        checkAuth();
    }

    // Populate user badge in header if element exists
    const badgeName = document.getElementById('userBadgeName');
    if (badgeName) {
        const loggedUser = sessionStorage.getItem('loggedInUser') || 'Training Officer';
        badgeName.textContent = loggedUser;
    }

    // Initialize students list if table present on page
    if (document.getElementById('studentTableBody')) {
        loadStudents();
    }

    // Initialize enrollment form submit listener
    const form = document.getElementById('enrollmentForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Setup sidebar navigation collapse toggle & state persistence
    setupSidebarToggle();
});

// Load students from API asynchronously, falling back to LocalStorage on error
async function loadStudents() {
    const tbody = document.getElementById('studentTableBody');
    if (!tbody) return;

    try {
        const response = await fetch(getAppUrl('config/api.php?action=students'));
        if (!response.ok) {
            throw new Error(`API fetch failed with status ${response.status}`);
        }

        const students = await response.json();
        if (Array.isArray(students)) {
            currentStudentsList = students;
            // Update LocalStorage cache for offline fallback
            localStorage.setItem('field_students', JSON.stringify(students));
            renderTable(students);
        } else {
            throw new Error('API did not return a valid list array');
        }
    } catch (error) {
        console.warn('Unable to load students from API, using LocalStorage fallback:', error);
        const cached = JSON.parse(localStorage.getItem('field_students')) || [];
        currentStudentsList = cached;
        renderTable(cached);
    }
}

// Render Data Table dynamically into #studentTableBody
function renderTable(students) {
    const tbody = document.getElementById('studentTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    // Calculate today's date normalized to 00:00:00 for active state comparison
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();

    if (!Array.isArray(students) || students.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:30px;">No field students enrolled yet.</td></tr>`;
        return;
    }

    students.forEach((student, index) => {
        const id = student.student_id || student.id || (index + 1);
        const fullName = escapeHtml(student.fullName || student.full_name || '');
        const institution = escapeHtml(student.institution || '');
        const eduLevel = escapeHtml(student.eduLevel || student.edu_level || '');
        const yearOfStudy = escapeHtml(student.yearOfStudy || student.year_of_study || '');
        const startDate = student.startDate || student.start_date || '';
        const endDate = student.endDate || student.end_date || '';
        const phone = escapeHtml(student.phone || student.phone_number || '');

        // Calculate Active State: today <= endDate
        let isActive = false;
        if (endDate) {
            const parts = endDate.split('-');
            if (parts.length === 3) {
                const endTimestamp = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2])).getTime();
                isActive = today <= endTimestamp;
            } else {
                isActive = today <= new Date(endDate).getTime();
            }
        }

        const statusText = isActive ? 'Active' : 'Completed';
        const badgeClass = isActive ? 'badge-active' : 'badge-completed';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${fullName}</strong></td>
            <td>${institution}</td>
            <td>${eduLevel} <br><small style="color:#64748b">${yearOfStudy}</small></td>
            <td><small>${startDate} to ${endDate}</small></td>
            <td>${phone}</td>
            <td><span class="status-badge ${badgeClass}">${statusText}</span></td>
            <td>
                <button class="btn btn-danger btn-delete" data-id="${id}" data-index="${index}">Delete</button>
            </td>
        `;

        tbody.appendChild(row);

        const delBtn = row.querySelector('.btn-delete');
        if (delBtn) {
            delBtn.addEventListener('click', () => deleteStudent(id, index));
        }
    });
}

// Handle Form Submission with Strict Validations
async function handleFormSubmit(e) {
    e.preventDefault();

    const fullNameInput = document.getElementById('fullName');
    const institutionInput = document.getElementById('institution');
    const eduLevelInput = document.getElementById('eduLevel');
    const yearOfStudyInput = document.getElementById('yearOfStudy');
    const startDateInput = document.getElementById('startDate');
    const endDateInput = document.getElementById('endDate');
    const phoneInput = document.getElementById('phone');

    const fullName = fullNameInput ? fullNameInput.value.trim() : '';
    const institution = institutionInput ? institutionInput.value.trim() : '';
    const eduLevel = eduLevelInput ? eduLevelInput.value : '';
    const yearOfStudy = yearOfStudyInput ? yearOfStudyInput.value : '';
    const startDate = startDateInput ? startDateInput.value : '';
    const endDate = endDateInput ? endDateInput.value : '';
    const phone = phoneInput ? phoneInput.value.trim() : '';

    if (!fullName || !institution || !startDate || !endDate || !phone) {
        showAlert('formAlert', 'Please fill in all required form fields.', 'error');
        return;
    }

    // Validation 1: End Date > Start Date
    if (new Date(endDate) <= new Date(startDate)) {
        alert('SDLC Validation Error: Ending day must be chronological after the Starting day.');
        showAlert('formAlert', 'Validation Error: End date must be after start date.', 'error');
        return;
    }

    // Validation 2: Phone Number Validation (10 digits)
    const sanitizedPhone = phone.replace(/\s+/g, '');
    if (!/^[0-9]{10}$/.test(sanitizedPhone)) {
        alert('Validation Error: Please enter a valid 10-digit phone number.');
        showAlert('formAlert', 'Validation Error: Please enter a valid 10-digit phone number (e.g. 0712345678).', 'error');
        return;
    }

    const payload = {
        fullName,
        institution,
        eduLevel,
        yearOfStudy,
        startDate,
        endDate,
        phone: sanitizedPhone
    };

    try {
        const response = await fetch(getAppUrl('config/api.php'), {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            alert(result.message || 'Student enrolled successfully.');
            showAlert('formAlert', result.message || 'Student enrolled successfully.', 'success');
            
            // Reset form fields
            document.getElementById('enrollmentForm').reset();

            // Redirect to enrolled list view after successful enrollment
            setTimeout(() => {
                window.location.href = getAppUrl('modules/enrollment/enrolled_list.php');
            }, 800);
        } else {
            throw new Error(result.message || 'Error processing enrollment');
        }
    } catch (error) {
        console.warn('API enrollment post failed, caching locally:', error);
        
        // Save to LocalStorage fallback
        const newRecord = {
            id: Date.now(),
            student_id: Date.now(),
            fullName,
            institution,
            eduLevel,
            yearOfStudy,
            startDate,
            endDate,
            phone: sanitizedPhone,
            createdAt: new Date().toISOString()
        };

        const students = JSON.parse(localStorage.getItem('field_students')) || [];
        students.unshift(newRecord);
        localStorage.setItem('field_students', JSON.stringify(students));

        alert('Student enrolled locally (Offline fallback mode).');
        document.getElementById('enrollmentForm').reset();
        window.location.href = getAppUrl('modules/enrollment/enrolled_list.php');
    }
}

// Handle Delete Student Record cleanly with API DELETE call and confirmation prompt
async function deleteStudent(id, index) {
    if (!confirm('Are you sure you want to remove this field student record?')) {
        return;
    }

    try {
        // Perform server-side HTTP DELETE call to api.php
        const response = await fetch(`${getAppUrl('config/api.php')}?id=${encodeURIComponent(id)}`, {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: id })
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            alert('Record deleted successfully.');
            loadStudents();
        } else {
            throw new Error(result.message || 'Failed to delete record from server');
        }
    } catch (error) {
        console.warn('API delete failed, updating local state:', error);
        
        // LocalStorage fallback deletion
        let students = JSON.parse(localStorage.getItem('field_students')) || currentStudentsList;
        students = students.filter(s => (s.student_id || s.id) != id);
        
        if (typeof index === 'number' && index >= 0 && index < students.length) {
            students.splice(index, 1);
        }

        localStorage.setItem('field_students', JSON.stringify(students));
        currentStudentsList = students;
        renderTable(students);
        alert('Record deleted locally.');
    }
}

// Real-Time Search & Filter Table Logic
function filterTable() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;

    const query = searchInput.value.toLowerCase().trim();
    if (!query) {
        renderTable(currentStudentsList);
        return;
    }

    const filtered = currentStudentsList.filter(student => {
        const name = (student.fullName || student.full_name || '').toLowerCase();
        const inst = (student.institution || '').toLowerCase();
        const level = (student.eduLevel || student.edu_level || '').toLowerCase();
        const year = (student.yearOfStudy || student.year_of_study || '').toLowerCase();
        const phone = (student.phone || student.phone_number || '').toLowerCase();

        return name.includes(query) || 
               inst.includes(query) || 
               level.includes(query) || 
               year.includes(query) || 
               phone.includes(query);
    });

    renderTable(filtered);
}

// Export Table Data to Downloadable CSV file (kinondoni_field_students.csv)
function exportCSV() {
    const students = currentStudentsList.length > 0 
        ? currentStudentsList 
        : (JSON.parse(localStorage.getItem('field_students')) || []);

    if (students.length === 0) {
        alert('No student records available to export.');
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,Full Name,Institution,Level,Year of Study,Start Date,End Date,Phone,Status\n";

    const today = new Date().setHours(0, 0, 0, 0);

    students.forEach(s => {
        const fullName = (s.fullName || s.full_name || '').replace(/"/g, '""');
        const inst = (s.institution || '').replace(/"/g, '""');
        const level = (s.eduLevel || s.edu_level || '').replace(/"/g, '""');
        const year = (s.yearOfStudy || s.year_of_study || '').replace(/"/g, '""');
        const start = s.startDate || s.start_date || '';
        const end = s.endDate || s.end_date || '';
        const phone = (s.phone || s.phone_number || '').replace(/"/g, '""');

        const endDateVal = end ? new Date(end).setHours(0, 0, 0, 0) : 0;
        const status = today <= endDateVal ? 'Active' : 'Completed';

        csvContent += `"${fullName}","${inst}","${level}","${year}","${start}","${end}","${phone}","${status}"\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "kinondoni_field_students.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Handle Sidebar Collapse Toggling and State Persistence via localStorage.getItem('sidebarClosed')
function setupSidebarToggle() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const appLayout = document.querySelector('.app-layout');

    if (!appLayout) return;

    // Restore saved sidebar collapsed state
    const isClosed = localStorage.getItem('sidebarClosed') === 'true';
    if (isClosed) {
        appLayout.classList.add('sidebar-closed');
    }

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            appLayout.classList.toggle('sidebar-closed');
            const closedNow = appLayout.classList.contains('sidebar-closed');
            localStorage.setItem('sidebarClosed', closedNow ? 'true' : 'false');
            
            toggleBtn.setAttribute('aria-expanded', String(!closedNow));
            toggleBtn.setAttribute('aria-label', closedNow ? 'Open Navigation' : 'Toggle Navigation');
        });
    }

    // Responsive window resize handling
    window.addEventListener('resize', () => {
        if (window.innerWidth <= 768) {
            appLayout.classList.remove('sidebar-closed');
        } else {
            if (localStorage.getItem('sidebarClosed') === 'true') {
                appLayout.classList.add('sidebar-closed');
            }
        }
    });
}

// Helper to escape HTML characters
function escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Display UI Alert Messages
function showAlert(elementId, message, type = 'error') {
    const box = document.getElementById(elementId);
    if (!box) return;

    box.style.display = 'block';
    box.className = `alert-box alert-${type}`;
    box.textContent = message;

    setTimeout(() => {
        box.style.display = 'none';
    }, 5000);
}
