# BlackBOX Clipboard — Shared/Distributed Clipboard Specification

## 1. Project Overview
A web-based **data transformation pipeline** called "Shared/Distributed Clipboard". Users input data (Copy/Upload) into predefined channels (Clipboards). The system validates, stores, and optionally transforms data before delivering it to other users in real time (Paste/Download/Execute) based on strict rules.

## 2. Tech Stack & Restrictions
- **Backend:** Pure PHP 8+ (OOP where sensible, NO frameworks like Laravel/Symfony).
- **Frontend:** HTML5, CSS3, Vanilla JS (ES6+) — NO React, Vue, Angular, jQuery.
- **Database:** MySQL/MariaDB via PDO (prepared statements only).
- **Architecture:** Light MVC or component-based — `public/` as entry point, `includes/` or `src/` for config, classes, and routing.
- **Dependencies:** NO external libraries. No Composer, no NPM, no Tailwind/Bootstrap (unless standard CSS resets).

## 3. Domain Language
- **Clipboard:** Container (channel/room) with access rules, input/output format rules, and capacity (single-item vs. history).
- **Copy (Input):** Adding a resource to a Clipboard (paste text or upload file).
- **Paste (Output):** Consuming a resource from a Clipboard.
- **ExportAs:** The transformation rule applied to input data on Paste (e.g., raw text URL → clickable HTML `<a>` link).

## 4. User Roles & UI Constraints
1. **Admin:** Defines clipboards, sets MIME rules & `ExportAs` transforms. 
   - *CRITICAL UI CONSTRAINT:* Do not build HTML forms for Admin clipboard CRUD operations. Instead, implement a global **Export JSON** button (downloads current DB clipboard configs) and an **Import JSON** button (uploads file, parses it, and overwrites the DB config).
2. **Registered User:** Accesses authorized clipboards. Can Copy/Paste, subscribe, and view a History dashboard of their actions.
3. **Guest:** Read-only (or Read/Write, depending on config) access to public clipboards only.

## 5. Data Mapping (Copy → ExportAs → Paste)
The core logic resides in how data changes between input and output.

| Input (MIME / Type) | Storage Handling | `ExportAs` Options (Output) |
|---|---|---|
| `text/plain` (Link) | Stored as text | `Link` (generates HTML `<a href="...">`), `Text` |
| `text/php` | Stored as text or `.php` file | `Code Preview` (syntax highlighted text), `Re-share` |
| `text/js` | Stored as text | `Execute` (evaluates/runs in recipient's browser) |
| `application/zip` (with PHP) | Unarchived / Stored as file | `Code preview`, `Download` |
| `image/jpeg`, `image/gif` | Saved to server storage | `<img>` tag render, `Download link` |

## 6. Expiration (Resource TTL) & Garbage Collection
- **Time-to-Live (TTL):** Senders dictate expiration on upload: 30 mins / 60 mins / 8 hours / One-time view.
- **One-time view:** Record and file are permanently deleted immediately after the first "Paste" (consumption).
- **Garbage Collection (GC):** No cron jobs available. Implement a pseudo-cron GC mechanism that triggers on `index.php` load or via a lightweight background async call to purge expired DB rows and unlink files.

## 7. Notifications & Live Updates
- **Live Updates:** Use Vanilla JS `setInterval` (short polling) querying a PHP JSON endpoint containing `last_timestamp` to fetch and render new clipboard items without page reload.
- **System/Email Notifications:** When a user subscribes to a clipboard, notify them of new copies via a UI Dashboard marker (e.g., "3 new items") OR via PHP's native `mail()` function.

## 8. External API Scripts (CLI/Browser Simulation)
Build 3 standalone, isolated PHP scripts to prove the pipeline works without the main web UI. Use `cURL` or `file_get_contents` with stream context.
1. `api_create_clipboard.php` — POSTs a JSON payload to the system to generate a new clipboard.
2. `api_external_copy.php` — POSTs text/file data to a specific `clipboard_id`.
3. `api_external_paste.php` — GETs/polls the latest content from a `clipboard_id` and prints the formatted `ExportAs` result to the console/screen.

## 9. Database Schema (Suggested)
- `users` — id, username, email, password_hash, role
- `clipboards` — id, name, is_public, capacity_type, input_types (JSON), export_types (JSON)
- `clipboard_access` — user_id, clipboard_id
- `shares` — id, clipboard_id, user_id, content_type, content_path, created_at, expires_at, is_consumed, view_count
- `subscriptions` — user_id, clipboard_id
- `history` — user_id, action_type (enum: copy, paste), share_id, timestamp

## 10. Critical Agent Instructions
- **Security First:** Uploaded files (`.php`, `.zip`, images) MUST be stored outside the public web root or protected via `.htaccess` (`Deny from all`). Never execute user-uploaded PHP files directly on the server. Always use `htmlspecialchars()` on text output to prevent XSS.
- **ExportAs Architecture:** The logic formatting the output (e.g., a `ClipboardFormatter` class) is the core of this project. Keep it modular (Strategy pattern or similar) so new MIME types and transformation rules can be added easily.
- **Code Quality:** Keep comments minimal but explain complex backend logic. Output semantic, accessible HTML5.