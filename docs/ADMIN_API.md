# Admin API

Backend base URL (development): `http://localhost:8000`  
API prefix: `/api`

All admin endpoints are under: **`/api/admin`**

---

## Base URL

- **Local:** `http://localhost:8000`
- **Production:** Set to your deployed backend URL (e.g. `https://api.yourdomain.com`)

---

## Admin Login

**Endpoint:** `POST /api/admin/login`

**Request**

- **Headers:** `Content-Type: application/json`
- **Body (JSON):**

```json
{
  "email": "admin@jobs.com",
  "password": "admin123"
}
```

**Response — 200 OK**

```json
{
  "message": "Login successful",
  "token": "1|abc123...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@jobs.com",
    "user_type": "admin"
  }
}
```

**Response — 401 Unauthorized (invalid credentials)**

```json
{
  "message": "Invalid credentials"
}
```

**Response — 403 Forbidden (not admin or inactive)**

```json
{
  "message": "Unauthorized. Admin access only."
}
```

or

```json
{
  "message": "Account is inactive."
}
```

**Response — 422 Unvalidation Failed**

```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

---

## Using the token

For all other admin endpoints, send the token in the header:

```
Authorization: Bearer <token>
```

---

## Admin Me

**Endpoint:** `GET /api/admin/me`  
**Auth:** `Authorization: Bearer <token>`

**Response — 200 OK**

```json
{
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@jobs.com",
    "user_type": "admin"
  }
}
```

---

## Admin Logout

**Endpoint:** `POST /api/admin/logout`  
**Auth:** `Authorization: Bearer <token>`

**Response — 200 OK**

```json
{
  "message": "Logout successful"
}
```

---

## Admin Dashboard (single endpoint)

**Endpoint:** `GET /api/admin/dashboard`  
**Auth:** `Authorization: Bearer <token>`

Returns all data for the admin dashboard in one response: KPIs (with % change), daily job applications (last 14 days), active categories, user flow (views/clicks/applications), recent signups, recent payments (stub), and ads about to expire.

**Response — 200 OK:** See [ADMIN_DASHBOARD_RESPONSE.md](ADMIN_DASHBOARD_RESPONSE.md) for the full JSON structure and field reference.

---

## Job Seekers Management Overview

**Endpoint:** `GET /api/admin/job-seekers/overview`  
**Auth:** `Authorization: Bearer <token>`

Returns the data needed for the **Job Seekers Management** screen: summary KPI cards (total job seekers, active users, pending verification, suspended/banned), a paginated list of job seekers with contact and activity info, plus quick actions and recent activity.

- **Query params (optional):**
  - `page` — page number (default: `1`)
  - `per_page` — items per page (default: `10`, max: `100`)
  - `search` — filter by name, email, or phone
  - `status` — `all` \| `active` \| `suspended`

**Response — 200 OK:** See the **"Job Seekers Management — Response"** section in [ADMIN_DASHBOARD_RESPONSE.md](ADMIN_DASHBOARD_RESPONSE.md).

---

## Employers Management Overview

**Endpoint:** `GET /api/admin/employers/overview`  
**Auth:** `Authorization: Bearer <token>`

Returns the data needed for the **Employers Management** screen: summary KPI cards (total employers, active companies, pending verification, suspended/banned), a paginated list of employers/companies with contact and job posting stats, plus quick actions and recent employer activity.

- **Query params (optional):**
  - `page` — page number (default: `1`)
  - `per_page` — items per page (default: `10`, max: `100`)
  - `search` — filter by company name, email, or phone
  - `status` — `all` \| `active` \| `pending_verification` \| `suspended`

**Response — 200 OK:** See the **"Employers Management — Response"** section in [ADMIN_DASHBOARD_RESPONSE.md](ADMIN_DASHBOARD_RESPONSE.md).

---

## Advertisements Management (job ads + tender ads)

Tenders are a type of advertisement (tender ads). They are **fetched via approved endpoints** and **seeded** in the system. The admin can **list**, **view**, **approve**, and **reject** tenders.

| Purpose | Endpoints |
|--------|-----------|
| Overview (KPIs + list by type: all / job_ads / tender_ads) | `GET /api/admin/advertisements/overview` |
| Tenders (list, get one) | `GET /api/admin/tenders`, `GET /api/admin/tenders/{id}` |
| Approve / Reject tender | `PUT /api/admin/tenders/{id}/approve`, `PUT /api/admin/tenders/{id}/reject` |

Full request/response details and public tenders API: **[ADMIN_ADVERTISEMENTS_API.md](ADMIN_ADVERTISEMENTS_API.md)**.

---

## Admin Users Management

Manage admin accounts (create users, assign fixed roles, see activity).

- **Overview (dashboard data + list):** `GET /api/admin/admin-users/overview`
- **Create admin:** `POST /api/admin/admin-users`
- **Update admin:** `PUT /api/admin/admin-users/{id}`
- **Deactivate admin:** `DELETE /api/admin/admin-users/{id}`

Only admins with `admin_role = "super_admin"` can call these endpoints.  
Full details in **[ADMIN_USERS_API.md](ADMIN_USERS_API.md)**.

---

## Notifications (management + navbar inbox)

**Notification campaigns (dashboard):** Create, list, edit, duplicate, send notifications to audiences (e.g. all employers).  
**Admin inbox (navbar):** List and mark as read the in-app notifications for the logged-in admin (e.g. new employer registered, new job post, someone hired).

Full request/response details and automatic notification events: **[ADMIN_NOTIFICATIONS_API.md](ADMIN_NOTIFICATIONS_API.md)**.

| Purpose | Endpoints |
|--------|-----------|
| List campaigns + KPIs, create, get, update, delete, duplicate, send | `GET/POST /api/admin/notifications`, `GET/PUT/DELETE /api/admin/notifications/{id}`, `POST .../duplicate`, `POST .../send` |
| Navbar: list inbox, unread count, mark read | `GET /api/admin/inbox`, `GET /api/admin/inbox/unread-count`, `PUT /api/admin/inbox/{id}/read`, `PUT /api/admin/inbox/mark-all-read` |

---

## CORS

CORS is configured to allow all origins for the API (`config/cors.php`). You can restrict origins later if needed.

## Seeder

Default admin user (run `php artisan db:seed --class=AdminUserSeeder` or full `php artisan db:seed`):

- **Email:** `admin@jobs.com`
- **Password:** `admin123`

If you get "Trait Laravel\Sanctum\HasApiTokens not found", run `composer dump-autoload` (or `composer dump-autoload --no-scripts`) then run the seeder again.
