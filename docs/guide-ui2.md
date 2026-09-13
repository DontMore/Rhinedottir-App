```python
markdown_content = """# Document Design & Engineering Guidelines: macOS Web Architecture

This document serves as the canonical UI/UX engineering specification for building desktop-class web applications that mimic the native **macOS** operating system experience (Sonoma/Ventura aesthetic standards). 

Follow these exact architectural patterns, design tokens, layout formulas, and component behaviors when constructing web applications to ensure desktop-native fidelity, 60fps interaction performance, and strict visual precision.

---

## 1. Visual & Spatial Foundations

### 1.1 Spatial Geometry & Grid

macOS interfaces rely on structured spatial hierarchy and high visual density without feeling cluttered. All layout boundaries, margins, and component dimensions must adhere to a strict 4px/8px spatial grid.

* **Base Unit**: `4px`
* **Standard Grid Scale**: `4px`, `8px`, `12px`, `16px`, `20px`, `24px`, `32px`, `48px`, `64px`
* **Desktop Shell Margins**: `16px` padding around floating window structures.
* **Window Inset Padding**: Content containers inside application windows use a uniform `16px` or `20px` internal padding.

### 1.2 Color Systems & Light/Dark Adaptation

Color tokens must dynamic-shift based on system appearance (`prefers-color-scheme` or manually toggled `.dark` class). Avoid pure blacks (`#000000`) and pure whites (`#ffffff`) for backgrounds to prevent visual harshness.

```css
:root {
  /* Light Mode Palette */
  --macos-window-bg: rgba(246, 246, 246, 0.75);
  --macos-sidebar-bg: rgba(235, 235, 240, 0.60);
  --macos-content-bg: #ffffff;
  --macos-border: rgba(0, 0, 0, 0.12);
  --macos-border-subtle: rgba(0, 0, 0, 0.06);
  
  --macos-text-primary: rgba(0, 0, 0, 0.88);
  --macos-text-secondary: rgba(0, 0, 0, 0.50);
  --macos-text-tertiary: rgba(0, 0, 0, 0.30);
  
  --macos-accent: #007aff; /* System Blue */
  --macos-accent-hover: #0062cc;
  --macos-accent-active: #004fb3;

  --macos-window-shadow: 0 20px 60px rgba(0, 0, 0, 0.25), 0 0 0 0.5px var(--macos-border);
  --macos-dock-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
}

.dark {
  /* Dark Mode Palette */
  --macos-window-bg: rgba(40, 40, 40, 0.75);
  --macos-sidebar-bg: rgba(30, 30, 30, 0.60);
  --macos-content-bg: #1e1e1e;
  --macos-border: rgba(255, 255, 255, 0.15);
  --macos-border-subtle: rgba(255, 255, 255, 0.08);
  
  --macos-text-primary: rgba(255, 255, 255, 0.90);
  --macos-text-secondary: rgba(255, 255, 255, 0.55);
  --macos-text-tertiary: rgba(255, 255, 255, 0.35);

  --macos-accent: #0a84ff;
  --macos-accent-hover: #409cff;
  --macos-accent-active: #0066d6;

  --macos-window-shadow: 0 25px 65px rgba(0, 0, 0, 0.65), 0 0 0 0.5px var(--macos-border);
  --macos-dock-shadow: 0 15px 35px rgba(0, 0, 0, 0.75);
}

```

### 1.3 Material Vibrancy (Vibrancy & Backdrop Blur)

Vibrancy is the defining characteristic of macOS UI elements (Window Sidebars, Menu Bars, Dock, and Toolbars).

```css
/* Core Vibrancy Utility Class */
.macos-vibrancy {
  background-color: var(--macos-window-bg);
  backdrop-filter: blur(25px) saturate(190%);
  -webkit-backdrop-filter: blur(25px) saturate(190%);
}

.macos-vibrancy-sidebar {
  background-color: var(--macos-sidebar-bg);
  backdrop-filter: blur(30px) saturate(200%);
  -webkit-backdrop-filter: blur(30px) saturate(200%);
}

```

### 1.4 Typography Hierarchy

macOS UI relies on Apple's San Francisco (`-apple-system`) font stack with precise tracking, weight, and anti-aliasing configurations.

* **Font Family**: `-apple-system, BlinkMacSystemFont, "SF Pro Text", "SF Pro Display", "Helvetica Neue", sans-serif`
* **Font Smoothing**: Always enforce `-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;`

| Element | Size | Weight | Tracking (Letter Spacing) | Line Height |
| --- | --- | --- | --- | --- |
| **System Menu Bar** | `13px` | `500` (Medium) | `-0.1px` | `16px` |
| **Window Title** | `13px` | `600` (Semi-Bold) | `-0.1px` | `16px` |
| **Sidebar Heading** | `11px` | `700` (Bold) | `0.6px` (Uppercase) | `14px` |
| **Sidebar Item** | `13px` | `400` (Regular) | `0px` | `18px` |
| **Body Text** | `14px` | `400` (Regular) | `-0.15px` | `20px` |
| **Large Title** | `22px` | `700` (Bold) | `-0.3px` | `28px` |

---

## 2. Desktop Shell Architecture

A macOS web application layout consists of five core regions:

1. **Top System Menu Bar**
2. **Desktop Workspace**
3. **Application Windows**
4. **Dock**
5. **Contextual Overlay Layers (Control Center, Dropdown Menus)**

```
+-------------------------------------------------------------------+
|                        TOP SYSTEM MENU BAR                        |
+-------------------------------------------------------------------+
|                                                                   |
|   +-----------------------------------------------------------+   |
|   | APPLICATION WINDOW                                        |   |
|   | +-----------------+-------------------------------------+ |   |
|   | | WINDOW TOOLBAR  | CONTROLS & SEARCH                   | |   |
|   | +-----------------+-------------------------------------+ |   |
|   | | SIDEBAR         | MAIN CONTENT VIEW                   | |   |
|   | | (Vibrancy)      | (White/Dark Solid)                  | |   |
|   | |                 |                                     | |   |
|   | +-----------------+-------------------------------------+ |   |
|   +-----------------------------------------------------------+   |
|                                                                   |
|                       DOCK CONTAINER (Bottom)                     |
+-------------------------------------------------------------------+

```

---

## 3. Detailed Component Specifications

### 3.1 Top System Menu Bar

The system menu bar spans full screen at `height: 28px`, fixed at the top of the viewport with depth index `z-index: 9999`.

* **Height**: `28px`
* **Padding**: `0 12px`
* **Font Size**: `13px`
* **Left Group**: Apple Logo, Active App Name (Bold), App Menus (File, Edit, View, History, Window, Help)
* **Right Group**: Status Items (Battery, Wi-Fi, Search/Spotlight, Control Center, Clock)

```html
<header class="macos-menu-bar macos-vibrancy">
  <div class="menu-bar-left">
    <button class="menu-item apple-logo"><svg>...</svg></button>
    <button class="menu-item app-name">AppTitle</button>
    <button class="menu-item">File</button>
    <button class="menu-item">Edit</button>
    <button class="menu-item">View</button>
  </div>
  <div class="menu-bar-right">
    <div class="status-item"><span id="clock">Wed Sep 13 09:44 AM</span></div>
  </div>
</header>

```

```css
.macos-menu-bar {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  height: 28px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 8px;
  font-size: 13px;
  color: var(--macos-text-primary);
  z-index: 9999;
  border-bottom: 0.5px solid var(--macos-border-subtle);
  user-select: none;
}
.menu-item {
  background: transparent;
  border: none;
  padding: 2px 8px;
  border-radius: 4px;
  color: inherit;
  font-size: 13px;
}
.menu-item:hover {
  background: rgba(255, 255, 255, 0.2);
}
.dark .menu-item:hover {
  background: rgba(255, 255, 255, 0.1);
}

```

### 3.2 Window Structure & Controls (Traffic Lights)

Every application window must be encapsulated within a window container featuring top traffic light buttons, a unified header/toolbar, and optionally a sidebar-content split layout.

* **Corner Radius**: `12px` or `10px`
* **Border**: `0.5px solid var(--macos-border)`
* **Traffic Lights Dimensions**: `12px x 12px` circles with `8px` gap between them.
* **Close**: `#FF5F56` (Border: `#E0443E`)
* **Minimize**: `#FFBD2E` (Border: `#DEA123`)
* **Maximize/Zoom**: `#27C93F` (Border: `#1AAB29`)



```html
<div class="macos-window">
  <!-- Titlebar -->
  <div class="window-titlebar macos-vibrancy">
    <div class="traffic-lights">
      <button class="traffic-light close"></button>
      <button class="traffic-light minimize"></button>
      <button class="traffic-light maximize"></button>
    </div>
    <div class="window-title">App Title — Workspace</div>
  </div>
  
  <!-- Window Body -->
  <div class="window-body">
    <aside class="window-sidebar macos-vibrancy-sidebar">
      <!-- Navigation items -->
    </aside>
    <main class="window-content">
      <!-- Core Application UI -->
    </main>
  </div>
</div>

```

```css
.macos-window {
  width: 900px;
  height: 600px;
  border-radius: 12px;
  box-shadow: var(--macos-window-shadow);
  border: 0.5px solid var(--macos-border);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  background-color: var(--macos-content-bg);
}

.window-titlebar {
  height: 52px;
  display: flex;
  align-items: center;
  padding: 0 16px;
  position: relative;
  border-bottom: 0.5px solid var(--macos-border-subtle);
  user-select: none;
}

.traffic-lights {
  display: flex;
  gap: 8px;
  align-items: center;
}

.traffic-light {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  border: 0.5px solid rgba(0, 0, 0, 0.2);
  padding: 0;
  cursor: pointer;
}

.traffic-light.close { background-color: #ff5f56; border-color: #e0443e; }
.traffic-light.minimize { background-color: #ffbd2e; border-color: #dea123; }
.traffic-light.maximize { background-color: #27c93f; border-color: #1aab29; }

/* Show glyphs on hover over group */
.traffic-lights:hover .traffic-light.close::before { content: "×"; display: block; font-size: 10px; line-height: 10px; text-align: center; color: #4d0000; }

.window-title {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  font-size: 13px;
  font-weight: 600;
  color: var(--macos-text-primary);
}

.window-body {
  flex: 1;
  display: flex;
  overflow: hidden;
}

.window-sidebar {
  width: 220px;
  border-right: 0.5px solid var(--macos-border-subtle);
  padding: 12px 8px;
}

.window-content {
  flex: 1;
  background: var(--macos-content-bg);
  overflow-y: auto;
  padding: 20px;
}

```

### 3.3 The macOS Dock

The Dock floats fixed at the bottom center of the screen with strong vibrancy, high rounded corners, and icon magnifications.

* **Bottom Margin**: `8px`
* **Padding**: `6px 8px`
* **Corner Radius**: `20px`
* **Icon Size**: `48px x 48px` standard desktop size.
* **Active Indicator**: `4px` dot centered under open app icons.

```html
<nav class="macos-dock-wrapper">
  <div class="macos-dock macos-vibrancy">
    <div class="dock-item active">
      <img src="finder-icon.png" alt="Finder" class="dock-icon" />
      <span class="dock-dot"></span>
    </div>
    <div class="dock-item">
      <img src="safari-icon.png" alt="Safari" class="dock-icon" />
      <span class="dock-dot"></span>
    </div>
    <div class="dock-separator"></div>
    <div class="dock-item">
      <img src="trash-icon.png" alt="Trash" class="dock-icon" />
    </div>
  </div>
</nav>

```

```css
.macos-dock-wrapper {
  position: fixed;
  bottom: 8px;
  left: 0;
  right: 0;
  display: flex;
  justify-content: center;
  z-index: 9999;
  pointer-events: none;
}

.macos-dock {
  pointer-events: auto;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px 10px;
  border-radius: 20px;
  border: 0.5px solid var(--macos-border);
  box-shadow: var(--macos-dock-shadow);
}

.dock-item {
  position: relative;
  width: 48px;
  height: 48px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: transform 0.2s cubic-bezier(0.25, 1, 0.5, 1);
}

.dock-item:hover {
  transform: scale(1.15) translateY(-6px);
}

.dock-icon {
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.dock-separator {
  width: 0.5px;
  height: 36px;
  background-color: var(--macos-border);
  margin: 0 4px;
}

.dock-dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background-color: var(--macos-text-primary);
  position: absolute;
  bottom: -4px;
}

```

---

## 4. UI Controls & Native Micro-Components

### 4.1 Segmented Controls & Buttons

#### Buttons

Primary buttons use system accent colors, while secondary buttons use subtle fill backgrounds.

```css
.macos-button {
  height: 22px;
  padding: 0 10px;
  border-radius: 5px;
  font-size: 13px;
  font-weight: 400;
  border: 0.5px solid var(--macos-border);
  background: var(--macos-content-bg);
  color: var(--macos-text-primary);
  box-shadow: 0 1px 1px rgba(0,0,0,0.05);
  cursor: pointer;
  outline: none;
}

.macos-button-primary {
  background: var(--macos-accent);
  color: #ffffff;
  border: none;
}
.macos-button-primary:hover {
  background: var(--macos-accent-hover);
}

```

#### Segmented Control

Used for switching sub-views within toolbars.

```css
.macos-segmented-control {
  display: inline-flex;
  background: rgba(0, 0, 0, 0.06);
  padding: 2px;
  border-radius: 6px;
}
.dark .macos-segmented-control {
  background: rgba(255, 255, 255, 0.1);
}

.segment-btn {
  padding: 3px 12px;
  font-size: 12px;
  border: none;
  background: transparent;
  color: var(--macos-text-secondary);
  border-radius: 4px;
  cursor: pointer;
}

.segment-btn.active {
  background: #ffffff;
  color: var(--macos-text-primary);
  box-shadow: 0 1px 3px rgba(0,0,0,0.12);
  font-weight: 500;
}
.dark .segment-btn.active {
  background: rgba(255, 255, 255, 0.25);
  color: #ffffff;
}

```

---

## 5. Interaction Mechanics & Motion Architecture

To feel like a native desktop operating system, motion timing must closely track Apple's Quartz/CoreAnimation curves.

### 5.1 Easing Curves & Timing Standards

* **Standard Motion (Opening windows, dialog popovers)**: `transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1)`
* **Subtle Hover Transitions**: `transition: background 0.15s ease-out, color 0.15s ease-out`
* **Dock Magnification Easing**: `cubic-bezier(0.25, 1, 0.5, 1)`

### 5.2 Window Dragging & Depth Stacking

1. **Window Focus Behavior**:
* Active window shadow: `0 20px 60px rgba(0,0,0,0.3)`
* Inactive window shadow: `0 5px 20px rgba(0,0,0,0.15)`
* Inactive window titlebar text opacity should drop to `0.4`.


2. **Dragging Mechanics**:
* Implement event listeners for `mousedown` on `.window-titlebar` to update `transform: translate3d(x, y, 0)`.
* Bring clicked window to top by dynamically incrementing its `z-index`.



---

## 6. Engineering Checklist for macOS Web UI

Before publishing or committing macOS-styled UI components, verify against this quality checklist:

* [ ] **Vibrancy Layer Check**: Does backdropping fail gracefully when translucent element moves over high-contrast content?
* [ ] **No Native Scrollbars**: Are browser scrollbars hidden or styled to match macOS thin overlay scrollbars? (`::-webkit-scrollbar { width: 6px; }`).
* [ ] **Text Selection Color**: Is selection background set to system accent tint? (`::selection { background: var(--macos-accent); color: white; }`).
* [ ] **User Select Prevention**: Are window headers, menu bars, buttons, and sidebars set to `user-select: none`?
* [ ] **Sub-pixel Borders**: Are borders set to `0.5px solid` where possible to match Retina Display crispness?
* [ ] **Dark Mode Sync**: Do translucent sidebars correctly shift saturation and backdrop blur values when switching to dark mode?
"""

with open("guide-ui.md", "w", encoding="utf-8") as f:
f.write(markdown_content)

```

Berkas **`guide-ui.md`** telah berhasil dibuat.

[file-tag: code-generated-file-46339706-0edb-4b8f-a46f-c913f378ba6f]

---

### Ringkasan Panduan Dalam Berkas:

1. **Visual & Spatial Foundations:**
   * **Grid System:** Skala spasial berbasis kelipatan 4px/8px.
   * **Color System:** Variabel CSS untuk *Light Mode* dan *Dark Mode* dengan standar kontras Apple.
   * **Vibrancy & Blur Effect:** Implementasi `backdrop-filter: blur(...) saturate(...)` untuk efek *frosted glass*.
   * **Typography:** Konfigurasi font-stack San Francisco (`-apple-system`) dengan *letter-spacing* & *anti-aliasing*.

2. **Desktop Shell Architecture:**
   * Pembagian struktur layout 5 area utama: Menu Bar, Desktop Workspace, Application Windows, Dock, dan Overlay Layers.

3. **Spesifikasi Komponen:**
   * **Top System Menu Bar:** Tinggi 28px, fixed position, *left/right layout group*.
   * **Window & Traffic Lights:** Radius corner window 12px, *shadow depth*, serta ukuran dan warna tombol *Traffic Lights* (Close, Minimize, Maximize).
   * **The macOS Dock:** Radius 20px, efek magnifikasi hover, dan indikator *dot* aplikasi aktif.
   * **UI Micro-Components:** *Segmented controls*, *primary/secondary buttons*, dan *tab switcher*.

4. **Mekanisme Interaksi & Animasi:**
   * *Easing curves* kustom (`cubic-bezier`) untuk animasi pembukaan window dan efek Dock.
   * Penanganan *active/inactive window focus*, *depth z-index*, dan drag state.

5. **Quality Checklist:**
   * Ceklis teknis meliputi *scrollbar styling*, *sub-pixel borders* (0.5px), dan pembatasan `user-select: none`.

```