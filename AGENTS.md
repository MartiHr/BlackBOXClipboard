# BlackBOX Clipboard — Distributed Clipboard & Real-Time Sharing System

## 1. Project Overview
A web-based **distributed data transformation pipeline** where users publish and consume structured resources (text, links, code, files) inside configurable containers called **Clipboards**. 

Each Clipboard behaves as:
* A data channel (publish/subscribe).
* A storage container (single-state or history).
* A real-time feed (polling-based updates).

Users input data (Copy/Upload) into predefined channels. The system validates, stores, and optionally transforms data before delivering it to other users in real time (Paste/Download/Execute) based on strict rules.

---

## 2. Tech Stack & Restrictions
- **Backend:** Pure PHP 8+ (OOP, NO frameworks like Laravel/Symfony).
- **Frontend:** HTML5, CSS3, Vanilla JS (ES6+) — NO React, Vue, Angular, jQuery.
- **Database:** MySQL / MariaDB via PDO (prepared statements only).
- **Architecture:** Lightweight MVC or modular component structure.
- **Dependencies:** NO external libraries (No Composer, no NPM, no Tailwind/Bootstrap).

---

## 3. Domain Language & Architecture Model
**Model Flow:** `User Action → Clipboard Item → Event → Notification → ExportAs Rendering`

* **Clipboard:** Logical container (channel/room) with access and formatting rules.
* **Item:** Single unit of published data.
* **Copy (Publish):** Adding a resource to a Clipboard.
* **Paste (Consume):** Reading/rendering an item from a Clipboard.
* **ExportAs:** Output transformation strategy applied to data on Paste.
* **Event:** System state change (e.g., `item_created`, `item_consumed`).

---

## 4. User Roles & UI Constraints

### Admin
* Creates clipboards and defines allowed types/formats.
* **CRITICAL UI CONSTRAINT:** Do not build HTML forms for Admin clipboard CRUD operations. Instead, implement a global **Export JSON** button (downloads current DB clipboard configs) and an **Import JSON** button (uploads file, parses it, and overwrites the DB config).

### Registered User
* Publish (Copy) and consume (Paste) items in authorized clipboards.
* Subscribe to clipboards for notifications.
* View a History dashboard of their actions.

### Guest
* Read-only (or Read/Write, depending on config) access to public clipboards only.

---

## 5. Database Schema (Prioritized Structure)

### `users`
```sql
CREATE TABLE users (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','user') DEFAULT 'user',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### `clipboards`
```sql
CREATE TABLE clipboards (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE NOT NULL,
  description TEXT,
  owner_id BIGINT,
  visibility ENUM('public','private') DEFAULT 'public',
  allow_guest BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### `clipboard_rules`
```sql
CREATE TABLE clipboard_rules (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  clipboard_id BIGINT NOT NULL,
  capacity_type ENUM('single','multi') DEFAULT 'multi',
  allowed_mime JSON,
  allowed_formats JSON,
  export_rules JSON,
  ttl_default INT NULL,
  FOREIGN KEY (clipboard_id) REFERENCES clipboards(id) ON DELETE CASCADE
);
```

### `clipboard_items` (The Data)
```sql
CREATE TABLE clipboard_items (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  clipboard_id BIGINT NOT NULL,
  user_id BIGINT NULL,
  type ENUM('text','link','code','file','image') NOT NULL,
  mime_type VARCHAR(100),
  format VARCHAR(50),
  content LONGTEXT NULL,
  file_path TEXT NULL,
  view_count INT DEFAULT 0,
  is_consumed BOOLEAN DEFAULT false,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (clipboard_id) REFERENCES clipboards(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
```

### `item_expiration`
```sql
CREATE TABLE item_expiration (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  item_id BIGINT NOT NULL,
  expires_at DATETIME NULL,
  max_views INT NULL,
  one_time BOOLEAN DEFAULT false,
  FOREIGN KEY (item_id) REFERENCES clipboard_items(id) ON DELETE CASCADE
);
```

### `subscriptions`
```sql
CREATE TABLE subscriptions (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT NOT NULL,
  clipboard_id BIGINT NOT NULL,
  notify_email BOOLEAN DEFAULT true,
  notify_inapp BOOLEAN DEFAULT true,
  notify_realtime BOOLEAN DEFAULT false,
  last_seen_item_id BIGINT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (clipboard_id) REFERENCES clipboards(id) ON DELETE CASCADE
);
```

### `history` & `clipboard_events`
```sql
CREATE TABLE history (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT NOT NULL,
  item_id BIGINT NOT NULL,
  action ENUM('copy','paste') NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE clipboard_events (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  clipboard_id BIGINT NOT NULL,
  item_id BIGINT NULL,
  event_type ENUM('item_created','item_viewed','item_consumed','clipboard_updated'),
  payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 6. ExportAs System (Core Engine)
The core logic resides in how data changes between input and output using the Strategy pattern:
`Stored Item → ExportAs Strategy → Rendered Output`

| Input Type | Output Handling (`ExportAs`) |
| :--- | :--- |
| `text` | Raw / Escaped text |
| `link` | HTML anchor tag (`<a href="...">`) |
| `code` | Formatted preview (Syntax highlighted) |
| `js` | Browser execution (evaluates in recipient's browser) |
| `image` | Rendered `<img>` tag |
| `file` | Download link |

### PHP Implementation Example
```php
interface ExportStrategy {
    public function render($item);
}

class LinkExport implements ExportStrategy {
    public function render($item) {
        return '<a href="' . htmlspecialchars($item->content) . '">Link</a>';
    }
}
```

---

## 7. TTL & Garbage Collection
**No Cron Jobs Allowed.**
Implement a pseudo-cron GC mechanism. Cleanup triggers on `index.php` load, API requests, or clipboard access.
Logic must:
1. Remove expired items (`item_expiration.expires_at`).
2. Remove one-time consumed items (`is_consumed = true`).
3. Delete orphaned files from the server via `unlink()`.

---

## 8. Real-Time Updates & Notifications
* **Live Feed (No WebSockets):** Use Vanilla JS `setInterval` (short polling every ~3000ms) to fetch `/api/clipboard/updates?last_id=123`.
* **System/Email Notifications:** For subscribed users, trigger PHP's native `mail()` function or update the UI Dashboard marker when a new item is published to their followed clipboard.

---

## 9. External API Simulation Scripts (CLI/Browser)
Build 3 standalone PHP scripts to prove the pipeline works outside the main web UI. Use `cURL` or `file_get_contents` with stream context.
1. `api_create_clipboard.php`: POSTs a JSON payload to create a new clipboard and returns the `clipboard_id`.
2. `api_external_copy.php`: POSTs text/file data (`clipboard_id`, `content`, `type/mime`) to publish an item.
3. `api_external_paste.php`: GETs/polls the latest item from a `clipboard_id` and prints the formatted `ExportAs` result to the console/screen.

---

## 10. Security & Key Design Principles
* **Security First:** Uploaded files MUST be stored outside the public web root or protected via `.htaccess` (`Deny from all`). Never execute user-uploaded PHP files directly on the server.
* **XSS Prevention:** Always escape output with `htmlspecialchars()`.
* **Immutability:** Clipboards are immutable containers. Items are append-only records.
* **Pluggable:** ExportAs must be a pluggable strategy system.
* **Event-Driven:** The system is event-driven (using the `clipboard_events` table), not just request-driven.