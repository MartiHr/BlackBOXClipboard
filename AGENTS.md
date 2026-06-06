# BlackBOX Clipboard — Distributed Clipboard & Real-Time Sharing System (v2)

---

## 1. Project Overview

A web-based **distributed clipboard system** where users publish and consume structured resources (text, links, code, files) inside configurable containers called **Clipboards**.

Each Clipboard behaves as:

* a data channel (publish/subscribe)
* a storage container (single-state or history)
* optionally a real-time feed (polling-based updates)

Users can:

* Copy (publish) data into a Clipboard
* Paste (consume/render) data from a Clipboard
* Subscribe to Clipboards for notifications (email / UI / realtime)

---

## 2. Core Architecture Model

```text
User Action → Clipboard Item → Event → Notification → ExportAs Rendering
```

---

## 3. Tech Stack & Restrictions

* Backend: Pure PHP 8+ (NO frameworks)
* Frontend: Vanilla JS (ES6+), HTML5, CSS3
* Database: MySQL via PDO (prepared statements only)
* Architecture: Lightweight MVC or modular structure
* No external dependencies (no Composer, no npm)

---

## 4. Domain Language

* Clipboard: Logical container (channel)
* Item: Single unit of published data
* Copy: Create item (publish)
* Paste: Read/render item (consume)
* ExportAs: Output transformation layer
* Subscription: User follows clipboard updates
* Event: System state change (item_created, item_consumed)

---

## 5. User Roles

### Admin

* Creates clipboards
* Defines allowed types & formats
* Defines ExportAs rules
* Imports/exports configuration via JSON only

### Registered User

* Publish items (Copy)
* Consume items (Paste)
* Subscribe to clipboards
* View history

### Guest

* Read-only access depending on clipboard rules

---

## 6. Database Schema

---

### users

```sql
users (
  id BIGINT PRIMARY KEY,
  username VARCHAR,
  email VARCHAR UNIQUE,
  password_hash VARCHAR,
  role ENUM('admin','user'),
  created_at TIMESTAMP
)
```

---

### clipboards

```sql
clipboards (
  id BIGINT PRIMARY KEY,
  name VARCHAR,
  slug VARCHAR UNIQUE,
  description TEXT,
  owner_id BIGINT,
  visibility ENUM('public','private'),
  mode ENUM('single','multi'),
  allow_guest BOOLEAN DEFAULT false,
  created_at TIMESTAMP
)
```

---

### clipboard_rules

```sql
clipboard_rules (
  id BIGINT PRIMARY KEY,
  clipboard_id BIGINT,
  capacity_type ENUM('single','multi'),
  allowed_mime JSON,
  allowed_formats JSON,
  export_rules JSON,
  ttl_default INT NULL
)
```

---

### clipboard_items

```sql
clipboard_items (
  id BIGINT PRIMARY KEY,
  clipboard_id BIGINT,
  user_id BIGINT,
  type ENUM('text','link','code','file','image'),
  mime_type VARCHAR,
  format VARCHAR,
  content LONGTEXT NULL,
  file_path TEXT NULL,
  view_count INT DEFAULT 0,
  is_consumed BOOLEAN DEFAULT false,
  created_at TIMESTAMP
)
```

---

### item_expiration

```sql
item_expiration (
  id BIGINT PRIMARY KEY,
  item_id BIGINT,
  expires_at DATETIME NULL,
  max_views INT NULL,
  one_time BOOLEAN DEFAULT false
)
```

---

### subscriptions

```sql
subscriptions (
  id BIGINT PRIMARY KEY,
  user_id BIGINT,
  clipboard_id BIGINT,
  notify_email BOOLEAN DEFAULT true,
  notify_inapp BOOLEAN DEFAULT true,
  notify_realtime BOOLEAN DEFAULT false,
  last_seen_item_id BIGINT NULL,
  created_at TIMESTAMP
)
```

---

### clipboard_events

```sql
clipboard_events (
  id BIGINT PRIMARY KEY,
  clipboard_id BIGINT,
  item_id BIGINT NULL,
  event_type ENUM('item_created','item_viewed','item_consumed','clipboard_updated'),
  payload JSON,
  created_at TIMESTAMP
)
```

---

### history

```sql
history (
  id BIGINT PRIMARY KEY,
  user_id BIGINT,
  item_id BIGINT,
  action ENUM('copy','paste'),
  created_at TIMESTAMP
)
```

---

## 7. ExportAs System (Core Engine)

### Concept

```text
Stored Item → ExportAs Strategy → Rendered Output
```

---

### Supported transformations

| Type  | Output                        |
| ----- | ----------------------------- |
| text  | raw / escaped                 |
| link  | HTML anchor                   |
| code  | formatted preview             |
| js    | browser execution (sandboxed) |
| image | `<img>`                       |
| file  | download link                 |

---

### PHP Interface

```php
interface ExportStrategy {
    public function render($item);
}
```

---

### Example

```php
class LinkExport implements ExportStrategy {
    public function render($item) {
        return '<a href="'.$item->content.'">'.$item->content.'</a>';
    }
}
```

---

## 8. TTL & Garbage Collection

No cron jobs allowed.

Cleanup triggers:

* index.php load
* API request
* clipboard access

Logic:

* remove expired items
* remove one-time consumed items
* delete orphaned files

---

## 9. Live Updates System

No WebSockets.

Polling approach:

```text
GET /api/clipboard/updates?last_id=123
```

JS:

```javascript
setInterval(() => {
  fetch('/updates');
}, 3000);
```

---

## 10. External API Simulation Scripts

### 1. create clipboard

`api_create_clipboard.php`

* POST JSON
* returns clipboard_id

---

### 2. external copy

`api_external_copy.php`

* POST:

  * clipboard_id
  * content
  * type/mime

---

### 3. external paste

`api_external_paste.php`

* GET clipboard_id
* returns latest item + ExportAs output

---

## 11. Security Rules

* No execution of uploaded PHP files
* Escape all output with `htmlspecialchars()`
* Store uploads outside public directory
* Protect uploads via `.htaccess`
* Validate MIME types before saving

---

## 12. Key Design Principles

* Clipboards are immutable containers
* Items are append-only records
* ExportAs is pluggable strategy system
* System is event-driven, not request-driven
* Polling replaces real-time sockets

---

## 13. Final Definition

A PHP-based distributed clipboard system where users publish items into configurable clipboards, and those items are transformed and delivered via ExportAs rules through polling-based real-time updates and event-driven notifications.
