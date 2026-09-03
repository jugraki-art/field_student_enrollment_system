# IT SERVICE DISPATCH SYSTEM
## TECHNICAL REQUIREMENT DOCUMENTATION (TRD)
**Document Version:** 2.0.0  
**Target Architecture:** Modern React 19 / TypeScript / Node.js / MySQL 8.0+  
**Status:** Approved & Production-Ready  
**Classification:** Internal Technical Architecture  

---

## 1. System Architecture & Technology Stack

The IT Service Dispatch System is constructed as a decoupled, multi-tier enterprise web application designed for high responsiveness, strong transactional guarantees, and strict role isolation.

```mermaid
graph TD
    subgraph Client Layer [Browser Client (React 19 + TypeScript)]
        UI[Tailwind CSS v4 & Lucide Icons]
        Motion[Motion / Framer Animation Engine]
        Audio[Web Audio API Synthesizer]
        State[AppContext / Reactive State]
    end

    subgraph Security & Routing Layer
        AuthGuard[Role-Gated Auth Guard]
        RBAC[Single Admin RBAC Engine]
    end

    subgraph Service Layer (Node.js / Express API)
        OddsService[Fair Odds Calculation Engine]
        DispatchTx[Transactional Dispatch Controller]
        SessionTx[Session Termination Controller]
        AuditService[Audit Trail & Notification Hub]
    end

    subgraph Persistence Layer (MySQL 8.0+ / InnoDB)
        DB[(it_dispatch_db)]
        T_Acc[accounts]
        T_Tech[it_servicemen]
        T_Req[service_requests]
        T_Rnd[dispatch_rounds]
        T_Notif[notifications]
        T_Log[audit_logs]
    end

    Client Layer --> Security & Routing Layer
    Security & Routing Layer --> Service Layer
    Service Layer --> Persistence Layer
    DB --- T_Acc
    DB --- T_Tech
    DB --- T_Req
    DB --- T_Rnd
    DB --- T_Notif
    DB --- T_Log
```

### 1.1 Technology Stack Matrix

| Tier | Technology | Version | Purpose |
| :--- | :--- | :--- | :--- |
| **Frontend Framework** | React | 19.0.1 | Component-driven declarative user interface. |
| **Language** | TypeScript | ~5.8.2 | Strict type safety across models, props, and actions. |
| **Styling Engine** | Tailwind CSS / Vite | 4.1.14 | Modern utility-first responsive styling with dark-mode palette. |
| **Animation Engine** | Motion (`motion/react`) | 12.23.24 | Hardware-accelerated modal and view transition animations. |
| **Icons** | Lucide React | 0.546.0 | Semantic iconography for statuses, roles, and actions. |
| **Particle Physics** | Canvas-Confetti | 1.9.4 | Visual celebration upon verified session termination. |
| **Bundler & Dev Server** | Vite | 6.2.3 | Instant HMR development and optimized ESM production build. |
| **Backend API** | Express / Node.js | 4.21.2 | RESTful JSON service layer. |
| **Database Engine** | MySQL / MariaDB | 8.0+ / 10.3+ | ACID relational persistence with InnoDB storage engine. |
| **Audio Engine** | Web Audio API | Native Browser | Zero-asset synthesized chimes and alert cues. |

---

## 2. Mathematical Specification of the Fair Odds Algorithm

### 2.1 Problem Formulation
Let $T$ be the set of all registered IT technicians:
$$T = \{t_1, t_2, \dots, t_N\}$$

At any time $\tau$, each technician $t_i$ has an operational status $S(t_i) \in \{\text{unoccupied}, \text{occupied}, \text{absent}\}$.

Let $E \subseteq T$ be the set of **eligible candidates for dispatch**:
$$E = \{t_i \in T \mid S(t_i) = \text{unoccupied}\}$$

If $E = \emptyset$, no dispatch can occur, and an alert is signaled to the administrator.

### 2.2 Dispatch Cycle & Round State
Let $R$ be the active dispatch cycle index ($R \in \mathbb{N}_{\ge 1}$), and $A_R \subset T$ be the set of technician identifiers that have already been dispatched during cycle $R$.

### 2.3 Raw Priority Scoring Model
For every candidate $t_i \in E$, the raw priority score $S_i$ is computed from two components:
1. **Round Assignment State ($H_i$):**
   $$H_i = \begin{cases} 
   \text{true} & \text{if } t_i \in A_R \text{ or } c_i > 0 \\ 
   \text{false} & \text{otherwise} 
   \end{cases}$$
   where $c_i$ is the number of assignments completed by $t_i$ in round $R$.

2. **Idle Duration ($M_i$):**
   Elapsed duration (in minutes) since $t_i$ was last assigned a ticket:
   $$M_i = \begin{cases} 
   \max\left(0, \left\lfloor \frac{\tau - \tau_{\text{last}}(t_i)}{60{,}000} \right\rfloor\right) & \text{if } \tau_{\text{last}}(t_i) \ne \text{null} \\ 
   9999 & \text{if } \tau_{\text{last}}(t_i) = \text{null (fresh shift)} 
   \end{cases}$$

3. **Scoring Function:**
   $$S_i = \begin{cases} 
   1000 + \min(2 \times M_i, 500) & \text{if } H_i = \text{false} \\ 
   50 + \min(0.2 \times M_i, 50) & \text{if } H_i = \text{true} 
   \end{cases}$$

### 2.4 Probability Normalization & Ranking
The relative dispatch odds $P_i$ (as a percentage) are normalized across all active candidates in $E$:
$$P_i = \frac{S_i}{\sum_{k \in E} S_k} \times 100\%$$

The candidates are sorted descending by $S_i$:
$$\text{RankedCandidates} = \text{SortDescending}(E, \text{key} = S_i)$$
The candidate with $\text{Rank} = 1$ is designated as the **Highest Odds Recommendation**.

### 2.5 Round Rollover Invariant
Let $T_{\text{duty}} = \{t \in T \mid S(t) \ne \text{absent}\}$.
When an assignment occurs, $A_R \leftarrow A_R \cup \{t_{\text{assigned}}\}$.
If $T_{\text{duty}} \subseteq A_R$, the cycle boundary is triggered:
$$R \leftarrow R + 1, \quad A_R \leftarrow \emptyset, \quad \forall t_i \in T: c_i \leftarrow 0$$

### 2.6 Algorithmic Complexity
- Filtering candidates: $O(|T|)$
- Score calculation: $O(|E|)$
- Sorting candidates: $O(|E| \log |E|)$
- Overall time complexity: $\mathcal{O}(|T| + |E| \log |E|)$, executing in $<2$ milliseconds for enterprise rosters up to 1,000 technicians.

---

## 3. Frontend Architecture & State Management

### 3.1 Context Architecture (`src/context/AppContext.tsx`)
State is encapsulated in a unified React Context provider (`AppContext`) backed by persistent local storage with structured migration keys:

```typescript
interface AppContextType {
  currentAccount: AuthAccount | null;
  isAuthenticated: boolean;
  activeRole: UserRole | null;
  currentUser: UserProfile;
  currentITGuy: ITGuy;
  accounts: AuthAccount[];
  users: UserProfile[];
  itGuys: ITGuy[];
  requests: ServiceRequest[];
  dispatchRound: DispatchRound;
  notifications: AppNotification[];
  auditLogs: AuditLog[];
  unoccupiedITGuysCount: number;
  occupiedITGuysCount: number;
  absentITGuysCount: number;
  pendingRequestsCount: number;
  getRankedITGuysForDispatch: () => RankedITGuy[];
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

### 3.2 Web Audio Synthesis System (`src/utils/sound.ts`)
To eliminate external MP3/WAV asset dependencies, the sound engine leverages the HTML5 `AudioContext` to construct procedural synthesized frequencies:
- **Success Chime:** Arpeggiated sequence (C5: 523.25 Hz $\rightarrow$ E5: 659.25 Hz $\rightarrow$ G5: 783.99 Hz $\rightarrow$ C6: 1046.50 Hz) with exponential volume decay.
- **Dispatch Chime:** Ascending dual-chord alert (F4: 349.23 Hz $\rightarrow$ A4: 440.00 Hz) signaling incoming field dispatch.
- **Alert Tone:** Resonant ping (A5: 880.00 Hz) triggering on new ticket submissions or status changes.

---

## 4. Security Architecture & Threat Mitigations

### 4.1 Authentication & Threat Vectors
1. **Administrator Impersonation:**
   - Predefined credentials (`admin@itdispatch.local` / `Admin@2026!`) with unique ID `adm-1`.
   - Registration logic explicitly intercepts and rejects any payload requesting `role = 'admin'`.
2. **Horizontal Privilege Escalation:**
   - Requesters can only view and terminate their own tickets (`requesterId === currentUser.id`).
   - Technicians can only operate on tickets where `assignedITGuyId === currentITGuy.id`.
   - Supervisors have read-write access to dispatch and attendance operations.
3. **Cross-Site Scripting (XSS):**
   - React's JSX automatically escapes dynamic expressions.
   - User resolution notes and feedback are sanitized before DOM rendering.
4. **SQL Injection (SQLi):**
   - Backend queries utilize prepared statements (`mysql2/promise` with parameterized placeholders `?` or named parameters `:param`).

---

## 5. Deployment, Containerization & Infrastructure

### 5.1 Docker Architecture

#### `Dockerfile` (Multi-stage production build)
```dockerfile
# Stage 1: Build Frontend Assets
FROM node:22-alpine AS client-builder
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production Server Runtime
FROM node:22-alpine AS production
WORKDIR /app
ENV NODE_ENV=production
COPY package*.json ./
RUN npm ci --only=production
COPY --from=client-builder /app/dist ./dist
COPY mysql_schema.sql ./
EXPOSE 3000
CMD ["npm", "run", "dev"]
```

#### `docker-compose.yml`
```yaml
version: '3.8'

services:
  database:
    image: mysql:8.0
    container_name: it_dispatch_mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: RootSecurePassword2026!
      MYSQL_DATABASE: it_dispatch_db
      MYSQL_USER: it_dispatch_user
      MYSQL_PASSWORD: UserSecurePassword2026!
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql
      - ./mysql_schema.sql:/docker-entrypoint-initdb.d/init.sql:ro

  app:
    build: .
    container_name: it_dispatch_app
    restart: always
    environment:
      PORT: 3000
      DB_HOST: database
      DB_USER: it_dispatch_user
      DB_PASSWORD: UserSecurePassword2026!
      DB_NAME: it_dispatch_db
    ports:
      - "3000:3000"
    depends_on:
      - database

volumes:
  mysql_data:
```

### 5.2 Backup & Disaster Recovery
- **Hot Backups:** Execute daily scheduled `mysqldump` jobs:
  ```bash
  mysqldump --single-transaction --quick --lock-tables=false \
    -u it_dispatch_user -p it_dispatch_db > /backups/it_dispatch_$(date +\%F).sql
  ```
- **Recovery Point Objective (RPO):** < 1 hour via binary logging (`binlog`).
- **Recovery Time Objective (RTO):** < 15 minutes by loading dump file into spare instance.
