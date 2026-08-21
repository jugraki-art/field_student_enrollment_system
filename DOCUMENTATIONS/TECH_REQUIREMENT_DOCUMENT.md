# Technical Requirement Document (TRD)

## Project Title: Field Student Enrollment System
**Organization:** Kinondoni Municipal Council HQ  
**System Version:** 1.0.0  
**Target Environment:** Cross-platform Web Browsers (Chrome, Edge, Firefox, Safari) / Apache / PHP 8.x / MySQL 8.x  

---

## 1. System Architecture Overview

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

## 2. Technology Stack Specifications

### 2.1 Core Technologies
* **Frontend Logic:** Vanilla JavaScript (ES6 Modules/Functions, Promises, Async/Await Fetch API).
* **Styling & CSS:** Vanilla CSS3 (CSS Custom Properties, Flexbox, CSS Grid, Responsive Media Queries). No external frameworks required.
* **Charts Engine:** Chart.js v4.x loaded via CDN for responsive data visualization.
* **Backend Runtime:** PHP 8.0+ (specifically tested on PHP 8.5.9 CLI/Apache).
* **Database Management System:** MySQL 5.7+ / MariaDB 10.4+ using `mysqli` extension.

---

## 3. Server-Side Specifications (`api.php`)

### 3.1 HTTP Headers & Cross-Origin Resource Sharing (CORS)
```php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
```
* **Preflight Requests:** Handled via `REQUEST_METHOD === 'OPTIONS'`, returning `HTTP 200 OK` instantly.

### 3.2 Database Connection & Auto-Provisioning
* Connects using `new mysqli("localhost", "root", "")`.
* Executes `CREATE DATABASE IF NOT EXISTS kinondoni_pt_db`.
* Automatically creates required tables (`field_students`, `users`) if missing, ensuring zero-configuration deployment.

### 3.3 REST API Endpoints Specification

#### 1. Retrieve Student Records
* **HTTP Method:** `GET`
* **URL:** `/api.php` or `/api.php?action=students`
* **Response Status:** `200 OK`
* **Output Payload (JSON Array):**
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

#### 2. Create Field Student Enrollment
* **HTTP Method:** `POST`
* **URL:** `/api.php`
* **Input Payload (JSON Object):**
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
* **Validation Logic:** Enforces `strtotime($endDate) > strtotime($startDate)`.
* **Success Response (`200 OK`):**
```json
{
  "status": "success",
  "message": "Student enrolled successfully.",
  "id": 2
}
```
* **Validation Failure Response (`400 Bad Request`):**
```json
{
  "status": "error",
  "message": "End date must be after start date."
}
```

#### 3. Delete Field Student Record
* **HTTP Method:** `DELETE`
* **URL:** `/api.php?id=1` or JSON Body `{ "id": 1 }`
* **Database Prepared Statement:** `DELETE FROM field_students WHERE student_id = ?`
* **Success Response (`200 OK`):**
```json
{
  "status": "success",
  "message": "Record deleted."
}
```

#### 4. User Authentication (Login)
* **HTTP Method:** `POST`
* **URL:** `/api.php?action=login`
* **Input Payload:** `{ "username": "admin", "password": "admin123" }`
* **Success Response (`200 OK`):**
```json
{
  "status": "success",
  "message": "Login successful",
  "user": {
    "username": "admin",
    "position": "Training Officer"
  }
}
```

#### 5. User Registration (Sign Up)
* **HTTP Method:** `POST`
* **URL:** `/api.php?action=register`
* **Input Payload:** `{ "username": "officer1", "role": "Training Officer", "phone": "0712345678", "password": "securepassword" }`
* **Success Response (`200 OK`):**
```json
{
  "status": "success",
  "message": "User registered successfully."
}
```

---

## 4. Client-Side Technical Architecture (`app.js` & `login.js`)

### 4.1 Data Flow & State Management
* **`currentStudentsList`:** In-memory global JavaScript array containing fetched student records.
* **LocalStorage Fallback:** When network calls fail, data reads and writes fall back to `localStorage.getItem('field_students')`.
* **Sidebar State Persistence:** Nav state persisted via `localStorage.setItem('sidebarClosed', 'true'|'false')`.

### 4.2 Form Validation Specifications
1. **Chronological Date Check:**
   ```javascript
   if (new Date(endDate) <= new Date(startDate)) {
       alert('SDLC Validation Error: Ending day must be chronological after the Starting day.');
       return;
   }
   ```
2. **Phone Number Regex Check:**
   ```javascript
   const sanitizedPhone = phone.replace(/\s+/g, '');
   if (!/^[0-9]{10}$/.test(sanitizedPhone)) {
       alert('Validation Error: Please enter a valid 10-digit phone number.');
       return;
   }
   ```

### 4.3 Active Status Calculation Logic
```javascript
const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()).getTime();
const endTimestamp = new Date(endDate).getTime();
const isActive = today <= endTimestamp;
// Assigns badge-active ("Active", green) or badge-completed ("Completed", gray)
```

---

## 5. Security & Data Integrity

1. **SQL Injection Prevention:** All SQL insert, delete, and lookup operations use `mysqli::prepare()` parameter binding (`bind_param`).
2. **Password Security:** User passwords stored in database using PHP's native `password_hash($password, PASSWORD_DEFAULT)` and verified using `password_verify()`.
3. **Cross-Site Scripting (XSS) Prevention:** HTML rendering sanitized via `escapeHtml()` helper function.
4. **Session Guarding:** Client-side authentication status checked via `sessionStorage.getItem('isLoggedIn')`. Protected views redirect unauthenticated visitors to `login.html`.
