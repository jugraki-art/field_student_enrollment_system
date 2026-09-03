# IT SERVICE DISPATCH SYSTEM
## COMPREHENSIVE APPLICATION FLOW & INTERACTION DOCUMENTATION
**Document Version:** 2.0.0  
**Target Platform:** Enterprise Web & Mobile Responsive  
**Status:** Approved & Production-Ready  
**Related Components:** User Portal, Admin Portal, IT Serviceman Portal, Fair Odds Dispatch Engine  

---

## 1. Executive Summary & Operational Scope

The **IT Service Dispatch & Fair Iteration Management System** is an enterprise-grade service management platform designed to automate, optimize, and equitably govern internal IT support operations. Traditional IT service desks frequently suffer from:
1. **Technician Dispatch Fatigue & Favoritism:** Certain technicians are repeatedly assigned difficult or frequent tasks while others remain underutilized.
2. **Lack of Lifecycle Accountability:** Technicians mark jobs as complete without end-user verification, leading to premature ticket closure and unresolved tickets.
3. **Attendance & Availability Desynchronization:** Technicians on leave or out of the building are inadvertently assigned urgent tasks, causing critical downtime.

This application resolves these systemic issues through:
- **Fair Round-Robin Odds Allocation Engine:** A mathematical dispatch model that calculates dynamically decaying odds for previously assigned technicians while giving priority to technicians with longer idle times who have not yet served in the current round.
- **Closed-Loop Session Termination:** Technicians can only indicate that technical work is complete; only the requesting end-user holds the authority to "Terminate Session", provide a 1-to-5 star quality rating, and release the technician back into the available pool.
- **Supervisory Attendance Governance:** Strict administrative controls where only the single designated system Administrator can designate technicians as `absent` (out of service area) or restore them to `unoccupied`.
- **Strict Role-Gated Architecture:** Individual accounts are strictly separated into three distinct portals: Requester (`user`), Technician (`it_guy`), and Supervisor (`admin`), with robust server/client security preventing self-registration of administrative privileges.

---

## 2. Actor Personas & Access Boundaries

| Actor Role | Primary Identity | Core Permissions | Key Views & Portals |
| :--- | :--- | :--- | :--- |
| **Administrator** (`admin`) | Single Designated Account (`admin@itdispatch.local`) | Full system oversight; ticket reassignment; manual technician dispatch override; IT attendance management (`absent` / `unoccupied`); audit log review; MySQL database architecture inspector. | **Admin Command Center** (`AdminPortal.tsx`): KPI metrics, Pending dispatch queue, Serviceman roster, Global ticket monitor, Audit trail, MySQL schema manager. |
| **IT Serviceman** (`it_guy`) | Field Technical Engineers & Systems Specialists | View active dispatched assignment; accept & start service (`in_progress`); document technical resolution notes; submit completion for user review (`completed_by_it`); view personal performance metrics & ratings. | **Technician Field Station** (`ITGuyPortal.tsx`): Live dispatch alert card, active service execution console, completed job history, personal rating card. |
| **Service Requester** (`user`) | Corporate Employees & Department Staff | Submit IT service requests with category, urgency, and exact geolocation (Building, Floor, Room); track live ticket status; review technician resolution notes; **mandatorily terminate session** with star rating and feedback. | **Employee Request Hub** (`UserPortal.tsx`): Rapid ticket submission form with presets, active request tracking timeline, session termination modal, personal service history. |

---

## 3. Core State Machines & Lifecycles

### 3.1 Service Request Ticket State Machine

```mermaid
stateDiagram-v2
    [*] --> pending_admin: User Submits Ticket
    pending_admin --> assigned: Admin Dispatches via Fair Odds
    assigned --> in_progress: Technician Clicks "Start Service"
    in_progress --> completed_by_it: Technician Submits Resolution Notes
    completed_by_it --> session_terminated: User Verifies & Terminates Session (Rating 1-5★)
    session_terminated --> [*]
```

#### Ticket Status Definitions:
1. **`pending_admin`**: The request has been submitted by the employee and is queued in the Admin Command Center awaiting technician evaluation and assignment.
2. **`assigned`**: The Admin has assigned a specific technician based on the Fair Odds recommendation (or supervisory discretion). The technician is notified instantly.
3. **`in_progress`**: The technician has arrived on site or accessed the equipment remotely and clicked "Start Service" in their portal.
4. **`completed_by_it`**: Technical labor and repairs are finished. The technician has logged detailed resolution notes. The ticket now locks for the technician and awaits user verification.
5. **`session_terminated`**: The end-user has inspected the resolution, submitted their satisfaction rating (1–5 stars) and optional feedback, and closed the ticket. This action triggers the release of the technician back to `unoccupied`.

---

### 3.2 IT Serviceman Attendance & Duty State Machine

```mermaid
stateDiagram-v2
    [*] --> unoccupied: System Onboarding / Admin Restore
    unoccupied --> occupied: Admin Dispatches Ticket
    occupied --> unoccupied: User Terminates Session
    unoccupied --> absent: Admin Marks Absent (Leave / Out of Area)
    occupied --> absent: Admin Overrides Status (Emergency Leave)
    absent --> unoccupied: Admin Restores to Duty
```

#### Technician Status Definitions:
1. **`unoccupied`**: Technician is on duty, physically present in the work area, and has no active tickets. They are fully eligible for odds calculation and dispatch.
2. **`occupied`**: Technician is currently dispatched to an active ticket (`assigned`, `in_progress`, or `completed_by_it`). They are strictly excluded from new ticket dispatches until the user terminates the session.
3. **`absent`**: Technician is on leave, off-site, sick, or attending training. **Only the Admin can assign or remove this status.** Absent technicians are completely excluded from odds calculations.

---

### 3.3 Dispatch Round-Robin Lifecycle

The system enforces an iterative round-robin cycle to guarantee equal distribution:
- Each cycle starts at **Round $N$** with a pool of eligible (non-absent) technicians.
- When an unoccupied technician is dispatched, their ID is appended to `assignedTechnicianIds` for Round $N$, and their `currentRoundAssignments` increments by 1.
- Their probability score drops dramatically for subsequent dispatches in Round $N$ compared to unassigned technicians.
- **Round Rollover Condition:** When every eligible technician in the roster has received an assignment ($\text{Eligible} \subseteq \text{AssignedInRound}$), the round counter increments to **Round $N+1$**, the `assignedTechnicianIds` set is emptied, all technicians' `currentRoundAssignments` reset to 0, and all unoccupied technicians return to baseline priority.

---

## 4. End-to-End Application Sequences & Step-by-Step Flows

### 4.1 Flow 1: Authentication & Access Control

```mermaid
sequenceDiagram
    autonumber
    actor User as Actor (Employee / IT / Admin)
    participant UI as AuthPortal.tsx
    participant Context as AppContext.tsx
    participant Storage as LocalStorage / MySQL DB
    participant Router as App.tsx (MainContent)

    User->>UI: Enters Email & Password
    UI->>Context: login(email, password)
    alt Admin Login (admin@itdispatch.local)
        Context->>Context: Verify KNOWN_ADMIN_CREDENTIALS
        Context->>Storage: Record AuditLog('ADMIN_AUTHENTICATED')
        Context->>Router: Set activeRole = 'admin'
        Router-->>User: Render AdminPortal.tsx
    else Employee / IT Guy Login
        Context->>Storage: Find account by email & verify password
        Context->>Storage: Record AuditLog('USER_AUTHENTICATED')
        Context->>Router: Set activeRole = account.role
        Router-->>User: Render UserPortal.tsx OR ITGuyPortal.tsx
    end
```

#### Security Implementation Rules:
1. **Admin Exclusivity:** The Administrator account is singular and predefined (`adm-1`). Self-registration with `role = 'admin'` is strictly rejected with a security validation error.
2. **Self-Registration Allowed Roles:** New employees can register as `user` (requester), providing department, contact phone, and building/room. IT staff can register as `it_guy`, selecting their technical specialties (e.g., Hardware, Network, Audio/Visual).
3. **Session Persistence:** On page reload, the active session is securely restored from local storage or backend bearer tokens.

---

### 4.2 Flow 2: Service Request Creation Flow

```mermaid
sequenceDiagram
    autonumber
    actor Requester as Employee (User)
    participant UP as UserPortal.tsx
    participant AppCtx as AppContext.tsx
    participant Sound as SoundManager
    participant Admin as AdminPortal.tsx

    Requester->>UP: Selects Category, Urgency, Physical Geolocation
    Requester->>UP: Inputs Problem Title & Description (or applies Quick Preset)
    Requester->>UP: Clicks "Submit Service Request"
    UP->>AppCtx: createRequest(params)
    AppCtx->>AppCtx: Generate Ticket Number (e.g., REQ-1051)
    AppCtx->>AppCtx: Set Status = 'pending_admin'
    AppCtx->>AppCtx: Create Admin Notification ("New Request REQ-1051")
    AppCtx->>AppCtx: Append to AuditLog ('CREATED_REQUEST')
    AppCtx->>Sound: playAlertTone()
    UP-->>Requester: Display Success Banner & Confetti Toast
    AppCtx-->>Admin: Real-time update in Pending Queue + Badge Counter
```

#### Ticket Creation Constraints:
- Mandatory fields: Title (min 5 chars), Description (min 10 chars), Category (Hardware, Software, Network & WiFi, Printer & Peripherals, Access & Security, Audio/Visual), Urgency (Low, Medium, High, Critical), Building, Floor, Room.
- Pre-set templates are provided for common issues (e.g., "Monitor display blackout", "Conference room audio failure", "Color laser printer paper jam").

---

### 4.3 Flow 3: Fair Odds Calculation & Dispatch Flow

```mermaid
sequenceDiagram
    autonumber
    actor Admin as IT Administrator
    participant AP as AdminPortal.tsx
    participant Calc as oddsCalculator.ts
    participant AppCtx as AppContext.tsx
    participant Tech as ITGuyPortal.tsx
    participant User as UserPortal.tsx

    Admin->>AP: Clicks "Evaluate & Dispatch" on Ticket REQ-1051
    AP->>Calc: calculateOddsAndRankings(itGuys, dispatchRound)
    Calc->>Calc: Filter: status === 'unoccupied'
    loop For each unoccupied technician
        Calc->>Calc: Check if assigned in Round N
        Calc->>Calc: Calculate idle minutes since lastAssignedAt
        Calc->>Calc: Compute rawScore = Baseline + IdleBonus - RoundDecay
    end
    Calc->>Calc: Sum rawScores & Compute Percentage Odds
    Calc->>Calc: Sort descending: Highest Odds (Rank 1) first
    Calc-->>AP: Return RankedITGuy[] with explanations
    AP-->>Admin: Modal displays Candidate List (Top recommendation pre-selected)
    Admin->>AP: Confirms Dispatch (Marcus Vance - 72% Odds)
    AP->>AppCtx: assignITGuy(requestId, itGuyId)
    AppCtx->>AppCtx: Update Ticket: status = 'assigned', assignedITGuy = Marcus
    AppCtx->>AppCtx: Update Technician: status = 'occupied', roundAssignments + 1
    AppCtx->>AppCtx: Update DispatchRound: add to assignedTechnicianIds
    AppCtx->>AppCtx: Push Notification to Technician & Requester
    AppCtx->>AppCtx: Append to AuditLog ('ASSIGNED_TECHNICIAN')
    AppCtx-->>Tech: Instant Dispatch Alert Banner
    AppCtx-->>User: Technician En-Route Notification
```

#### Algorithmic Odds Formula Summary:
- **Technician Unassigned in Current Round:**
  $$\text{Score} = 1000 + \min(\text{IdleMinutes} \times 2, 500)$$
- **Technician Already Assigned in Current Round:**
  $$\text{Score} = 50 + \min(\text{IdleMinutes} \times 0.2, 50)$$
- **Odds Percentage ($P_i$):**
  $$P_i = \frac{\text{Score}_i}{\sum \text{Score}} \times 100\%$$
- The candidate with the highest idle time who has not yet worked in the current round appears as **Rank #1 (Highest Odds)**.

---

### 4.4 Flow 4: IT Service Execution Flow

```mermaid
sequenceDiagram
    autonumber
    actor Tech as IT Serviceman
    participant TP as ITGuyPortal.tsx
    participant AppCtx as AppContext.tsx
    participant User as UserPortal.tsx

    Tech->>TP: Receives Alert & Reviews Location (Building 2, Room 304)
    Tech->>TP: Arrives on site & clicks "Start Working on Ticket"
    TP->>AppCtx: startService(requestId)
    AppCtx->>AppCtx: Update Ticket: status = 'in_progress', startedAt = NOW()
    AppCtx->>AppCtx: Notify Requester ("Technician has commenced work")
    AppCtx->>AppCtx: Append AuditLog ('STARTED_SERVICE')
    Tech->>TP: Performs diagnostic & hardware/software repair
    Tech->>TP: Clicks "Finish Technical Labor"
    TP-->>Tech: Modal prompts for Resolution Notes
    Tech->>TP: Enters notes ("Replaced faulty DP cable, flashed dock firmware")
    Tech->>TP: Submits Resolution
    TP->>AppCtx: completeServiceByIT(requestId, resolutionNotes)
    AppCtx->>AppCtx: Update Ticket: status = 'completed_by_it', completedAt = NOW()
    AppCtx->>AppCtx: Notify Requester ("Action Required: Please verify and terminate session")
    AppCtx->>AppCtx: Append AuditLog ('MARKED_COMPLETED')
    AppCtx-->>User: Urgent Banner: "Technician finished. Verify & Terminate Session"
```

> **Crucial Rule:** The technician **CANNOT** mark the ticket as closed or terminate the session. The technician's status remains `occupied` until the user completes the termination step.

---

### 4.5 Flow 5: Session Termination & Technician Release Flow

```mermaid
sequenceDiagram
    autonumber
    actor Requester as Employee (User)
    participant UP as UserPortal.tsx
    participant AppCtx as AppContext.tsx
    participant Sound as SoundManager
    participant Tech as ITGuyPortal.tsx
    participant Admin as AdminPortal.tsx

    Requester->>UP: Views "Awaiting Verification & Termination" Banner
    Requester->>UP: Tests repaired equipment & clicks "Review & Terminate Session"
    UP-->>Requester: Modal displays Resolution Notes, 5-Star Selector & Feedback Input
    Requester->>UP: Selects Star Rating (e.g. 5 Stars) and optional comments
    Requester->>UP: Clicks "Confirm Session Termination"
    UP->>AppCtx: terminateSession(requestId, rating, feedback)
    AppCtx->>AppCtx: Update Ticket: status = 'session_terminated', terminatedAt = NOW()
    AppCtx->>AppCtx: Update Technician: status = 'unoccupied', clear currentRequestId
    AppCtx->>AppCtx: Recalculate Technician Lifetime Rating & Total Completed Jobs
    AppCtx->>AppCtx: Notify Technician ("Session Terminated with 5★. Status: Unoccupied")
    AppCtx->>AppCtx: Notify Admin ("Ticket REQ-1051 Closed. Marcus Vance is Unoccupied")
    AppCtx->>AppCtx: Append AuditLog ('TERMINATED_SESSION')
    AppCtx->>Sound: playSuccessChime() + confetti()
    UP-->>Requester: Ticket moved to Completed History
    AppCtx-->>Tech: Workstation resets to "Unoccupied (Free & Available)"
```

---

### 4.6 Flow 6: Attendance & Absentee Management Flow

```mermaid
sequenceDiagram
    autonumber
    actor Admin as IT Administrator
    participant AP as AdminPortal.tsx
    participant AppCtx as AppContext.tsx
    participant Tech as ITGuyPortal.tsx

    Admin->>AP: Navigates to "IT Servicemen Roster" tab
    Admin->>AP: Locates Chloe Bennett (Reports on sick leave)
    Admin->>AP: Clicks "Mark Absent" button
    AP-->>Admin: Confirmation dialog confirms action
    Admin->>AP: Confirms Absent status
    AP->>AppCtx: updateITGuyStatusByAdmin(itGuyId, 'absent')
    AppCtx->>AppCtx: Set technician.status = 'absent', isOnline = false
    AppCtx->>AppCtx: Remove technician from current round dispatch calculations
    AppCtx->>AppCtx: Send Notification to Technician ("Marked Absent by Admin")
    AppCtx->>AppCtx: Append AuditLog ('MARKED_ABSENT')
    AppCtx-->>Tech: Portal displays prominent Absent Warning Notice
    Note over Admin,Tech: When Technician Returns to Duty
    Admin->>AP: Clicks "Restore to Unoccupied"
    AP->>AppCtx: updateITGuyStatusByAdmin(itGuyId, 'unoccupied')
    AppCtx->>AppCtx: Set technician.status = 'unoccupied', isOnline = true
    AppCtx-->>Tech: Portal restored to available status
```

---

## 5. Summary of Key Interaction Rules

1. **Only the Admin** can assign tickets, change technician attendance (`absent` / `unoccupied`), and inspect audit logs.
2. **Only the User (Requester)** can terminate a session and release a technician.
3. **Only Unoccupied Technicians** are factored into odds calculations. Occupied and Absent technicians are bypassed.
4. **Odds Naturally Decay** once a technician receives a ticket in the current round, guaranteeing that other available technicians rise to the top of the queue.
5. **Auditing is Immutable:** Every critical transition (`CREATED_REQUEST`, `ASSIGNED_TECHNICIAN`, `STARTED_SERVICE`, `MARKED_COMPLETED`, `TERMINATED_SESSION`, `MARKED_ABSENT`) generates an audit log record with timestamps and actor identities.
