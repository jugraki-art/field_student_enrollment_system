// Field Student Enrollment System - Frontend Logic

document.addEventListener('DOMContentLoaded', () => {
    loadStudents();

    const form = document.getElementById('enrollmentForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }
});

// Load students from API, falling back to LocalStorage when needed
async function loadStudents() {
    const tbody = document.getElementById('studentTableBody');
    if (!tbody) return;

    try {
        const response = await fetch('api.php');
        if (!response.ok) {
            throw new Error(`API fetch failed with status ${response.status}`);
        }

        const students = await response.json();
        renderTable(Array.isArray(students) ? students : []);
    } catch (error) {
        console.error('Unable to load students from API:', error);
        const students = JSON.parse(localStorage.getItem('field_students')) || [];
        renderTable(students);
    }
}

// Render Data Table
function renderTable(students) {
    const tbody = document.getElementById('studentTableBody');
    tbody.innerHTML = '';

    const today = new Date().setHours(0, 0, 0, 0);

    if (students.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" style="text-align:center; color:#94a3b8; padding:20px;">No field students enrolled yet.</td></tr>`;
        return;
    }

    students.forEach((student, index) => {
        const fullName = escapeHtml(student.fullName || student.full_name || '');
        const institution = escapeHtml(student.institution || '');
        const eduLevel = student.eduLevel || student.edu_level || '';
        const yearOfStudy = student.yearOfStudy || student.year_of_study || '';
        const startDate = student.startDate || student.start_date || '';
        const endDate = student.endDate || student.end_date || '';
        const phone = escapeHtml(student.phone || student.phone_number || '');

        const endDateValue = endDate ? new Date(endDate).setHours(0, 0, 0, 0) : today - 1;
        const isActive = today <= endDateValue;
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
            <td><button class="btn btn-danger" onclick="deleteStudent(${index})">Delete</button></td>
        `;
        tbody.appendChild(row);
    });
}

// Handle Form Submission with Validation
async function handleFormSubmit(e) {
    e.preventDefault();

    const fullName = document.getElementById('fullName').value.trim();
    const institution = document.getElementById('institution').value.trim();
    const eduLevel = document.getElementById('eduLevel').value;
    const yearOfStudy = document.getElementById('yearOfStudy').value;
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    const phone = document.getElementById('phone').value.trim();

    // System Logic Rule 1: End Date > Start Date
    if (new Date(endDate) <= new Date(startDate)) {
        alert('SDLC Validation Error: Ending day must be chronological after the Starting day.');
        return;
    }

    // System Logic Rule 2: Phone Number Validation (10 digits)
    if (!/^[0-9]{10}$/.test(phone.replace(/\s+/g, ''))) {
        alert('Validation Error: Please enter a valid 10-digit phone number.');
        return;
    }

    const newStudent = {
        id: Date.now(),
        fullName,
        institution,
        eduLevel,
        yearOfStudy,
        startDate,
        endDate,
        phone
    };

    const payload = {
        fullName,
        institution,
        eduLevel,
        yearOfStudy,
        startDate,
        endDate,
        phone
    };

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            throw new Error(`API request failed with status ${response.status}`);
        }

        const result = await response.json();
        if (result.status === 'success') {
            alert(result.message);
            document.getElementById('enrollmentForm').reset();
            loadStudents();
        } else {
            throw new Error(result.message || 'API error');
        }
    } catch (error) {
        console.error('Failed to save enrollment to API:', error);
        alert('Unable to save to database. Please try again later.');
    }
}

// Delete Record
function deleteStudent(index) {
    if (confirm('Are you sure you want to remove this record?')) {
        const students = JSON.parse(localStorage.getItem('field_students')) || [];
        students.splice(index, 1);
        localStorage.setItem('field_students', JSON.stringify(students));
        renderTable(students);
    }
}

// Search / Filter
function filterTable() {
    const input = document.getElementById('searchInput').value.toLowerCase();
    const students = JSON.parse(localStorage.getItem('field_students')) || [];
    
    const filtered = students.filter(s => 
        s.fullName.toLowerCase().includes(input) || 
        s.institution.toLowerCase().includes(input)
    );
    
    renderTable(filtered);
}

// Export to CSV
function exportCSV() {
    const students = JSON.parse(localStorage.getItem('field_students')) || [];
    if (students.length === 0) {
        alert('No data to export.');
        return;
    }

    let csvContent = "data:text/csv;charset=utf-8,Full Name,Institution,Level,Year,Start Date,End Date,Phone\n";
    
    students.forEach(s => {
        csvContent += `"${s.fullName}","${s.institution}","${s.eduLevel}","${s.yearOfStudy}","${s.startDate}","${s.endDate}","${s.phone}"\n`;
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "kinondoni_field_students.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

// Sidebar Open/Close Toggle
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const appLayout = document.querySelector('.app-layout');

    if (toggleBtn && appLayout) {
        toggleBtn.addEventListener('click', () => {
            appLayout.classList.toggle('sidebar-closed');
            const isClosed = appLayout.classList.contains('sidebar-closed');
            toggleBtn.setAttribute('aria-expanded', String(!isClosed));
            toggleBtn.setAttribute('aria-label', isClosed ? 'Open Navigation' : 'Toggle Navigation');
        });
    }
});
