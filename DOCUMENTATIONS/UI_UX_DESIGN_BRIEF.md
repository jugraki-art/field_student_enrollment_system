# UI/UX Design Brief Document

## Project Title: Field Student Enrollment System
**Organization:** Kinondoni Municipal Council HQ  
**Design Theme:** Modern Corporate Civic Portal  

---

## 1. Visual Identity & Design Philosophy

The **Kinondoni Municipal Council Field Student Enrollment System** visual interface is designed to project trust, efficiency, clarity, and modern civic governance. 

### Key Design Principles:
1. **Corporate Municipal Aesthetic:** Uses deep navy blues (`#0f172a`) paired with vibrant primary blues (`#2563eb`) to evoke authority and technical modernization.
2. **Visual Hierarchy & Scannability:** Key information—such as student active/completed status, training durations, and metric statistics—is emphasized using structured card layouts and color-coded status badges.
3. **High Contrast & Clarity:** Clean background surfaces (`#f8fafc`) ensure high contrast against dark typography (`#0f172a`), improving readability during prolonged officer workflows.
4. **Fluid Motion & Micro-Interactions:** Subtle CSS transition effects (hover elevations, button focus rings, sidebar toggle slides) create a responsive, software-grade tactile feel.

---

## 2. Color Palette & Design Tokens

### Primary Palette
| Token Name | Hex Code | Usage | Visual Example |
| :--- | :--- | :--- | :--- |
| `--dark-navy` | `#0f172a` | Primary Sidebar background, Login background gradient start | `██████` |
| `--navbar-bg` | `#1e293b` | Top Navbar background, Sidebar header background | `██████` |
| `--primary-blue` | `#2563eb` | Primary CTA buttons, Header accent border, Active state indicators | `██████` |
| `--primary-hover` | `#1d4ed8` | Primary button hover state | `██████` |
| `--secondary-teal` | `#0f766e` | Secondary buttons (CSV Export, Navigation shortcuts) | `██████` |

### Status & Feedback Tokens
| Token Name | Hex Code | Usage / Badge Context |
| :--- | :--- | :--- |
| `--success-bg` | `#dcfce7` | Active status badge background (`.badge-active`) |
| `--success-text` | `#15803d` | Active status badge text color |
| `--danger-bg` | `#fef2f2` | Delete buttons, validation error alert background (`.alert-error`) |
| `--danger-text` | `#dc2626` | Validation error alert text color |
| `--warning-bg` | `#fef3c7` | System warning notice background |
| `--warning-text` | `#d97706` | System warning notice text color |

### Neutral Surfaces
| Token Name | Hex Code | Usage |
| :--- | :--- | :--- |
| `--bg-main` | `#f8fafc` | Application workspace page background |
| `--card-bg` | `#ffffff` | Content cards, form cards, table cards background |
| `--border-color`| `#e2e8f0` | Table row borders, card outlines, form input borders |
| `--text-primary`| `#0f172a` | Primary headings, body copy |
| `--text-muted`  | `#64748b` | Subtitles, date ranges, helper copy |

---

## 3. Typography Hierarchy

The system uses **Inter** (Google Fonts) as its primary typeface, falling back to `-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`.

```
Display Heading (Page Titles)   : 1.4rem  (22px) | Font-Weight: 700 (Bold)
Card Headings (Section Titles) : 1.15rem (18px) | Font-Weight: 700 (Bold)
Metric Numbers (Counter Cards) : 1.6rem  (26px) | Font-Weight: 800 (ExtraBold)
Body Text (Regular Content)    : 0.92rem (15px) | Font-Weight: 400 (Regular)
Form Labels / Button Text      : 0.88rem (14px) | Font-Weight: 600 (SemiBold)
Badges / Table Headers / Small : 0.78rem (12px) | Font-Weight: 700 (Bold, Uppercase)
```

---

## 4. Component Design Specifications

### 4.1 Navigation Layout Structure
* **Fixed Vertical Sidebar (`.sidebar`):**
  * Width: `260px`
  * Position: `fixed`, `top: 0`, `left: 0`, `height: 100vh`
  * Transition: `width 0.3s cubic-bezier(0.4, 0, 0.2, 1)`
* **Fixed Header Navbar (`.navbar`):**
  * Height: `72px`
  * Position: `fixed`, `top: 0`, `left: 260px`, `right: 0`
  * Bottom Accent Border: `4px solid #2563eb`
* **Main Container (`.main-content`):**
  * Margin-Left: `260px`
  * Padding-Top: `72px`
  * Max-Width: `1240px` (centered inside `.main-container`)

### 4.2 Status Badges (`.status-badge`)
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

### 4.3 Form Input Controls
* **Inputs & Selects (`input`, `select`):**
  * Border: `1px solid #e2e8f0`
  * Border-Radius: `8px`
  * Padding: `11px 14px`
  * Focus State: `border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);`

### 4.4 Buttons (`.btn`)
* **Primary Button (`.btn-primary`):** Background `#2563eb`, hover `#1d4ed8`, rounded `8px`, font weight `600`.
* **Secondary Button (`.btn-secondary`):** Background `#0f766e`, hover `#0d9488`, rounded `8px`.
* **Danger Button (`.btn-danger`):** Background `#ef4444`, hover `#dc2626`, font size `0.82rem`, padding `6px 12px`.

---

## 5. Responsive Design & Breakpoints

* **Desktop View (> 768px):** Full fixed sidebar (`260px`), side-by-side metric grid, multi-column forms, header toggle button active.
* **Mobile & Tablet View (<= 768px):** Sidebar collapses smoothly into full-width top navigation bar, form rows stack vertically (`flex-direction: column`), search input spans 100% width.
