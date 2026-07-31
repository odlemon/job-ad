# Admin Users API (Admin Management)

All admin user endpoints require **Bearer token** auth:  
`Authorization: Bearer <token>` (from `POST /api/admin/login`).

Only admins with `admin_role = "super_admin"` can manage admin users.

---

## 1. Admin Management Overview

Matches the **Admin Management** screen (summary cards, admin list, roles, recent activity).

**Endpoint:** `GET /api/admin/admin-users/overview`  
**Auth:** `Authorization: Bearer <token>`

**Query params (optional):**

| Param      | Type   | Description                                      |
|-----------|--------|--------------------------------------------------|
| `page`    | number | Page number (default: `1`)                       |
| `per_page`| number | Items per page (default: `10`, max: `100`)       |
| `search`  | string | Filter by name or email                          |
| `role`    | string | `super_admin` \| `content_manager` \| `support_agent` |
| `status`  | string | `all` \| `active` \| `inactive`                  |

**Response — 200 OK**

```json
{
  "summary": {
    "total_admins": { "value": 12, "change_percent": 5.3 },
    "super_admins": { "value": 3, "change_percent": 0.0 },
    "active_sessions": { "value": 8, "change_percent": 2.1 }
  },
  "filters": {
    "roles": ["super_admin", "content_manager", "support_agent"],
    "status": ["all", "active", "inactive"]
  },
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 12,
    "last_page": 2
  },
  "admins": [
    {
      "id": 1,
      "name": "Admin User",
      "email": "admin@jobs.com",
      "role": "super_admin",
      "avatar_initials": "AU",
      "last_login_at": "2026-03-02T10:03:00.000000Z",
      "status": "active"
    }
  ],
  "roles": [
    {
      "key": "super_admin",
      "label": "Super Admin",
      "description": "Full platform access and control",
      "admins_count": 3
    },
    {
      "key": "content_manager",
      "label": "Content Manager",
      "description": "Manages ads and moderation queues",
      "admins_count": 5
    },
    {
      "key": "support_agent",
      "label": "Support Agent",
      "description": "Handles user support tickets",
      "admins_count": 4
    }
  ],
  "recent_activity": [
    {
      "admin_name": "Admin User",
      "action": "admin_created",
      "description": "Created admin Sarah Manager (content_manager)",
      "created_at": "2026-03-02T09:55:00.000000Z"
    }
  ]
}
```

---

## 2. Create admin user

When a new admin is created, the backend **generates or accepts a password** and sends the credentials to their email using **ZeptoMail SMTP** (via Laravel mailer).

**Endpoint:** `POST /api/admin/admin-users`  
**Auth:** `Authorization: Bearer <token>` (super admin only)

**Body (JSON):**

| Field        | Type   | Required | Description |
|-------------|--------|----------|-------------|
| `name`      | string | yes      | Full name   |
| `email`     | string | yes      | Unique email |
| `password`  | string | no       | Min 6 chars. If omitted, a strong random password is generated. |
| `password_confirmation` | string | no | Must match `password` when `password` is provided |
| `role`      | string | yes      | `super_admin` \| `content_manager` \| `support_agent` |
| `phone`     | string | no       | Phone number |
| `is_active` | bool   | no       | Default `true` |

**Response — 201 Created**

```json
{
  "message": "Admin user created",
  "data": {
    "id": 4,
    "name": "Mike Support",
    "email": "mike@jobs.com",
    "role": "support_agent",
    "status": "active"
  }
}
```

After a successful create, the system sends an email to the new admin with:\n+- Login URL (e.g. `/admin`)\n+- Email address\n+- Temporary password (generated if not provided)\n+They are instructed to log in and change their password.

---

## 3. Update admin user

**Endpoint:** `PUT /api/admin/admin-users/{id}`  
**Auth:** `Authorization: Bearer <token>` (super admin only)

**Body (JSON, all fields optional):**

| Field        | Type   | Description |
|-------------|--------|-------------|
| `name`      | string | New name |
| `email`     | string | New unique email |
| `password`  | string | New password (if set, must have `password_confirmation`) |
| `password_confirmation` | string | Confirmation for `password` |
| `role`      | string | `super_admin` \| `content_manager` \| `support_agent` |
| `phone`     | string | Phone number |
| `is_active` | bool   | `true` or `false` |

**Response — 200 OK**

```json
{
  "message": "Admin user updated",
  "data": {
    "id": 4,
    "name": "Mike Support",
    "email": "mike@jobs.com",
    "role": "support_agent",
    "status": "active"
  }
}
```

If the update changes `is_active` from `true` to `false` or vice versa, the action is recorded in `recent_activity` for the overview endpoint.

---

## 4. Deactivate admin user

Instead of hard-deleting, the API **deactivates** admin accounts by setting `is_active = false`. This keeps history and activity logs intact.\n\n**Endpoint:** `DELETE /api/admin/admin-users/{id}`  \n**Auth:** `Authorization: Bearer <token>` (super admin only)\n\n**Response — 200 OK**\n\n```json\n{\n  \"message\": \"Admin user deactivated\"\n}\n```\n\n**Notes:**\n- A super admin **cannot deactivate themselves** (the API returns 422).\n- Deactivating also writes an entry to `recent_activity`.\n\n---\n\n## 5. Summary of Admin Users endpoints\n\n| Method | Endpoint                          | Description                          |\n|--------|-----------------------------------|--------------------------------------|\n| GET    | `/api/admin/admin-users/overview` | Full Admin Management overview (KPIs, list, roles, activity) |\n| POST   | `/api/admin/admin-users`         | Create a new admin user              |\n| PUT    | `/api/admin/admin-users/{id}`    | Update an admin user                 |\n| DELETE | `/api/admin/admin-users/{id}`    | Deactivate an admin user             |\n\nAll responses are JSON and include an error body with `message` and optional `errors` when validation fails or access is forbidden.\n+
