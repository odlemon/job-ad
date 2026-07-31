# Admin Notifications API

All admin notification endpoints require **Bearer token** authentication:  
`Authorization: Bearer <token>`  
(Base: `POST /api/admin/login`)

---

## 1. Notification management (dashboard)

These endpoints power the **Notifications Management** screen: KPIs, list with filters, create/edit/duplicate/delete/send campaigns.

### 1.1 List notifications (with KPIs)

**Endpoint:** `GET /api/admin/notifications`

**Query parameters (optional):**

| Parameter   | Type   | Description                                  |
|------------|--------|----------------------------------------------|
| `page`     | number | Page number (default: 1)                     |
| `per_page` | number | Items per page (default: 15, max: 100)       |
| `search`   | string | Search in title and reference_id            |
| `status`   | string | `draft` \| `scheduled` \| `sent`            |
| `method`   | string | `email` \| `in_app`                         |
| `audience` | string | `all_employers` \| `all_job_seekers` \| `all` |

**Response — 200 OK**

```json
{
  "summary": {
    "total_sent": { "value": 1234, "change_percent": 19.3 },
    "scheduled_next_7_days": { "value": 45, "change_percent": 0 },
    "delivery_rate": { "value": 98.7, "change_percent": 2.1 },
    "open_rate": { "value": 67.8, "change_percent": 5.4 }
  },
  "filters": {
    "status": ["draft", "scheduled", "sent"],
    "method": ["email", "in_app"],
    "audience": ["all_employers", "all_job_seekers", "all"]
  },
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 50,
    "last_page": 4
  },
  "notifications": [
    {
      "id": 1,
      "reference_id": "NOT-2026-001",
      "title": "New Feature Announcement",
      "message": "We have launched...",
      "method": "email",
      "audience": "all_employers",
      "category": "update",
      "status": "sent",
      "scheduled_at": null,
      "sent_at": "2026-01-15T12:00:00.000000Z",
      "created_at": "2026-01-15T11:00:00.000000Z",
      "created_by": 1
    }
  ]
}
```

---

### 1.2 Create notification (draft)

**Endpoint:** `POST /api/admin/notifications`

**Body (JSON):**

| Field          | Type   | Required | Description                                      |
|----------------|--------|----------|--------------------------------------------------|
| `title`        | string | yes      | Notification title                               |
| `message`      | string | no       | Body text                                        |
| `method`       | string | no       | `email` \| `in_app` (default: `email`)         |
| `audience`     | string | no       | `all_employers` \| `all_job_seekers` \| `all` (default: `all_employers`) |
| `category`     | string | no       | `update` \| `alert` \| `promotion`             |
| `status`       | string | no       | `draft` \| `scheduled` (default: `draft`)      |
| `scheduled_at` | string | no       | ISO 8601 datetime (for future send)              |

**Response — 201 Created**

```json
{
  "message": "Notification created",
  "data": {
    "id": 2,
    "reference_id": "NOT-2026-002",
    "title": "Scheduled Maintenance",
    "message": "...",
    "method": "email",
    "audience": "all_employers",
    "category": "alert",
    "status": "draft",
    "scheduled_at": null,
    "created_at": "2026-03-02T14:00:00.000000Z"
  }
}
```

---

### 1.3 Get one notification

**Endpoint:** `GET /api/admin/notifications/{id}`

**Response — 200 OK**

```json
{
  "data": {
    "id": 1,
    "reference_id": "NOT-2026-001",
    "title": "New Feature Announcement",
    "message": "...",
    "method": "email",
    "audience": "all_employers",
    "category": "update",
    "status": "sent",
    "scheduled_at": null,
    "sent_at": "2026-01-15T12:00:00.000000Z",
    "created_at": "2026-01-15T11:00:00.000000Z",
    "created_by": 1
  }
}
```

**Response — 404**  
`{ "message": "Notification not found" }`

---

### 1.4 Update notification

**Endpoint:** `PUT /api/admin/notifications/{id}`

Only **draft** or **scheduled** notifications can be updated. Same body fields as create (all optional).

**Response — 200 OK**  
`{ "message": "Notification updated", "data": { ... } }`

**Response — 422**  
`{ "message": "Cannot edit a sent notification" }`

---

### 1.5 Delete notification

**Endpoint:** `DELETE /api/admin/notifications/{id}`

Only **draft** or **scheduled** notifications can be deleted.

**Response — 200 OK**  
`{ "message": "Notification deleted" }`

**Response — 422**  
`{ "message": "Cannot delete a sent notification" }`

---

### 1.6 Duplicate notification

**Endpoint:** `POST /api/admin/notifications/{id}/duplicate`

Creates a new **draft** with the same title, message, method, audience, and category. New `reference_id` is generated.

**Response — 201 Created**

```json
{
  "message": "Notification duplicated",
  "data": {
    "id": 3,
    "reference_id": "NOT-2026-003",
    "title": "Scheduled Maintenance",
    "status": "draft",
    "created_at": "2026-03-02T14:05:00.000000Z"
  }
}
```

---

### 1.7 Send notification now

**Endpoint:** `POST /api/admin/notifications/{id}/send`

Sends the campaign **now**: creates in-app notifications for every user in the selected audience. Status is set to `sent` and `sent_at` is set.

**Response — 200 OK**

```json
{
  "message": "Notification sent",
  "recipients_count": 342,
  "data": {
    "id": 1,
    "reference_id": "NOT-2026-001",
    "status": "sent",
    "sent_at": "2026-03-02T14:10:00.000000Z"
  }
}
```

**Response — 422**  
`{ "message": "Notification already sent" }`

---

## 2. Admin inbox (navbar icon)

These endpoints are for the **logged-in admin’s in-app notifications** (navbar bell): list, unread count, mark read.

### 2.1 List inbox notifications

**Endpoint:** `GET /api/admin/inbox`

**Query parameters (optional):**

| Parameter     | Type    | Description                          |
|---------------|---------|--------------------------------------|
| `limit`       | number  | Max items (default: 20, max: 50)     |
| `unread_only` | boolean | If true, return only unread         |

**Response — 200 OK**

```json
{
  "notifications": [
    {
      "id": 101,
      "user_id": 1,
      "type": "employer_registered",
      "title": "New Employer Registered",
      "message": "Jane Doe (jane@company.com) just registered as an employer.",
      "data": { "user_id": 5, "email": "jane@company.com" },
      "is_read": false,
      "read_at": null,
      "created_at": "2026-03-02T10:00:00.000000Z"
    }
  ],
  "unread_count": 3
}
```

---

### 2.2 Unread count (navbar badge)

**Endpoint:** `GET /api/admin/inbox/unread-count`

**Response — 200 OK**

```json
{
  "unread_count": 3
}
```

---

### 2.3 Mark one as read

**Endpoint:** `PUT /api/admin/inbox/{id}/read`

**Response — 200 OK**

```json
{
  "message": "Notification marked as read",
  "unread_count": 2
}
```

**Response — 404**  
`{ "message": "Notification not found" }`

---

### 2.4 Mark all as read

**Endpoint:** `PUT /api/admin/inbox/mark-all-read`

**Response — 200 OK**

```json
{
  "message": "All notifications marked as read",
  "marked_count": 3
}
```

---

## 3. Events that create admin notifications

The system **automatically** creates in-app notifications for **all admin users** when these events occur (no extra API call needed):

| Event                    | Notification `type`           | When it runs |
|-------------------------|-------------------------------|--------------|
| New job seeker registers| `job_seeker_registered`       | After `POST /api/auth/register` with `user_type: job_seeker` |
| New employer registers  | `employer_registered`         | After `POST /api/auth/register` with `user_type: employer`  |
| New job post created    | `new_job_post`                | After an employer creates a job (e.g. employer job store)   |
| Someone is hired        | `application_hired`           | After an employer sets an application status to `hired`     |

Admins see these in their **inbox** (navbar) via `GET /api/admin/inbox` and `GET /api/admin/inbox/unread-count`.

---

## 4. Summary of admin notification endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET    | `/api/admin/notifications`       | List campaigns + KPIs, with filters and pagination |
| POST   | `/api/admin/notifications`       | Create draft campaign |
| GET    | `/api/admin/notifications/{id}`  | Get one campaign |
| PUT    | `/api/admin/notifications/{id}`  | Update draft/scheduled campaign |
| DELETE | `/api/admin/notifications/{id}`  | Delete draft/scheduled campaign |
| POST   | `/api/admin/notifications/{id}/duplicate` | Duplicate as draft |
| POST   | `/api/admin/notifications/{id}/send`      | Send now (in-app to audience) |
| GET    | `/api/admin/inbox`               | List admin’s in-app notifications (navbar) |
| GET    | `/api/admin/inbox/unread-count`  | Unread count for navbar badge |
| PUT    | `/api/admin/inbox/mark-all-read` | Mark all admin notifications as read |
| PUT    | `/api/admin/inbox/{id}/read`     | Mark one notification as read |

All require: **`Authorization: Bearer <admin_token>`**
