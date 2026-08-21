# Product Requirement Document (PRD)

## Project Title: Field Student Enrollment System
**Organization:** Kinondoni Municipal Council HQ (IT Department & Training Office)  
**Version:** 1.0.0  
**Status:** Approved & Implemented  
**Author:** Lead AI Software Architect & Development Team  
**Date:** August 2026  

---

## 1. Executive Summary
The **Field Student Enrollment System** is a dedicated web application built for **Kinondoni Municipal Council HQ** to streamline, automate, and centralize the record-keeping and placement management of field students undertaking practical training across municipal departments. 

Historically, tracking practical training placements across various higher learning institutions (such as UDSM, DIT, IFM, CBE, MUST) was handled through fragmented manual ledgers or standalone spreadsheets. This system provides a unified digital portal enabling Training Officers to enroll students, validate placement timelines, monitor active versus completed field terms, generate statistical analytical reports, search/filter student records in real time, and export verified datasets into CSV formats for institutional reporting.

---

## 2. Problem Statement & Business Goals

### 2.1 Problem Statement
1. **Manual Data Fragmentation:** Lack of a centralized database led to duplicate enrollment records and misplaced contact details.
2. **Inconsistent Validation:** Incorrect training dates (e.g., end date prior to start date) and malformed phone numbers caused record degradation.
3. **Lack of Visibility:** Officers lacked real-time dashboard analytics regarding total active students, institutional breakdowns, and study year distributions.
4. **Export Complexity:** Extracting verified student lists for council reporting required manual compilation.

### 2.2 Business Objectives & Key Results (OKRs)
* **OKR 1:** Reduce student enrollment data entry time by 60% through structured client-side and server-side form validation.
* **OKR 2:** Provide 100% real-time visibility into active practical training placements across Kinondoni Municipal Council departments.
* **OKR 3:** Eliminate data entry errors (0% invalid dates, 0% malformed phone numbers).
* **OKR 4:** Enable instant 1-click CSV data export for official council audits.

---

## 3. User Personas

### Persona 1: Training Officer (Primary User)
* **Role:** Manages day-to-day student applications, enrollment records, and completion certificates.
* **Goals:** Quick student registration, real-time list filtering, instant CSV exports, monitoring student active/completed status.
* **Pain Points:** Time spent manually validating training dates and cross-referencing contact info.

### Persona 2: IT Department Administrator
* **Role:** Oversees system maintenance, user access creation, and data integrity.
* **Goals:** Secure user authentication, reliable MySQL storage, REST API standards compliance.
* **Pain Points:** System downtime or non-standard code structures.

---

## 4. Scope & Feature Requirements

| Requirement ID | Feature Name | Description | Priority |
| :--- | :--- | :--- | :--- |
| **FR-01** | User Sign-In & Auth | Secure authentication with demo (`admin`/`admin123`) and database user credentials via `sessionStorage` guard. | **Must Have** |
| **FR-02** | User Registration | Portal for registering new Training Officers into the `users` table with password hashing. | **Must Have** |
| **FR-03** | Dashboard & Analytics | Overview welcome screen with metric counter cards and 4 Chart.js visual charts (Trend, Level, Year, Top Institutions). | **Must Have** |
| **FR-04** | Quick Action Shortcuts | One-click navigation buttons from dashboard to `add_enrollment.php` and `enrolled_list.php`. | **Must Have** |
| **FR-05** | Enrollment Form | Structured form capturing `fullName`, `institution`, `eduLevel`, `yearOfStudy`, `startDate`, `endDate`, and `phone`. | **Must Have** |
| **FR-06** | Chronological Date Rule | Validation enforcing `endDate > startDate`. Submissions failing this rule are rejected with clear error feedback. | **Must Have** |
| **FR-07** | Phone Number Regex | Enforces strict 10-digit phone number matching regex `/^[0-9]{10}$/`. | **Must Have** |
| **FR-08** | Dynamic Records List | Displays `#recordsTable` with dynamic active badge calculation (`today <= endDate` -> Active; else Completed). | **Must Have** |
| **FR-09** | Real-Time Search Filter | Client-side filter (`#searchInput`) filtering across student name, institution, education level, and contact. | **Must Have** |
| **FR-10** | Record Deletion | Deletes field student records via REST API prepared statement `DELETE FROM field_students WHERE student_id = ?`. | **Must Have** |
| **FR-11** | CSV Export | Generates downloadable `kinondoni_field_students.csv` file from active dataset. | **Must Have** |
| **FR-12** | Responsive Sidebar | Collapsible sidebar navigation with persistent state via `localStorage.getItem('sidebarClosed')`. | **Must Have** |
| **FR-13** | Profile Management | Interface for updating officer profile details saved locally. | **Should Have** |

---

## 5. System Business Rules

1. **Date Validation Rule:** A student's practical training `endDate` must strictly occur after `startDate`. Submissions where `endDate <= startDate` are blocked immediately.
2. **Phone Number Format:** Phone numbers must contain exactly 10 numerical digits (e.g., `0712345678`). Spaces are stripped prior to validation.
3. **Active Status Rule:** A student record is classified as **Active** if `current_date <= end_date`. Once `current_date > end_date`, status automatically updates to **Completed**.
4. **Session Security:** Unauthenticated users attempting to access protected application views are automatically redirected to `login.html`.

---

## 6. Key Performance Indicators (KPIs)
* **Page Load Time:** < 1.2 seconds for full dashboard & record table render.
* **API Response Time:** < 200ms for GET, POST, and DELETE operations.
* **System Uptime:** 99.9% local server availability on Apache/XAMPP.
* **Data Accuracy:** 100% compliance with date chronology and phone format standards.

---

## 7. Future Roadmap (Post v1.0)
* **Phase 2:** PDF Certificate Generation upon student training completion.
* **Phase 3:** Automated SMS/Email notifications sent to students prior to training completion.
* **Phase 4:** Departmental assignment module linking students to specific municipal officers.
