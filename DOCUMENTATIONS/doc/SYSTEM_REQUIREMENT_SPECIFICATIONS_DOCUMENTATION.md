# IT SERVICE DISPATCH & FAIR ODDS MANAGEMENT SYSTEM
## SYSTEM REQUIREMENTS SPECIFICATION (SRS) DOCUMENTATION
**Standard Compliance:** IEEE 830-1998 / ISO/IEC/IEEE 29148:2018  
**System Version:** 2.0.0 Enterprise Edition  
**Document Identifier:** SRS-ITSD-2026-V2  
**Classification:** Confidential • Technical Architecture & Product Engineering  
**Last Updated:** September 2026  

---

## 1. Introduction

### 1.1 Purpose of the Document
This **System Requirements Specification (SRS)** document establishes the complete, unambiguous functional, non-functional, interface, and behavioral requirements for the **IT Service Dispatch & Fair Odds Management System (v2.0.0)**. It serves as the authoritative engineering contract between software architects, backend/frontend engineers, product owners, QA test teams, and enterprise IT stakeholders.

### 1.2 Document Conventions & Notation
* **RFC 2119 Keywords:** The key words **MUST**, **MUST NOT**, **REQUIRED**, **SHALL**, **SHALL NOT**, **SHOULD**, **RECOMMENDED**, and **MAY** in this document are to be interpreted as described in RFC 2119.
* **Requirement IDs:** Specific functional and non-functional requirements are categorized with standardized identifiers (e.g., `SRS-FR-01.1`, `SRS-NFR-PERF-01`).

### 1.3 Intended Audience & Reading Suggestions
* **System Architects & Lead Developers:** Review Section 2 (Overall Description), Section 3 (System Features), Section 4 (Interfaces), and Section 6 (Data Models).
* **QA & Test Automation Engineers:** Focus on Section 3 (Functional Requirements), Section 5 (Non-Functional Requirements), and Section 6.3 (Requirements Traceability Matrix).
* **IT Operations Supervisors:** Review Section 2.3 (User Classes), Section 3.2 (Odds Engine), Section 3.4 (Attendance), and Section 3.6 (Session Termination).

### 1.4 Project Scope & Core Value Proposition
The system is an enterprise service desk and field dispatch automation platform that solves technician burnout, dispatch favoritism, and unresolved ticket drift. The system provides:
1. **Fair Round-Robin Odds Calculation:** Mathematically allocates dispatch probabilities to unoccupied technicians using idle time and cohort iteration decay.
2. **Tri-Role Modality:** Strict separation across **Service Requester (User)**, **IT Operations Coordinator (Admin)**, and **Field IT Serviceman (Technician)**.
3. **Enforced Session Termination Lifecycle:** Guarantees that a technician remains locked in an occupied state until the end-user inspects the repair, submits a 1-5 star rating, and executes formal session termination.
4. **Admin-Exclusive Attendance Lockout:** Ensures only administrators have the authority to mark technicians absent or reactivate them back into the active dispatch queue.
5. **Real-Time Synchronized Simulation:** Enables multi-actor side-by-side interactive execution via a dedicated Split Simulator.

### 1.5 References & Industry Standards
* **IEEE 830-1998:** Recommended Practice for Software Requirements Specifications.
* **ISO/IEC/IEEE 29148:2018:** Systems and software engineering — Life cycle processes — Requirements engineering.
* **WCAG 2.1 AA:** Web Content Accessibility Guidelines.
* **RFC 7519:** JSON Web Token (JWT) Standard.

---

## 2. Overall Description

### 2.1 Product Perspective & Context
The platform operates as an intranet/extranet cloud-native web application serving enterprise organizations. It replaces legacy ticketing systems that rely on manual cherry-picking, unweighted round-robin, or untracked verbal requests.

```
+----------------------------------------------------------------------------------------------------+
|                                      SYSTEM CONTEXT DIAGRAM                                        |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|   +-----------------------+      +-----------------------+      +-----------------------+          |
|   |   SERVICE REQUESTER   |      |   ADMIN COORDINATOR   |      |     IT SERVICEMAN     |          |
|   |  - Web Portal (SPA)   |      |  - Command Hub (SPA)  |      |  - Field Client (SPA) |          |
|   +-----------+-----------+      +-----------+-----------+      +-----------+-----------+          |
|               |                              |                              |                      |
|               +------------------------------+------------------------------+                      |
|                                              |                                                     |
|                                    HTTPS REST / WSS WebSocket                                      |
|                                              |                                                     |
|                                              v                                                     |
|                     +--------------------------------------------------+                           |
|                     |     IT DISPATCH PLATFORM APPLICATION SERVER      |                           |
|                     |  - State Engine & Event Router                   |                           |
|                     |  - Mathematical Selection Odds Calculator        |                           |
|                     |  - Attendance & Cohort Progression Controller    |                           |
|                     +------------------------+-------------------------+                           |
|                                              |                                                     |
|                                              v                                                     |
|                     +--------------------------------------------------+                           |
|                     |       PERSISTENCE & REAL-TIME CACHE TIER         |                           |
|                     |  - PostgreSQL 16 (ACID Relational Storage)       |                           |
|                     |  - Redis 7 (Pub/Sub & Distributed Locks)         |                           |
|                     +--------------------------------------------------+                           |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

### 2.2 Product Functions Summary
* **Ticket Intake & Presets:** Form validation with category, urgency, pinpoint location, and one-click quick templates.
* **Odds Ranking Engine:** Dynamic generation of descending selection probabilities.
* **Dispatch & Push Alerts:** Instant assignment with Web Audio feedback and in-app notifications.
* **Technician Execution Console:** Acknowledgment, start work, diagnostic repair, and resolution notes entry.
* **User Termination & Rating:** Mandatory end-user verification, 1-5 star review, and technician release.
* **Attendance Governance:** Single-click Admin marking of Absent / Unoccupied duty statuses.
* **Split Simulation:** Side-by-side real-time tripartite view of all roles.

### 2.3 User Classes & Characteristics

| User Class | Technical Sophistication | Role Responsibilities | Access Permissions |
| :--- | :--- | :--- | :--- |
| **Service Requester (`user`)** | General / Non-technical | Reports hardware/software faults, tracks status, verifies fixes, rates technicians. | Create own tickets, view own history, terminate own sessions. |
| **IT Admin (`admin`)** | High / Operational Lead | Oversees queues, evaluates odds, dispatches personnel, manages attendance roster, reviews audit logs. | Organization-wide visibility, dispatch controls, attendance overrides, audit access. |
| **IT Serviceman (`it_guy`)** | High / Field Engineer | Receives dispatches, travels on-site, performs remediation, logs technical repair notes. | View assigned tasks, transition job states (`in_progress`, `completed_by_it`), view personal metrics. |
| **Split Simulator (`split`)** | QA / Evaluator | Observes real-time synchronization between User, Admin, and Technician views. | Full simulation access. |

### 2.4 Operating Environment & Constraints
* **Client Environment:** Modern web browsers (Chrome 110+, Edge 110+, Safari 16+, Firefox 110+) with JavaScript and HTML5 Web Audio API enabled.
* **Server Environment:** Linux (Ubuntu 22.04 LTS / Alpine Linux) or Windows Server containerized via Docker.
* **Runtime Stack:** Node.js v24 LTS, TypeScript 5.8, PostgreSQL 16, Redis 7.

### 2.5 Assumptions & Dependencies
1. **Network Connectivity:** Clients maintain continuous WebSocket / HTTPS connectivity with sub-100ms round-trip latency.
2. **Audio Hardware:** Client devices have standard audio output hardware enabled for audible alerts.
3. **Single Active Task Constraint:** A technician can only be assigned to one active service request at any given time.

---

## 3. System Features & Functional Requirements

### 3.1 Service Request Intake Subsystem (SRS-FEAT-01)
* **SRS-FR-01.1:** The system **SHALL** provide a structured ticket creation interface capturing: `Title`, `Description`, `Category`, `Urgency`, and `Location` (Building, Floor, Room).
* **SRS-FR-01.2:** The system **SHALL** support the following mandatory categories: `Hardware`, `Software`, `Network & WiFi`, `Printer & Peripherals`, `Access & Security`, `Audio/Visual`.
* **SRS-FR-01.3:** The system **SHALL** support four urgency levels: `low`, `medium`, `high`, `critical`.
* **SRS-FR-01.4:** The system **SHALL** provide one-click Quick Issue Templates (Monitor Flickering, WiFi Dropping, Printer Jam, AV Echo) that pre-fill form fields.
* **SRS-FR-01.5:** Upon submission, the system **SHALL** generate a unique, sequential ticket number (e.g., `REQ-1051`), transition the status to `pending_admin`, push an alert notification to Admin, and trigger an audible alert tone.

---

### 3.2 Dynamic Fair Round-Robin Odds Engine (SRS-FEAT-02)
* **SRS-FR-02.1:** The odds engine **SHALL** evaluate **strictly unoccupied technicians** (`status === 'unoccupied'`). Technicians marked `occupied` or `absent` **MUST NOT** be scored or ranked.
* **SRS-FR-02.2:** The engine **SHALL** calculate idle duration $m_i$ in minutes from the technician's `lastAssignedAt` timestamp.
* **SRS-FR-02.3:** For technicians who have not been assigned in the current round $R_n$, the raw score **SHALL** be computed as:
  $$S(T_i) = 1000 + \min(m_i 	imes 2, 500)$$
* **SRS-FR-02.4:** For technicians who have already been assigned in the current round $R_n$, the decayed raw score **SHALL** be computed as:
  $$S(T_i) = 50 + \min(m_i 	imes 0.2, 50)$$
* **SRS-FR-02.5:** The normalized percentage odds **SHALL** be computed as:
  $$P(T_i) = \left(rac{S(T_i)}{\sum S(T_j)}ight) 	imes 100\%$$
* **SRS-FR-02.6:** Candidates **SHALL** be sorted strictly in descending order of selection odds, with Rank 1 highlighted as the top recommendation.
* **SRS-FR-02.7:** When all non-absent eligible technicians have been assigned in round $R_n$, the system **SHALL** automatically increment the round counter to $R_{n+1}$ and reset round assignment counters.

---

### 3.3 Admin Triage & One-Click Dispatch Subsystem (SRS-FEAT-03)
* **SRS-FR-03.1:** The system **SHALL** render a real-time pending queue showing incoming tickets with urgency badges, timestamps, requester identity, and location.
* **SRS-FR-03.2:** Clicking **Evaluate & Dispatch IT Guy** **SHALL** open a modal displaying all unoccupied technicians ranked by odds percentage.
* **SRS-FR-03.3:** Admin confirmation of dispatch **SHALL** atomically:
  * Set ticket status to `assigned`.
  * Set technician status to `occupied` and record `currentRequestId`.
  * Increment technician's `currentRoundAssignments` and `lifetimeAssignments`.
  * Record round assignment in `round_assignments`.
  * Push notifications to technician and requester.
  * Play dispatch chime.

---

### 3.4 Admin Attendance Management Hub (SRS-FEAT-04)
* **SRS-FR-04.1:** The system **SHALL** grant Admin exclusive authority to alter technician duty status between `unoccupied` and `absent`.
* **SRS-FR-04.2:** Admin **SHALL** be permitted to mark an unoccupied technician as `absent` (out of area, sick, on leave).
* **SRS-FR-04.3:** The system **SHALL PREVENT** marking a technician `absent` if their status is `occupied`.
* **SRS-FR-04.4:** Admin **SHALL** be permitted to restore an absent technician back to `unoccupied`, immediately re-entering them into the odds pool.

---

### 3.5 IT Serviceman Task Execution Subsystem (SRS-FEAT-05)
* **SRS-FR-05.1:** Upon dispatch, the assigned technician's workspace **SHALL** render the Active Task Console displaying full problem details and requester location.
* **SRS-FR-05.2:** Technician clicking **Acknowledge & Start Service** **SHALL** transition the ticket status to `in_progress` and notify the requester.
* **SRS-FR-05.3:** Technician clicking **Mark Service as Completed** **SHALL** prompt for mandatory **Resolution Notes** and transition the ticket status to `completed_by_it`.
* **SRS-FR-05.4:** The technician **SHALL REMAIN** in `occupied` holding status until the user terminates the session.

---

### 3.6 Mutual Verification & Enforced Session Termination (SRS-FEAT-06)
* **SRS-FR-06.1:** When a ticket enters `completed_by_it`, the Requester Portal **SHALL** display a prominent golden callout banner stating that action is required.
* **SRS-FR-06.2:** Requester clicking **Terminate Session & Rate** **SHALL** open a modal requiring a 1 to 5 star rating and optional feedback text.
* **SRS-FR-06.3:** Confirming session termination **SHALL**:
  * Set ticket status to `session_terminated` (Closed).
  * Return the assigned technician's status to `unoccupied`.
  * Update technician's cumulative rating and completed jobs count.
  * Trigger confetti particle animation on requester UI.
  * Play success audio chime.

---

### 3.7 In-App Real-Time Notification & Audio Subsystem (SRS-FEAT-07)
* **SRS-FR-07.1:** The system **SHALL** provide an in-app notification bell with an unread badge counter.
* **SRS-FR-07.2:** Notifications **SHALL** be filtered dynamically by active role and user identity.
* **SRS-FR-07.3:** The system **SHALL** synthesize three distinct Web Audio tones without external audio files:
  * *Alert Tone (440Hz -> 880Hz)*
  * *Dispatch Chime (C5-E5-G5 Major Triad)*
  * *Success Chime (G4-C5-E5-G5 Arpeggio)*

---

### 3.8 Multi-Actor Split Simulator (SRS-FEAT-08)
* **SRS-FR-08.1:** The system **SHALL** provide a 3-column responsive view rendering User Portal, Admin Command, and IT Serviceman Workspace side-by-side.
* **SRS-FR-08.2:** Actions executed in one column **SHALL** immediately update state across all three viewports synchronously.

---

### 3.9 System Audit Trail & Compliance Subsystem (SRS-FEAT-09)
* **SRS-FR-09.1:** The system **SHALL** record an immutable audit log for every operational event (`CREATED_REQUEST`, `ASSIGNED_TECHNICIAN`, `STARTED_SERVICE`, `MARKED_COMPLETED`, `TERMINATED_SESSION`, `MARKED_ABSENT`, `MARKED_UNOCCUPIED`).
* **SRS-FR-09.2:** Each audit record **SHALL** capture timestamp, actor name, actor role, action code, and full event details.

---

## 4. External Interface Requirements

### 4.1 User Interfaces
* **Theme:** Deep Slate Dark Theme (`#020617`, `#0f172a`, `#1e293b`).
* **Color Accents:** Indigo (`#6366f1`), Cyan (`#06b6d4`), Emerald (`#10b981`), Amber (`#f59e0b`), Rose (`#f43f5e`).
* **Responsiveness:** Fluid grid scaling across Mobile (`<640px`), Tablet (`640px-1024px`), Desktop (`1024px-1440px`), and Ultra-wide (`>1440px`).

### 4.2 Software Interfaces
* **Database Interface:** PostgreSQL 16 via Prisma ORM / pg connection pool.
* **Pub/Sub Messaging:** Redis 7 streams for real-time WebSocket broadcasting.
* **Browser APIs:** HTML5 Web Audio API, Canvas 2D API (Confetti), LocalStorage API.

### 4.3 Communications Interfaces
* **Protocol:** HTTPS (TLS 1.3) for REST API endpoints.
* **WebSocket:** WSS for bi-directional state synchronization.
* **Data Format:** UTF-8 JSON for all request/response payloads.

---

## 5. Non-Functional System Requirements (NFR)

### 5.1 Performance Requirements
* **SRS-NFR-PERF-01:** Odds calculation execution time **SHALL NOT** exceed 50ms for rosters up to 100 technicians.
* **SRS-NFR-PERF-02:** Client-side initial page render time **SHALL** be under 1.5 seconds on standard broadband.
* **SRS-NFR-PERF-03:** API endpoint response time **SHALL** have a P99 latency of $< 150	ext{ms}$.

### 5.2 Security & Authorization Requirements
* **SRS-NFR-SEC-01:** Role-Based Access Control (RBAC) **MUST** be enforced across all API endpoints and UI action controls.
* **SRS-NFR-SEC-02:** All user inputs **MUST** be sanitized against Cross-Site Scripting (XSS) and SQL Injection vulnerabilities.
* **SRS-NFR-SEC-03:** Session tokens **MUST** utilize cryptographic signing (JWT RS256).

### 5.3 Reliability & Availability Requirements
* **SRS-NFR-REL-01:** The system **SHALL** maintain a minimum operational uptime of 99.95% excluding scheduled maintenance.
* **SRS-NFR-REL-02:** State mutations **MUST** be transactional (ACID) to prevent partial updates or orphaned records.

### 5.4 Usability & Accessibility Requirements
* **SRS-NFR-ACC-01:** All UI text **MUST** meet WCAG 2.1 AA minimum contrast ratio of `4.5:1`.
* **SRS-NFR-ACC-02:** All interactive controls **MUST** be fully navigable via keyboard (`Tab`, `Enter`, `Space`, `Escape`).

---

## 6. Requirements Traceability Matrix (RTM)

| Requirement ID | Feature Description | System Module | Verification Method | Target Release |
| :--- | :--- | :--- | :--- | :---: |
| `SRS-FR-01.1-5` | Request Intake & Presets | `UserPortal.tsx` | UI Test / API Test | v2.0 |
| `SRS-FR-02.1-7` | Fair Odds Engine & Rollover | `oddsCalculator.ts` | Unit / Algorithm Test | v2.0 |
| `SRS-FR-03.1-3` | Admin Dispatch Console | `AdminPortal.tsx` | Integration Test | v2.0 |
| `SRS-FR-04.1-4` | Attendance Management | `AdminPortal.tsx` | Security & RBAC Test | v2.0 |
| `SRS-FR-05.1-4` | Technician Work Execution | `ITGuyPortal.tsx` | Functional Test | v2.0 |
| `SRS-FR-06.1-4` | Enforced Session Termination | `UserPortal.tsx` | E2E Scenario Test | v2.0 |
| `SRS-FR-07.1-3` | Notifications & Audio Engine | `sound.ts` / `Navbar.tsx`| Audio / UI Test | v2.0 |
| `SRS-FR-08.1-2` | Split Simulator | `SplitSimulator.tsx` | Multi-viewport Test | v2.0 |
| `SRS-FR-09.1-2` | Audit Trail Logging | `AppContext.tsx` | Compliance Audit Test | v2.0 |

---
*End of System Requirements Specification (SRS) Documentation.*
