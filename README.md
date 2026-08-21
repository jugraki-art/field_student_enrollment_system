# Kinondoni MC Field Student Enrollment System
### Complete Master Documentation & GitHub Repository Guide

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6%2B-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![CSS3](https://img.shields.io/badge/CSS3-Vanilla-1572B6?style=for-the-badge&logo=css3&logoColor=white)
![License](https://img.shields.io/badge/License-Proprietary-blue?style=for-the-badge)

The **Field Student Enrollment System** is a web-based administrative portal developed for **Kinondoni Municipal Council HQ (IT Department & Training Office)** to streamline practical training applications, enforce data validation rules, display analytical dashboards, manage student records, and generate verified CSV exports for council reporting.

---

## 📖 Master Documentation Table of Contents
1. [SECTION 1: Project Overview & Quick Start Guide](#section-1-project-overview--quick-start-guide)
2. [SECTION 2: Product Requirement Document (PRD)](#section-2-product-requirement-document-prd)
3. [SECTION 3: Technical Requirement Document (TRD)](#section-3-technical-requirement-document-trd)
4. [SECTION 4: App Flow & User Journey Document](#section-4-app-flow--user-journey-document)
5. [SECTION 5: UI/UX Design Brief Document](#section-5-uiux-design-brief-document)
6. [SECTION 6: Backend Schema & Implementation Plan Document](#section-6-backend-schema--implementation-plan-document)

---

# SECTION 1: Project Overview & Quick Start Guide

## 1.1 Key System Features
* 🔐 **Secure Session Management & User Auth:**
  * Client-side `sessionStorage` authentication guard protecting application views.
  * Multi-method login supporting demo credentials (`admin` / `admin123`) and database user accounts.
  * User registration module (`register.php`) with password hashing (`password_hash`).

* 📊 **Interactive Analytical Dashboard (`report.php`):**
  * Welcome banner with quick action links (`+ Enroll New Student`, `📋 View Enrolled Records`).
  * Real-time counter cards tracking Total Enrollments, Active Placements, Completed Placements, and Participating Institutions.
  * 4 Chart.js visual charts: Monthly Enrollment Trend, Education Level Distribution, Year of Study Breakdown, and Top Institutions.

* 📝 **Validated Student Enrollment Form (`add_enrollment.php`):**
  * Required fields: Full Name, Institution, Level of Education, Year of Study, Start Date, End Date, Contact Phone.
  * **Chronological Date Rule:** Enforces `endDate > startDate`. Submissions where `endDate <= startDate` are blocked.
  * **Phone Number Regex Rule:** Enforces strict 10-digit format matching `/^[0-9]{10}$/`.

* 📋 **Dynamic Records List & Management (`enrolled_list.php`):**
  * Real-time search filter (`#searchInput`) filtering across student names, institutions, levels, and phone numbers.
  * Dynamic active status calculation (`today <= endDate`) rendering color-coded badges (`Active` green vs `Completed` slate).
  * HTTP `DELETE` operations using SQL prepared statements (`DELETE FROM field_students WHERE student_id = ?`).
  * 1-click CSV data export producing downloadable `kinondoni_field_students.csv`.

* 🎨 **Modern Responsive UI System:**
  * Custom corporate navy & primary blue CSS design system.
  * Fixed sidebar layout with smooth collapse toggling and state persistence in `localStorage.getItem('sidebarClosed')`.

---

## 1.2 Installation & Setup Guide

### System Requirements
* **XAMPP / WAMP / LAMP** or standalone Apache Server with PHP 8.0+
* **MySQL 5.7+** or **MariaDB 10.4+**
* Modern web browser (Chrome, Edge, Firefox, Safari)

### Installation Steps

1. **Clone or Copy Repository:**
   Place the project directory into your web server root folder:
   ```bash
   # For XAMPP Windows
   C:\xampp\htdocs\field_student_enrollment_system3
   ```

2. **Start Web Server & Database:**
   Open the XAMPP Control Panel and start **Apache** and **MySQL**.

3. **Database Provisioning (Automatic or Manual):**
   * **Automatic:** Opening the application automatically initializes database `kinondoni_pt_db` and required tables on first API access.
   * **Manual Import (Optional):** Open `phpMyAdmin` (`http://localhost/phpmyadmin/`), create database `kinondoni_pt_db`, and import `schema.sql`.

4. **Access Portal in Browser:**
   Open your browser and navigate to:
   ```
   http://localhost/field_student_enrollment_system3/
   ```

---

## 1.3 Default Authentication Credentials

| User Type | Username | Password | Role / Access Level |
| :--- | :--- | :--- | :--- |
| **Demo Admin** | `admin` | `admin123` | Full Access (Training Officer) |
| **Registered User**| Registered Username | User Password | Full Access (Training Officer) |

---

## 1.4 REST API Quick Reference

| Method | Endpoint | Description | Sample Body / Parameters |
| :--- | :--- | :--- | :--- |
| `GET` | `/api.php` | Fetch all student records | `?action=students` |
| `POST` | `/api.php` | Enroll new field student | `{ "fullName": "...", "institution": "...", ... }` |
| `DELETE`| `/api.php?id=1` | Delete student record by ID | `?id=1` or `{ "id": 1 }` |
| `POST` | `/api.php?action=login` | Authenticate user credentials | `{ "username": "admin", "password": "admin123" }` |
| `POST` | `/api.php?action=register`| Register new user account | `{ "username": "...", "password": "...", ... }` |

---

# SECTION 2: Product Requirement Document (PRD)

## 2.1 Executive Summary
The **Field Student Enrollment System** is a dedicated web application built for **Kinondoni Municipal Council HQ** to streamline, automate, and centralize the record-keeping and placement management of field students undertaking practical training across municipal departments.

Historically, tracking practical training placements across various higher learning institutions (such as UDSM, DIT, IFM, CBE, MUST) was handled through fragmented manual ledgers or standalone spreadsheets. This system provides a unified digital portal enabling Training Officers to enroll students, validate placement timelines, monitor active versus completed field terms, generate statistical analytical reports, search/filter student records in real time, and export verified datasets into CSV formats for institutional reporting.

---

## 2.2 Problem Statement & Business Goals

### Problem Statement
1. **Manual Data Fragmentation:** Lack of a centralized database led to duplicate enrollment records and misplaced contact details.
2. **Inconsistent Validation:** Incorrect training dates (e.g., end date prior to start date) and malformed phone numbers caused record degradation.
3. **Lack of Visibility:** Officers lacked real-time dashboard analytics regarding total active students, institutional breakdowns, and study year distributions.
4. **Export Complexity:** Extracting verified student lists for council reporting required manual compilation.

### Business Objectives & Key Results (OKRs)
* **OKR 1:** Reduce student enrollment data entry time by 60% through structured client-side and server-side form validation.
* **OKR 2:** Provide 100% real-time visibility into active practical training placements across Kinondoni Municipal Council departments.
* **OKR 3:** Eliminate data entry errors (0% invalid dates, 0% malformed phone numbers).
* **OKR 4:** Enable instant 1-click CSV data export for official council audits.

---

## 2.3 User Personas

### Persona 1: Training Officer (Primary User)
* **Role:** Manages day-to-day student applications, enrollment records, and completion certificates.
* **Goals:** Quick student registration, real-time list filtering, instant CSV exports, monitoring student active/completed status.
* **Pain Points:** Time spent manually validating training dates and cross-referencing contact info.

### Persona 2: IT Department Administrator
* **Role:** Oversees system maintenance, user access creation, and data integrity.
* **Goals:** Secure user authentication, reliable MySQL storage, REST API standards compliance.
* **Pain Points:** System downtime or non-standard code structures.

---

## 2.4 Scope & Feature Requirements

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

## 2.5 System Business Rules

1. **Date Validation Rule:** A student's practical training `endDate` must strictly occur after `startDate`. Submissions where `endDate <= startDate` are blocked immediately.
2. **Phone Number Format:** Phone numbers must contain exactly 10 numerical digits (e.g., `0712345678`). Spaces are stripped prior to validation.
3. **Active Status Rule:** A student record is classified as **Active** if `current_date <= end_date`. Once `current_date > end_date`, status automatically updates to **Completed**.
4. **Session Security:** Unauthenticated users attempting to access protected application views are automatically redirected to `login.html`.

---

# SECTION 3: Technical Requirement Document (TRD)

## 3.1 System Architecture Overview

The system follows a modular **Client-Server Single-Page/Multi-Page Application (SPA/MPA Hybrid)** architecture:
* **Presentation Layer:** Vanilla HTML5, Vanilla CSS3 (Custom CSS Variables Design System), Vanilla ES6+ JavaScript, and Chart.js.
* **Service / API Layer:** PHP RESTful Endpoint (`api.php`) serving `application/json` responses, handling CORS, inputs escaping, and prepared statements.
* **Data Layer:** MySQL Relational Database (`kinondoni_pt_db`) storing student records and user credentials.
* **Storage & Fallback Layer:** Client-side `sessionStorage` for session guard flags and `localStorage` for offline caching and UI layout persistence (`sidebarClosed`).

```
+-----------------------------------------------------------------------+
|                            BROWSER (CLIENT)                           |
|  +-------------------+  +-------------------+  +-------------------+  |
|  |  report.php / JS  |  | add_enrollment.php|  | enrolled_list.php |  |
|  +---------+---------+  +---------+---------+  +---------+---------+  |
|            |                      |                      |            |
|            +----------------------+----------------------+            |
|                                   | Async Fetch (JSON)                |
+-----------------------------------|-----------------------------------+
                                    v
+-----------------------------------------------------------------------+
|                            SERVER (BACKEND)                           |
|  +-----------------------------------------------------------------+  |
|  |                            api.php                              |  |
|  |   - JSON Request Decoder & Response Formatter (CORS)            |  |
|  |   - Validation Engine (endDate > startDate & Phone Regex)        |  |
|  |   - MySQLi Prepared Statements Handler                          |  |
|  +--------------------------------+--------------------------------+  |
|                                   |                                   |
+-----------------------------------|-----------------------------------+
                                    v
+-----------------------------------------------------------------------+
|                         DATABASE (MYSQL / MARIA)                      |
|  +-----------------------------------------------------------------+  |
|  | kinondoni_pt_db                                                 |  |
|  |   - field_students (student_id, full_name, institution, ...)    |  |
|  |   - users (user_id, username, password_hash, position, ...)     |  |
|  +-----------------------------------------------------------------+  |
+-----------------------------------------------------------------------+
```

---

## 3.2 Technology Stack Specifications

* **Frontend Logic:** Vanilla JavaScript (ES6 Modules/Functions, Promises, Async/Await Fetch API).
* **Styling & CSS:** Vanilla CSS3 (CSS Custom Properties, Flexbox, CSS Grid, Responsive Media Queries).
* **Charts Engine:** Chart.js v4.x loaded via CDN.
* **Backend Runtime:** PHP 8.0+ (specifically tested on PHP 8.5.9 CLI/Apache).
* **Database Management System:** MySQL 5.7+ / MariaDB 10.4+ using `mysqli` extension.

---

## 3.3 Server-Side Specifications (`api.php`)

### HTTP Headers & CORS
```php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
```

### Endpoints Payload Details

#### 1. Retrieve Student Records (`GET /api.php`)
```json
[
  {
    "student_id": 1,
    "id": 1,
    "fullName": "Juma Rashidi",
    "institution": "University of Dar es Salaam (UDSM)",
    "eduLevel": "Degree",
    "yearOfStudy": "Year 3",
    "startDate": "2026-06-01",
    "endDate": "2026-09-30",
    "phone": "0712345678",
    "createdAt": "2026-08-21 17:48:40"
  }
]
```

#### 2. Create Field Student Enrollment (`POST /api.php`)
```json
{
  "fullName": "Amina Said",
  "institution": "DIT",
  "eduLevel": "Diploma",
  "yearOfStudy": "Year 2",
  "startDate": "2026-07-15",
  "endDate": "2026-10-15",
  "phone": "0754987654"
}
```

#### 3. Delete Student Record (`DELETE /api.php?id=1`)
Prepared Statement: `DELETE FROM field_students WHERE student_id = ?`

---

# SECTION 4: App Flow & User Journey Document

## 4.1 High-Level Workflow Diagram

```
[User Visits Portal] ---> (Session Active?)
                              |
              +---------------+---------------+
              | No                            | Yes
              v                               v
       [login.html]                   [report.php / Dashboard]
              |                               |
       (Submit Login)               +---------+---------+
       - Demo admin                 |         |         |
       - DB user auth               v         v         v
              |               [add_enrollment] [enrolled_list] [profile]
              v                     |         |         |
       [report.php]                 v         v         v
                              (Form POST) (Search/Export/Delete) (Edit Profile)
```

---

## 4.2 Detailed Screen-by-Screen Journeys

### Screen 1: Login Interface (`login.html`)
1. **User Landing:** If `sessionStorage.getItem('isLoggedIn') === 'true'`, system automatically redirects to `report.php`.
2. **Demo Credential Match:** Entering `admin` / `admin123` logs user in as `Training Officer (Admin)` instantly.
3. **Database Credential Match:** Sends HTTP POST request to `api.php?action=login`.
4. **Registration Link:** Navigates user to `register.php`.

### Screen 2: Dashboard Overview (`report.php`)
1. **Welcome Banner:** Quick action buttons (`+ Enroll New Student`, `📋 View Enrolled Records`).
2. **Metrics Grid:** Computes Total Enrollments, Active Placements (`end_date >= current_date`), Completed Placements (`end_date < current_date`), and Institutions Count.
3. **Visual Charts:** Monthly Trend Line Chart, Education Level Bar Chart, Year of Study Bar Chart, Top Institutions Doughnut Chart.

### Screen 3: Enroll New Student (`add_enrollment.php`)
1. **Client Validation:** Enforces `endDate > startDate` and 10-digit phone regex.
2. **Post-Submission:** Displays success alert and redirects to `enrolled_list.php`.

### Screen 4: Enrolled Records List (`enrolled_list.php`)
1. **Status Badge Calculation:** Evaluates `today <= endDate` for Active (green) vs Completed (slate).
2. **Live Search Filtering:** Real-time search across student fields via `#searchInput`.
3. **CSV Export:** Downloads `kinondoni_field_students.csv`.
4. **Record Deletion:** Prompts user confirmation, executes HTTP `DELETE` to `api.php`, and updates table.

---

# SECTION 5: UI/UX Design Brief Document

## 5.1 Visual Identity & Design Tokens

### Color Tokens
* `--dark-navy`: `#0f172a` (Sidebar background, Login gradient)
* `--navbar-bg`: `#1e293b` (Header navbar)
* `--primary-blue`: `#2563eb` (Primary CTA buttons, Header accent border)
* `--secondary-teal`: `#0f766e` (Secondary action buttons)
* `--success-bg` / `--success-text`: `#dcfce7` / `#15803d` (Active status badge)
* `--danger-bg` / `--danger-text`: `#fef2f2` / `#dc2626` (Delete buttons, Error alerts)

### Typography Hierarchy (Inter)
* **Display Heading:** `1.4rem` (22px) | Font-Weight: 700
* **Card Headings:** `1.15rem` (18px) | Font-Weight: 700
* **Metric Numbers:** `1.6rem` (26px) | Font-Weight: 800
* **Body Text:** `0.92rem` (15px) | Font-Weight: 400
* **Form Labels / Buttons:** `0.88rem` (14px) | Font-Weight: 600
* **Status Badges / Headers:** `0.78rem` (12px) | Font-Weight: 700

---

## 5.2 Component Specifications

### Status Badges (`.status-badge`)
```css
.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
    display: inline-block;
}

.badge-active {
    background-color: #dcfce7;
    color: #15803d;
    border: 1px solid #bbf7d0;
}

.badge-completed {
    background-color: #f1f5f9;
    color: #64748b;
    border: 1px solid #cbd5e1;
}
```

---

# SECTION 6: Backend Schema & Implementation Plan Document

## 6.1 Database Schema Specification

```sql
CREATE DATABASE IF NOT EXISTS kinondoni_pt_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kinondoni_pt_db;

-- Table: field_students
CREATE TABLE IF NOT EXISTS field_students (
    student_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    institution VARCHAR(150) NOT NULL,
    edu_level ENUM('Certificate', 'Diploma', 'Degree') NOT NULL,
    year_of_study ENUM('Year 1', 'Year 2', 'Year 3', 'Year 4') NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    position VARCHAR(50) DEFAULT 'Training Officer',
    phone_number VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 6.2 Data Mapping (Database to JSON API)

| Database Column (`snake_case`) | API JSON Key (`camelCase`) | Data Type | Sample Value |
| :--- | :--- | :--- | :--- |
| `student_id` | `student_id` / `id` | Integer | `1` |
| `full_name` | `fullName` | String | `"Juma Rashidi"` |
| `institution` | `institution` | String | `"University of Dar es Salaam (UDSM)"` |
| `edu_level` | `eduLevel` | String | `"Degree"` |
| `year_of_study` | `yearOfStudy` | String | `"Year 3"` |
| `start_date` | `startDate` | String (YYYY-MM-DD) | `"2026-06-01"` |
| `end_date` | `endDate` | String (YYYY-MM-DD) | `"2026-09-30"` |
| `phone_number` | `phone` | String | `"0712345678"` |
| `created_at` | `createdAt` | String (Timestamp)| `"2026-08-21 17:48:40"` |

---

## 6.3 Test Verification Strategy

| Test Case | Procedure | Expected Result | Pass Criteria |
| :--- | :--- | :--- | :--- |
| **TC-01: API GET** | Request `GET /api.php` | Returns 200 OK JSON array of students sorted by `student_id DESC`. | **PASSED** |
| **TC-02: Date Validation** | Submit form where `endDate <= startDate` | Alert triggered: `"SDLC Validation Error: Ending day must be chronological after the Starting day."` Request blocked. | **PASSED** |
| **TC-03: Phone Validation** | Submit phone number not matching 10 digits | Alert triggered: `"Validation Error: Please enter a valid 10-digit phone number."` Request blocked. | **PASSED** |
| **TC-04: API DELETE** | Trigger delete on student record | Prepared statement executes `DELETE FROM field_students WHERE student_id = ?`. Table updates dynamically. | **PASSED** |
| **TC-05: Search Filter** | Type university name in `#searchInput` | Table filters rows in real time without page reload. | **PASSED** |
| **TC-06: CSV Export** | Click "Export CSV" button | File `kinondoni_field_students.csv` downloads with proper headers. | **PASSED** |
| **TC-07: Auth Guard** | Open `report.php` without active session | Page automatically redirects visitor to `login.html`. | **PASSED** |
| **TC-08: Sidebar Toggle** | Click `#sidebarToggle` | Layout toggles `.sidebar-closed` class; state saved to `localStorage.getItem('sidebarClosed')`. | **PASSED** |

---

© 2026 **Kinondoni Municipal Council HQ - IT Department & Training Office**. All rights reserved. Proprietary software for municipal administrative operations.
