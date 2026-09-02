# IT SERVICE DISPATCH & FAIR ODDS MANAGEMENT SYSTEM
## PRODUCT REQUIREMENTS DOCUMENT (PRD)
**Product Version:** 2.0.0 Enterprise Edition  
**Document Code:** PRD-2026-V2  
**Classification:** Product Management & Functional Specifications  
**Target Stakeholders:** Product Managers, Engineering Leads, UI/UX Designers, Executive Leadership  
**Last Updated:** September 2026  

---

## 1. Document Control & Vision Statement

### 1.1 Executive Vision
The **IT Service Dispatch & Fair Odds Management System** is an organizational IT operations platform engineered to deliver equitable task distribution, prevent technician fatigue, minimize service response latency, and enforce mutual verification for ticket resolution.

### 1.2 Core Business Objectives
1. **Eliminate Dispatch Favoritism & Bottlenecks:** Replace arbitrary manual dispatching with a mathematically verified, transparent Fair Round-Robin Odds Engine.
2. **Enforce Mutual Resolution Accountability:** Prevent technicians from closing tickets prematurely by making user session termination the sole trigger for technician release.
3. **Streamline Supervisor Roster Oversight:** Give IT leadership single-click control over technician attendance with automated exclusion of absent personnel from dispatch calculations.
4. **Accelerate Mean Time to Resolution (MTTR):** Provide field technicians with pinpoint on-site location data, clear symptom descriptions, and real-time push alerts.

---

## 2. User Personas & Problem Scenarios

```
+----------------------------------------------------------------------------------------------------+
|                                    TARGET USER PERSONA PROFILES                                    |
+-----------------------------------+--------------------------------+-------------------------------+
| 1. SARAH JENKINS (Requester)      | 2. ALEX MERCER (IT Admin Lead) | 3. MARCUS VANCE (Field Tech)  |
+-----------------------------------+--------------------------------+-------------------------------+
| * Dept: Financial Operations      | * Dept: IT Operations Lead     | * Dept: IT Infrastructure     |
| * Goal: Fast resolution of dual-  | * Goal: Fair workload balancing| * Goal: Clear task details,   |
|   monitor crash during reporting. |   without manual favoritism.   |   fair breaks between calls.  |
| * Pain: Tickets get lost; no clue | * Pain: Technicians complain   | * Pain: Gets 5 tickets in a   |
|   when technician will arrive.    |   of uneven assignments.       |   row while peers sit idle.   |
+-----------------------------------+--------------------------------+-------------------------------+
```

---

## 3. Detailed Functional Requirements (FRD)

### FR-1: Requester Intake & Ticket Creation Subsystem
* **FR-1.1:** System shall provide a structured submission form capturing:
  * `Title` (Required, string max 255 chars)
  * `Description` (Required, text detailing error symptoms)
  * `Category` (Single select: `Hardware`, `Software`, `Network & WiFi`, `Printer & Peripherals`, `Access & Security`, `Audio/Visual`)
  * `Urgency` (Single select: `low`, `medium`, `high`, `critical`)
  * `Location` (Nested fields: `Building`, `Floor`, `Room/Cubicle`)
* **FR-1.2:** System shall provide one-click **Quick Issue Presets** (e.g., *Monitor Flickering*, *WiFi Dropping*, *Printer Jam*, *AV Echo*) to auto-populate form fields for common workplace emergencies.
* **FR-1.3:** System shall auto-generate human-readable ticket numbers (e.g., `REQ-1051`) upon submission.
* **FR-1.4:** System shall immediately notify Admin and transition the ticket to `pending_admin`.

---

### FR-2: Fair Round-Robin Odds Engine
* **FR-2.1:** System shall evaluate **strictly unoccupied technicians** (`status === 'unoccupied'`). Technicians marked `occupied` or `absent` must be completely excluded from odds calculations.
* **FR-2.2:** System shall compute idle duration in minutes based on `lastAssignedAt`.
* **FR-2.3:** System shall score unassigned technicians in active round with a base score of 1000 plus an idle bonus of up to +500 points ($2 	imes 	ext{minutes idle}$).
* **FR-2.4:** System shall apply an iteration decay penalty to technicians already assigned in the current round (Base 50 + minor idle bonus up to +50 points).
* **FR-2.5:** System shall normalize scores into percentage odds ($\sum = 100\%$) and rank candidates strictly in descending order (Highest Odds first).
* **FR-2.6:** System shall auto-detect cohort exhaustion when all non-absent technicians have received assignments in the round, auto-incrementing the round counter to $N+1$ and resetting round assignment flags.

---

### FR-3: Admin Dispatch Command Center
* **FR-3.1:** System shall render real-time summary metric cards:
  * *Unoccupied Technicians Count* (with pulsing green indicator)
  * *Occupied Technicians Count* (amber indicator)
  * *Absent Technicians Count* (rose indicator)
  * *Pending Requests Count* (blue indicator)
* **FR-3.2:** System shall provide a dedicated **Pending Requests Queue** displaying ticket details, requester metadata, and location.
* **FR-3.3:** System shall render a **Dispatch Evaluation Modal** displaying ranked unoccupied technicians with visual percentage bars, rank badges, idle duration, and top recommendation callouts.
* **FR-3.4:** Admin shall have the authority to confirm dispatch, triggering atomic state updates and push notifications.

---

### FR-4: Admin Attendance Management & Duty Override
* **FR-4.1:** System shall grant **exclusive authority to Admin** to modify technician attendance status.
* **FR-4.2:** Admin shall be able to mark any unoccupied technician as `absent` (e.g., out of working area, sick, off-duty).
* **FR-4.3:** System shall prevent marking a technician `absent` if they are currently `occupied` with an active ticket.
* **FR-4.4:** Admin shall be able to restore an absent technician back to `unoccupied`, immediately re-entering them into the odds evaluation pool.

---

### FR-5: IT Serviceman Workspace & Task Execution
* **FR-5.1:** System shall present an **Active Dispatch Console** when a technician has an assigned task.
* **FR-5.2:** Technician shall be able to click **Acknowledge & Start Service** to transition the ticket from `assigned` to `in_progress`.
* **FR-5.3:** Technician shall be able to log **Resolution Notes / Action Taken** and click **Mark Service as Completed**, transitioning the ticket to `completed_by_it`.
* **FR-5.4:** Technician shall remain in `occupied` status in a holding state until the requester terminates the session.

---

### FR-6: Enforced Session Termination & Mutual Sign-Off
* **FR-6.1:** When a ticket enters `completed_by_it`, the Requester Portal shall display a prominent, non-dismissible golden callout banner: *Action Required: Work Finished by IT Serviceman*.
* **FR-6.2:** Requester shall be able to review the technician's logged resolution notes.
* **FR-6.3:** Requester shall click **Terminate Session & Rate**, providing a mandatory 1 to 5 star rating and optional feedback.
* **FR-6.4:** Confirming termination shall:
  * Transition ticket to `session_terminated` (Closed).
  * Return the assigned technician to `unoccupied` status.
  * Recompute technician's cumulative lifetime rating and completed jobs count.
  * Trigger celebratory visual feedback (confetti particles) and success audio chime.

---

### FR-7: Real-Time Notifications & Web Audio Feedback
* **FR-7.1:** System shall maintain an in-app notification drawer with unread badge counter.
* **FR-7.2:** Notifications shall be filtered dynamically based on the active role and actor identity.
* **FR-7.3:** System shall generate distinct synthesized audio cues via HTML5 Web Audio API:
  * *Alert Tone:* For new requests and completion prompts.
  * *Dispatch Chime:* For assignment confirmations.
  * *Success Chime:* For session terminations and rating completions.

---

### FR-8: Tri-Role Modality & Multi-Actor Split Simulator
* **FR-8.1:** System navigation bar shall allow instant switching between **User Portal**, **Admin Command**, **IT Serviceman**, and **Split Simulator**.
* **FR-8.2:** System shall allow switching user and technician test identities dynamically.
* **FR-8.3:** **Split Simulator** shall display all three portals side-by-side in real time, validating synchronized state mutations.

---

### FR-9: System Audit Trail & Compliance Logging
* **FR-9.1:** System shall record an immutable audit log for every operational event:
  * `CREATED_REQUEST`
  * `ASSIGNED_TECHNICIAN`
  * `STARTED_SERVICE`
  * `MARKED_COMPLETED`
  * `TERMINATED_SESSION`
  * `MARKED_ABSENT` / `MARKED_UNOCCUPIED`
* **FR-9.2:** Audit logs shall capture timestamp, actor name, actor role, action code, and full event details.

---

## 4. Non-Functional Requirements (NFR)

| NFR Category | Requirement Specification | Metric / Target |
| :--- | :--- | :--- |
| **Performance** | Odds calculation and UI dispatch modal render time | < 50ms for up to 100 technicians |
| **Response Time** | API response time for ticket creation and dispatch | P99 < 150ms |
| **Availability** | System uptime and availability | 99.95% SLA |
| **Security** | Role-Based Access Control (RBAC) & data sanitization | Strict authorization guards; zero unauthorized overrides |
| **Data Integrity** | Concurrency control during simultaneous dispatches | ACID transaction locks; zero duplicate assignments |
| **Accessibility** | UI contrast, keyboard navigation, and ARIA labels | WCAG 2.1 AA Compliance |

---

## 5. Success Metrics & Key Performance Indicators (KPIs)

```
+----------------------------------------------------------------------------------------------------+
|                                      PRODUCT SUCCESS SCORECARD                                     |
+------------------------------------+-------------------------------+-------------------------------+
| KPI METRIC                         | BASELINE (LEGACY SYSTEM)      | TARGET (NEW PLATFORM)         |
+------------------------------------+-------------------------------+-------------------------------+
| Technician Workload Variance       | ± 45% Standard Deviation      | < 8% Standard Deviation       |
| Mean Time to Dispatch (MTTD)       | 18.5 Minutes                  | < 2.0 Minutes                 |
| Mean Time to Resolution (MTTR)     | 62.0 Minutes                  | < 25.0 Minutes                |
| Ticket Closure Verification Rate   | 42% (Often closed without user)| 100% (Enforced Termination)   |
| Requester CSAT Rating              | 3.4 / 5.0                     | > 4.8 / 5.0                   |
+------------------------------------+-------------------------------+-------------------------------+
```

---

## 6. Out-of-Scope & Future Roadmap

* **Out of Scope for v2.0:**
  * Native GPS / Geofencing tracking of technicians.
  * AI-powered automated ticket diagnostic suggestions (reserved for v2.5).
  * Direct procurement & hardware inventory ordering integration.
* **Roadmap for v2.5 / v3.0:**
  * Multi-building routing and walking time optimization.
  * Slack & Microsoft Teams conversational bot integration.
  * Predictive hardware failure analytics.

---
*End of Product Requirements Document.*
