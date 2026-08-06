// Field Student Enrollment System - Frontend Logic

document.addEventListener('DOMContentLoaded', () => {
    loadStudents();

    const form = document.getElementById('enrollmentForm');
    form.addEventListener('submit', handleFormSubmit);
});

// Load students from LocalStorage or API
function loadStudents() {
    const students = JSON.parse(localStorage.getItem('field_students')) || [];
    renderTable(students);
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
        const endDate = new Date(student.endDate).setHours(0, 0, 0, 0);
        
        // Automated System Logic: Determine status based on dates
        const isActive = today <= endDate;
        const statusText = isActive ? 'Active' : 'Completed';
        const badgeClass = isActive ? 'badge-active' : 'badge-completed';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${escapeHtml(student.fullName)}</strong></td>
            <td>${escapeHtml(student.institution)}</td>
            <td>${student.eduLevel} <br><small style="color:#64748b">${student.yearOfStudy}</small></td>
            <td><small>${student.startDate} to ${student.endDate}</small></td>
            <td>${escapeHtml(student.phone)}</td>
            <td><span class="status-badge ${badgeClass}">${statusText}</span></td>
            <td><button class="btn btn-danger" onclick="deleteStudent(${index})">Delete</button></td>
        `;
        tbody.appendChild(row);
    });
}

// Handle Form Submission with Validation
function handleFormSubmit(e) {
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

    const students = JSON.parse(localStorage.getItem('field_students')) || [];
    students.unshift(newStudent); // add newest first
    localStorage.setItem('field_students', JSON.stringify(students));

    renderTable(students);
    document.getElementById('enrollmentForm').reset();
    alert('Student successfully enrolled!');
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
