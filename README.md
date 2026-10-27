# Digitest Debug System Plugin for Joomla

**DigitestDebug** is a lightweight, high-performance system plugin designed for Joomla 4, 5, and 6. It provides real-time frontend debugging parameters, environment metrics, performance analytics, and MIME-type resource tracking specifically tailored for custom framework-free template developers.

---

## Features

- **CMS & Request Diagnostics:** Instantly inspect current Joomla route parameters (`option`, `view`, `layout`, `page`, `task`, `Itemid`, category IDs, and article mapping).
- **Environment & User Agent Detection:** Displays accurate OS platform identification, browser breakdown, and mobile/desktop status without relying on heavy external libraries like Mobile_Detect.
- **Performance Monitoring:** Tracks full CMS page execution time (`microtime`), active database query counts, and peak RAM consumption against server limits.
- **Template & Extension Inspection:** Automatically reads custom template parameters and checks the active/installed status of common third-party extensions and plugins.
- **MIME-Type & Missing Asset Tracker (New in v3.1.1):** An optional toggleable feature that catches resource loading failures (e.g., missing CSS/JS scripts returning `text/html` instead of asset types) and outputs structured error logs directly into the browser console.
- **Targeted Visibility:** Restricts the debug panel output strictly to authorized administrator user IDs defined in the plugin configuration, keeping it completely hidden from standard public visitors.

---

## Requirements

- Joomla! 4.x, 5.x, or 6.x
- PHP 8.0 or higher

---

## Installation

1. Download the latest installation package zip (`plg_system_digitestdebug_x.x.x.zip`) from the [Releases page](../../releases).
2. Go to your Joomla Administrator Panel -> **System** -> **Install** -> **Extensions**.
3. Upload and install the package.
4. Navigate to **System** -> **Plugins**, search for **System - Digitest Debug**, and enable it.

---

## Configuration

Configure the plugin parameters via the Joomla Plugin Manager:

- **Authorized User IDs:** Enter a comma-separated list of backend user IDs allowed to view the debug overlay.
- **Check Missing Assets (MIME Debug):** Toggle `Yes/No` to enable the client-side JavaScript listener that catches blocked or missing stylesheets/scripts in the browser console.
- **Theme & Styling Options:** Choose between light and dark display themes and customize text/background colors to match your workflow.

---

## Author

Developed by **Digitest.net**  
Professional Web Development & Custom Joomla Solutions (Italy & Germany).
