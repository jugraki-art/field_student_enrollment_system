# IT SERVICE DISPATCH & FAIR ODDS MANAGEMENT SYSTEM
## UI/UX DESIGN BRIEF & DESIGN SYSTEM SPECIFICATION
**System Version:** 2.0.0 Enterprise Edition  
**Document Code:** UXD-2026-V2  
**Classification:** Product Design, Visual Identity & UI Kit Specifications  
**Target Stakeholders:** UI/UX Designers, Product Designers, Frontend Engineers, QA Design Reviewers  
**Last Updated:** September 2026  

---

## 1. Design Philosophy & Creative Vision

The **IT Service Dispatch Platform** UI/UX is built on the philosophy of **Operational Transparency, Frictionless Action, and Equitable Clarity**. In mission-critical enterprise support environments, cognitive load must be minimized while operational state changes must be instantly discernible.

```
+----------------------------------------------------------------------------------------------------+
|                                      CORE UI/UX DESIGN PILLARS                                     |
+-----------------------------------+--------------------------------+-------------------------------+
| 1. STATE-FIRST VISUAL LANGUAGE    | 2. ZERO-FRICTION INTAKE        | 3. MULTI-SENSORY FEEDBACK     |
| Statuses (Unoccupied, Occupied,   | One-click issue presets,       | Harmonized Web Audio chimes,  |
| Absent) are visually loud with    | location auto-fill, and clear  | glowing status pulses, and    |
| distinct color tokens and pings.  | form field hierarchy.          | celebratory particle bursts.  |
+-----------------------------------+--------------------------------+-------------------------------+
```

### Visual Aesthetics:
* **Style:** Modern Deep Slate Dark Theme with Vibrant Neon Accents.
* **Mood:** High-tech, trustworthy, organized, highly responsive, and premium.
* **Elevation:** Subdued glassmorphic backdrops (`backdrop-blur-md`), dark border strokes (`border-slate-800`), and layered ambient glow shadows (`shadow-indigo-500/20`).

---

## 2. Design Tokens & Color Palette

```
+----------------------------------------------------------------------------------------------------+
|                                      SYSTEM COLOR TOKEN MATRIX                                     |
+-------------------+---------------+---------------------+------------------------------------------+
| TOKEN NAME        | HEX CODE      | TAILWIND CLASS      | SEMANTIC INTENT & USAGE                  |
+-------------------+---------------+---------------------+------------------------------------------+
| Background Base   | `#020617`     | `bg-slate-950`      | Global canvas background                 |
| Surface Card      | `#0f172a`     | `bg-slate-900`      | Primary container cards and panels       |
| Surface Overlay   | `#1e293b`     | `bg-slate-800`      | Form inputs, table headers, sub-panels   |
| Border Primary    | `#334155`     | `border-slate-800`  | Structural boundary dividers             |
| Indigo Primary    | `#6366f1`     | `text-indigo-400`   | Primary CTA buttons, brand highlights    |
| Cyan Accent       | `#06b6d4`     | `text-cyan-400`     | Secondary metrics, round progress pills  |
| Emerald Success   | `#10b981`     | `text-emerald-400`  | Unoccupied status, completed resolution  |
| Amber Warning     | `#f59e0b`     | `text-amber-400`    | Occupied status, action required banner  |
| Rose Danger       | `#f43f5e`     | `text-rose-400`     | Absent status, critical urgency badges   |
+-------------------+---------------+---------------------+------------------------------------------+
```

---

## 3. Typography & Spatial Grid Hierarchy

### 3.1 Font Family & Weights
* **Primary Sans-Serif:** `Inter`, `Segoe UI`, `-apple-system`, `BlinkMacSystemFont`, `sans-serif`.
* **Monospace Data:** `Consolas`, `JetBrains Mono`, `Fira Code`, `monospace` (used for ticket numbers e.g. `REQ-1051`, timestamps, and technical codes).

### 3.2 Typographic Hierarchy Scale:
| Level | Font Size | Line Height | Font Weight | Semantic Application |
| :--- | :--- | :--- | :--- | :--- |
| **Hero Title** | `24px / 1.5rem` | `32px / 2.0rem` | `font-bold` (700) | Portal main headers, role title cards |
| **Section Title** | `18px / 1.125rem`| `24px / 1.5rem` | `font-bold` (700) | Panel headers, card grouping headers |
| **Subhead** | `14px / 0.875rem`| `20px / 1.25rem` | `font-semibold` (600)| Modal titles, ticket subject titles |
| **Body Standard** | `13px / 0.8125rem`| `18px / 1.125rem`| `font-normal` (400) | Issue descriptions, resolution notes |
| **Meta & Captions**| `11px / 0.6875rem`| `16px / 1.0rem` | `font-medium` (500) | Timestamps, locations, badges, labels |

---

## 4. Key Component Anatomy & UI Kit Specifications

### 4.1 Sticky Glassmorphic Navigation Bar (`src/components/Navbar.tsx`)
* **Layout:** Fixed sticky top bar (`sticky top-0 z-40 bg-slate-950/80 backdrop-blur-md border-b border-slate-800`).
* **Left:** Brand badge with gradient avatar (`from-indigo-600 to-cyan-400`), platform title, and active **Round Pill** (`Round 1`).
* **Center:** Segmented Role Navigation Tabs (`User Portal`, `Admin Command`, `IT Serviceman`, `Split Simulator`) with active pill highlight.
* **Right:**
  * **Odds Engine Button:** Opens algorithmic explanation modal.
  * **Actor Switcher:** Dynamic dropdown to test different user/technician accounts.
  * **Notification Bell:** With pulsating red unread badge counter.
  * **Reset Data Button:** Allows resetting simulation data back to factory seed.

```
+----------------------------------------------------------------------------------------------------+
| [IT] IT Dispatch & Service Center (Round 1) | [User] [Admin] [IT Guy] [Split] | Odds | Bell(2) | ↺ |
+----------------------------------------------------------------------------------------------------+
```

---

### 4.2 Requester Golden Action Banner (`src/components/UserPortal.tsx`)
* **Trigger:** Appears when any request reaches `completed_by_it` status.
* **Styling:** Dynamic gradient container (`bg-gradient-to-r from-amber-950/70 via-indigo-950/60 to-emerald-950/70 border-2 border-amber-500/60 shadow-2xl`).
* **Elements:**
  * Bouncing golden sparkles icon.
  * Prominent **Action Required** badge.
  * Technician name and completion notification message.
  * Blockquote rendering the technician's logged **Resolution Notes**.
  * Large gradient action button: **Terminate Session & Rate**.

---

### 4.3 Admin Odds Evaluation & Dispatch Modal (`src/components/AdminPortal.tsx`)
* **Layout:** Backdrop modal with glassmorphism blur (`bg-black/80 backdrop-blur-sm`).
* **Ticket Overview Card:** Displays ticket number, title, requester name, department, and room.
* **Ranked Candidate Cards:**
  * Radio selection toggle.
  * Technician avatar and duty role.
  * **⭐ HIGHEST ODDS** golden badge on rank 1 candidate.
  * Clear explanation string detailing idle duration and round status.
  * Large percentage number display (e.g., `72% Selection Odds`).
* **Action Footer:** Push notification confirmation text + **Push Notification & Assign** gradient button.

---

### 4.4 Attendance Management Grid (`src/components/AdminPortal.tsx`)
* **Card Anatomy:**
  * Technician avatar and online indicator.
  * Duty status badge:
    * **Unoccupied:** Emerald pill with pinging dot (`animate-ping`).
    * **Occupied:** Amber pill with active job link.
    * **Absent:** Rose pill with red dot.
  * Skill tag chips.
  * Metrics row: Round Turns, Total Completed, Rating (★).
  * **Admin Attendance Control Button:**
    * *Mark Absent* (Rose outline button; disabled if occupied).
    * *Mark Back to Work* (Emerald filled button when absent).

---

### 4.5 IT Serviceman Active Task Console (`src/components/ITGuyPortal.tsx`)
* **Header:** Ticket number badge, category pill, urgency badge, issue subject.
* **2-Column Detail View:**
  * Left: Problem details and symptoms.
  * Right: Service requester profile, phone number, and physical room location.
* **Action Step Bar:**
  * Step 1 (`assigned`): **Acknowledge & Start Service** (Indigo button).
  * Step 2 (`in_progress`): **Mark Service as Completed** (Emerald button).
  * Step 3 (`completed_by_it`): **Awaiting User Session Termination...** (Pulsing amber indicator).

---

### 4.6 Multi-Actor Split Simulator (`src/components/SplitSimulator.tsx`)
* **Layout:** 3-column responsive grid rendering User Portal, Admin Command, and IT Serviceman Workspace side-by-side.
* **Header Stepper Hint:** Displays the 4-step execution flow (`1. User Requests -> 2. Admin Evaluates Odds -> 3. IT Guy Resolves -> 4. User Terminates`).

---

## 5. Micro-Interactions, Motion & Sound Design

### 5.1 Framer Motion Animations
* **Portal Transitions:**
  ```tsx
  <motion.div
    initial={{ opacity: 0, y: 8 }}
    animate={{ opacity: 1, y: 0 }}
    exit={{ opacity: 0, y: -8 }}
    transition={{ duration: 0.2 }}
  >
  ```
* **Modal Dialog Entrances:** Spring scale animation (`initial: { scale: 0.95, opacity: 0 } -> animate: { scale: 1, opacity: 1 }`).
* **Toasts:** Slide up from bottom right (`initial: { opacity: 0, y: 50 } -> animate: { opacity: 1, y: 0 }`).

### 5.2 Particle Celebrations
* Upon session termination, `canvas-confetti` executes a celebratory particle burst from the bottom center:
  ```typescript
  confetti({
    particleCount: 50,
    spread: 60,
    origin: { y: 0.6 }
  });
  ```

### 5.3 Web Audio Feedback Envelopes
* **Alert Tone:** Dual high-frequency beeps (440Hz -> 880Hz) to signal incoming tickets or urgent completion prompts.
* **Dispatch Chime:** Smooth rising major triad (C5-E5-G5) to confirm technician assignment.
* **Success Chime:** Harmonious 4-tone arpeggio (G4-C5-E5-G5) played during session termination.

---

## 6. Responsive Breakpoints & Accessibility (WCAG 2.1 AA)

| Breakpoint | Width (px) | Layout Adjustments |
| :--- | :--- | :--- |
| **Mobile (`sm`)** | `< 640px` | Single column stacked layouts; simplified navbar with icon-only tools; full-width modal dialogs. |
| **Tablet (`md`)** | `640px - 1024px` | 2-column grids for technician roster; tabbed navigation visible. |
| **Desktop (`lg`)** | `1024px - 1440px`| Full 12-column grid for user portal form and active tracker; 4-card metric summary rows. |
| **Ultra-wide (`xl`)** | `> 1440px` | Enables side-by-side **Split Simulator** 3-column live interactive workbench without horizontal scroll. |

### Accessibility Checklist:
1. **Color Contrast:** All text elements achieve a minimum contrast ratio of `4.5:1` against their slate backgrounds.
2. **Keyboard Focus:** All interactive elements feature visible focus rings (`focus:ring-2 focus:ring-indigo-500`).
3. **Screen Reader Tags:** Semantic HTML `<header>`, `<main>`, `<nav>`, `<form>`, `<button>`, and `aria-label` tags for icon buttons.

---
*End of UI/UX Design Brief & Design System Specification.*
