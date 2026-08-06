# field_student_enrollment_system
System Proposal & SDLC Documentation
Field Student Enrollment System | Kinondoni Municipal Council HQ - IT Department
Project Title: Automated Field Student Enrollment & Tracking System
Target Office: Training Officer & IT Department, Kinondoni Municipal Council HQ
Developer: Practical Training Student (Computer Science)
Architecture: Web-Based (HTML5, CSS3, JavaScript, PHP, MySQL) 
1. Executive Summary & Problem Statement
Kinondoni Municipal Council HQ hosts numerous tertiary field students annually across various departments (Land,
Finance, Health, IT). Previously, student enrollment and tracking were managed via manual paper logbooks or scattered
spreadsheets. This introduced key operational challenges: 
• 
• 
• 
Difficulty in tracking active vs. completed field attachment periods.
Risk of inaccurate contact records and missing institutional affiliation details.
Lack of instant reporting or export capabilities for monthly HR summaries.
The Field Student Enrollment System provides a streamlined web solution with built-in validation, real-time status
calculation, local search filtering, and CSV report export. 
2. Software Development Life Cycle (SDLC) Framework
SDLC Phase
1. Requirement
Analysis
2. System Design
3. Implementation
4. Testing
5. Deployment
Project Execution Activities
Interviews with the Training Officer to establish required
enrollment fields and business validation rules.
Designing responsive UI wireframes, JavaScript logical workflows,
and database schema (`field_students` table).
Writing clean client-side logic (HTML/CSS/JS) with browser
`localStorage` and PHP backend API endpoints.
Executing unit tests on input fields, boundary testing on start/end
dates, and User Acceptance Testing (UAT).
Deploying static web assets locally and configuring XAMPP for
LAN multi-PC access across HQ workstations.
3. System Requirements & Logic Rules
Functional Requirements
• 
• 
• 
• 
Key Artifacts
Software Requirement
Specification (SRS)
Data Dictionary & UI
Layouts
Source Code Package
Test Case Log
User Guide & Handover
Student Registration: Captures Full Name, Institution, Level of Education, Year of Study, Start Date, End Date,
and Contact Phone.
Automated Status Calculation: Computes status dynamically: 
Status = Active if Current Date ≤ End Date, else Completed.
Chronological Date Check: Restricts submission if End Date ≤ Start Date.
Live Search & CSV Export: Instant table filtering by name/university and one-click CSV generation for HR.
4. Database Schema Specification
CREATE TABLE field_students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    edu_level ENUM('Certificate', 'Diploma', 'Degree') NOT NULL,
    year_of_study ENUM('Year 1', 'Year 2', 'Year 3', 'Year 4') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
5. Testing & Validation Matrix
Test
ID Test Scenario Input Data Expected Result Status
TC-01 Valid Enrollment Start: 2026-08-01, End:
2026-10-01 Record added, Status = Active PASS
TC-02 Invalid Date
Sequence
Start: 2026-08-10, End:
2026-08-01
Alert: "Ending day must be after Starting
day" PASS
TC-03 Expired Field Period Start: 2026-05-01, End:
2026-07-01 Record added, Status = Completed PASS
