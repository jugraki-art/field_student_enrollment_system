# IT SERVICE DISPATCH & FAIR ODDS MANAGEMENT SYSTEM
## TECHNICAL REQUIREMENTS & SYSTEM SPECIFICATION (TRD)
**System Version:** 2.0.0 Enterprise Edition  
**Document Code:** TRD-2026-V2  
**Classification:** Technical Architecture & Engineering Standards  
**Target Stakeholders:** Software Engineers, DevOps/SRE, QA Engineers, Security Architects  
**Last Updated:** September 2026  

---

## 1. Technical Architecture & System Topology

The **IT Service Dispatch & Fair Odds Management Platform** is engineered as a reactive, low-latency Single Page Application (SPA) backed by a high-throughput real-time backend API.

```
+----------------------------------------------------------------------------------------------------+
|                                  TECHNICAL ARCHITECTURE TOPOLOGY                                   |
+----------------------------------------------------------------------------------------------------+
|                                                                                                    |
|    [ CLIENT APPLICATION LAYER ]                                                                    |
|    +------------------------------------------------------------------------------------------+    |
|    |  React 19 SPA • TypeScript 5.8 • Vite 6 • Tailwind CSS v4 • Motion v12                  |    |
|    |  +---------------------+  +------------------------+  +-------------------------------+  |    |
|    |  | React Context Store |  | Web Audio Synthesizer  |  | Canvas Confetti Engine        |  |    |
|    |  | (Optimistic State)  |  | (sound.ts Oscillator)  |  | (canvas-confetti)             |  |    |
|    |  +---------------------+  +------------------------+  +-------------------------------+  |    |
|    +------------------------------------------------------------------------------------------+    |
|                                              |                                                     |
|                                    HTTPS REST / WSS WebSocket                                      |
|                                              |                                                     |
|    [ EDGE & GATEWAY LAYER ]                  v                                                     |
|    +------------------------------------------------------------------------------------------+    |
|    |  NGINX Reverse Proxy • SSL Termination • Rate Limiter (Token Bucket) • CORS Guard        |    |
|    +------------------------------------------------------------------------------------------+    |
|                                              |                                                     |
|    [ BACKEND SERVICE LAYER ]                 v                                                     |
|    +------------------------------------------------------------------------------------------+    |
|    |  Node.js v24 LTS + Express / Fastify (TypeScript)                                         |    |
|    |  +---------------------+  +------------------------+  +-------------------------------+  |    |
|    |  | Odds Calculation    |  | RBAC & Permission Guard|  | Real-Time Event Publisher     |  |    |
|    |  | Engine (O(N log N)) |  | (Admin/Tech/User)      |  | (Redis Pub/Sub -> WS Clients) |  |    |
|    |  +---------------------+  +------------------------+  +-------------------------------+  |    |
|    +------------------------------------------------------------------------------------------+    |
|                                              |                                                     |
|    [ DATA STORAGE & CACHING LAYER ]          v                                                     |
|    +------------------------------------------------------------------------------------------+    |
|    |  PostgreSQL 16 (ACID Relational Storage) • PgBouncer Pooling • Redis 7 (In-Memory PubSub)|    |
|    +------------------------------------------------------------------------------------------+    |
|                                                                                                    |
+----------------------------------------------------------------------------------------------------+
```

---

## 2. Frontend Engineering & Technology Stack

### 2.1 Core Dependencies & Versions
* **UI Framework:** `React 19.0.1` + `React DOM 19.0.1`
* **Language & Type System:** `TypeScript 5.8.2` (Strict mode: `true`, `noImplicitAny: true`)
* **Build Tooling & Bundler:** `Vite 6.2.3` with `@vitejs/plugin-react`
* **CSS Framework:** `Tailwind CSS 4.1.14` with `@tailwindcss/vite`
* **Motion & Animation Engine:** `motion 12.23.24` (Framer Motion v12)
* **Iconography:** `lucide-react 0.546.0`
* **Particle Celebration:** `canvas-confetti 1.9.4`
* **Web Audio Synthesis:** Native HTML5 `AudioContext` API via `sound.ts`

### 2.2 Client State Architecture & Context Provider
The application state is centralized in `src/context/AppContext.tsx` providing optimistic state transitions, reactive synchronization, and local storage persistence:

```typescript
// AppContext Interface Schema
interface AppContextType {
  // Navigation & Identity State
  activeRole: UserRole | 'split';
  setActiveRole: (role: UserRole | 'split') => void;
  currentUser: UserProfile;
  setCurrentUserId: (id: string) => void;
  currentITGuy: ITGuy;
  setCurrentITGuyId: (id: string) => void;
  
  // Data Collections
  users: UserProfile[];
  itGuys: ITGuy[];
  requests: ServiceRequest[];
  dispatchRound: DispatchRound;
  notifications: AppNotification[];
  auditLogs: AuditLog[];
  
  // Computed Properties & Selectors
  unoccupiedITGuysCount: number;
  occupiedITGuysCount: number;
  absentITGuysCount: number;
  pendingRequestsCount: number;
  activeRequestsForCurrentUser: ServiceRequest[];
  completedAwaitingTerminationForCurrentUser: ServiceRequest[];
  activeTaskForCurrentITGuy: ServiceRequest | null;
  getRankedITGuysForDispatch: () => RankedITGuy[];
  
  // Action Handlers
  createRequest: (params: CreateRequestParams) => Promise<string>;
  assignITGuy: (requestId: string, itGuyId: string) => void;
  updateITGuyStatusByAdmin: (itGuyId: string, newStatus: 'absent' | 'unoccupied') => void;
  startService: (requestId: string) => void;
  completeServiceByIT: (requestId: string, resolutionNotes: string) => void;
  terminateSession: (requestId: string, rating: number, feedback?: string) => void;
  markNotificationAsRead: (notificationId: string) => void;
  markAllNotificationsAsRead: () => void;
  resetAllDataToDefault: () => void;
}
```

---

## 3. Odds Algorithm & Algorithmic Analysis

### 3.1 Mathematical Logic Implementation (`src/utils/oddsCalculator.ts`)

```typescript
export function calculateOddsAndRankings(
  allITGuys: ITGuy[],
  currentRound: DispatchRound
): RankedITGuy[] {
  // 1. Filter: Only unoccupied technicians are eligible
  const unoccupied = allITGuys.filter((guy) => guy.status === 'unoccupied');
  if (unoccupied.length === 0) return [];

  const now = Date.now();
  const assignedInRoundSet = new Set(currentRound.assignedTechnicianIds);

  // 2. Compute Raw Score per candidate
  const scoredGuys = unoccupied.map((guy) => {
    const hasBeenAssignedInRound = assignedInRoundSet.has(guy.id) || guy.currentRoundAssignments > 0;
    
    let idleMinutes = 9999;
    if (guy.lastAssignedAt) {
      idleMinutes = Math.max(0, Math.floor((now - new Date(guy.lastAssignedAt).getTime()) / 60000));
    }

    let rawScore = 0;
    let explanation = '';

    if (!hasBeenAssignedInRound) {
      // Baseline 1000 + idle bonus (up to 500)
      const idleBonus = Math.min(idleMinutes * 2, 500);
      rawScore = 1000 + idleBonus;
      explanation = guy.lastAssignedAt === null
        ? 'Fresh turn: Never assigned in recent shifts. Maximum priority.'
        : `Selected earlier (${formatDuration(idleMinutes)} ago). Awaiting turn in Round ${currentRound.roundNumber}.`;
    } else {
      // Significantly decayed odds (Base 50 + minor idle bonus up to 50)
      const minorIdleBonus = Math.min(idleMinutes * 0.2, 50);
      rawScore = 50 + minorIdleBonus;
      explanation = `Low odds: Already selected in Round ${currentRound.roundNumber} (${formatDuration(idleMinutes)} ago).`;
    }

    return { guy, rawScore, idleMinutes, hasBeenAssignedInRound, explanation };
  });

  // 3. Compute Total Score & Sort Descending
  const totalScore = scoredGuys.reduce((acc, curr) => acc + curr.rawScore, 0);
  scoredGuys.sort((a, b) => b.rawScore - a.rawScore);

  // 4. Normalize to Integer Percentages (summing exactly to 100%)
  let accumulatedPercentage = 0;
  return scoredGuys.map((item, index) => {
    const rawPct = totalScore > 0 ? (item.rawScore / totalScore) * 100 : 0;
    const roundedPct = index === scoredGuys.length - 1 && scoredGuys.length > 1
      ? Math.max(1, Math.round(100 - accumulatedPercentage))
      : Math.max(1, Math.round(rawPct));
    
    accumulatedPercentage += roundedPct;

    return {
      itGuy: item.guy,
      oddsPercentage: roundedPct,
      priorityScore: Math.round(item.rawScore),
      isHighestOdds: index === 0,
      rank: index + 1,
      explanation: index === 0 ? `⭐ Top Recommendation: ${item.explanation}` : item.explanation,
      hasBeenAssignedInCurrentRound: item.hasBeenAssignedInRound,
      timeSinceLastAssignedFormatted: formatDuration(item.idleMinutes),
    };
  });
}
```

### 3.2 Complexity Analysis:
* **Time Complexity:** $\mathcal{O}(N \log N)$ where $N = |\{	ext{unoccupied technicians}\}|$.
  * Filtering: $\mathcal{O}(M)$ where $M$ is total technicians.
  * Score calculation: $\mathcal{O}(N)$.
  * Sorting: $\mathcal{O}(N \log N)$ using JavaScript engine Timsort.
  * Normalization: $\mathcal{O}(N)$.
  * *Total Execution Time for $N = 100$: $< 0.4	ext{ms}$.*
* **Space Complexity:** $\mathcal{O}(N)$ auxiliary memory for ranking objects.

---

## 4. Web Audio Subsystem Specification (`src/utils/sound.ts`)

To avoid external network dependencies and latency, the system utilizes synthesized sine/triangle oscillator waves via HTML5 Web Audio:

```typescript
class SoundManager {
  private ctx: AudioContext | null = null;

  private init() {
    if (!this.ctx && typeof window !== 'undefined') {
      const AudioContextClass = window.AudioContext || (window as any).webkitAudioContext;
      if (AudioContextClass) this.ctx = new AudioContextClass();
    }
  }

  // Chime 1: Alert Tone (440Hz -> 880Hz Dual Beep)
  playAlertTone() {
    this.playToneSequence([
      { freq: 440, duration: 0.08, type: 'sine' },
      { freq: 880, duration: 0.12, type: 'sine', delay: 0.1 }
    ]);
  }

  // Chime 2: Dispatch Chime (Ascending Major Triad C5-E5-G5)
  playDispatchChime() {
    this.playToneSequence([
      { freq: 523.25, duration: 0.08, type: 'sine' },
      { freq: 659.25, duration: 0.08, type: 'sine', delay: 0.08 },
      { freq: 783.99, duration: 0.18, type: 'sine', delay: 0.16 }
    ]);
  }

  // Chime 3: Success Chime (G4-C5-E5-G5 Arpeggio + Confetti Sound)
  playSuccessChime() {
    this.playToneSequence([
      { freq: 392.00, duration: 0.06, type: 'sine' },
      { freq: 523.25, duration: 0.06, type: 'sine', delay: 0.06 },
      { freq: 659.25, duration: 0.06, type: 'sine', delay: 0.12 },
      { freq: 1046.50, duration: 0.25, type: 'sine', delay: 0.18 }
    ]);
  }
}
```

---

## 5. Security Architecture & RBAC Permission Matrix

| Operation / Action | User (`user`) | Admin (`admin`) | IT Serviceman (`it_guy`) |
| :--- | :---: | :---: | :---: |
| `CREATE_REQUEST` | **ALLOWED** | **ALLOWED** | DENIED |
| `VIEW_ALL_PENDING_QUEUE` | DENIED (Own only) | **ALLOWED** | DENIED |
| `EVALUATE_DISPATCH_ODDS` | DENIED | **ALLOWED** | DENIED |
| `ASSIGN_TECHNICIAN` | DENIED | **ALLOWED** | DENIED |
| `MARK_TECHNICIAN_ABSENT` | DENIED | **ALLOWED** | DENIED |
| `MARK_TECHNICIAN_UNOCCUPIED` | DENIED | **ALLOWED** | DENIED |
| `START_SERVICE` | DENIED | DENIED | **ALLOWED (Assigned only)** |
| `COMPLETE_SERVICE_BY_IT` | DENIED | DENIED | **ALLOWED (Assigned only)** |
| `TERMINATE_SESSION` | **ALLOWED (Owner only)** | OVERRIDE ONLY | DENIED |
| `VIEW_AUDIT_LOGS` | DENIED | **ALLOWED** | DENIED |

---

## 6. DevOps, Docker Containerization & CI/CD Pipeline

### 6.1 Multi-Stage Production Dockerfile

```dockerfile
# Stage 1: Build Frontend Assets
FROM node:24-alpine AS builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production Web Server
FROM nginx:alpine AS runner
COPY --from=builder /app/dist /usr/share/nginx/html
COPY nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=3s --retries=3   CMD wget -q --spider http://localhost/ || exit 1
CMD ["nginx", "-g", "daemon off;"]
```

### 6.2 CI/CD GitHub Actions Workflow

```yaml
name: CI/CD Pipeline
on: [push, pull_request]

jobs:
  validate-and-build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup Node.js 24
        uses: actions/setup-node@v4
        with:
          node-version: 24
          cache: 'npm'
      - name: Install Dependencies
        run: npm ci
      - name: Type Check & Lint
        run: npm run lint
      - name: Build Production Bundle
        run: npm run build
```

---
*End of Technical Requirements & System Specification.*
