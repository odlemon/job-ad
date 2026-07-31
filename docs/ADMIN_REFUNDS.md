# Admin: Refunds Management API

This document describes the API for the **Admin Refunds Management** dashboard: summary statistics (Total Refunds, Pending, Completed, Success Rate), the refund requests table (search, status filter, pagination), **Add Refund**, **View Details**, **Approve**, **Reject**, **Revert**, and **View Reports**.

All endpoints require admin authentication: `Authorization: Bearer <admin_sanctum_token>`.

**Base URL:** `/api/admin`

---

## 1. Dashboard (stats + refund list)

Returns the four summary cards and the first page of refund requests in one call. Use this when loading the Refunds Management page.

### Request

```
GET /api/admin/refunds/dashboard
```

| Query param | Type   | Description |
|-------------|--------|-------------|
| `per_page`  | int    | Items per page (1–100, default 10). |
| `page`      | int    | Page number. |
| `status`    | string | Filter by status: `all`, `pending`, `processing`, `approved`, `completed`, `rejected`. |
| `search`    | string | Search by request ID, company name, or contact email. |

### Response

**Status:** `200 OK`

```json
{
  "stats": {
    "total_refunds_amount": 156450,
    "pending_count": 12,
    "pending_amount": 19900,
    "completed_count": 234,
    "success_rate_percent": 87.5,
    "currency": "SCR"
  },
  "refunds": [
    {
      "id": 1,
      "request_id": "REF-2026-001",
      "employer_name": "Tech Solutions Ltd",
      "employer_email": "contact@techsolutions.sc",
      "amount": 2500,
      "currency": "SCR",
      "coins_equivalent": 500,
      "payment_method": "card",
      "payment_method_label": "Card",
      "type": "job",
      "type_label": "Job",
      "status": "pending",
      "date": "1/18/2026 1:15:00 AM",
      "created_at": "2026-01-18T01:15:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 5,
    "last_page": 1
  }
}
```

### User story (what to implement on the admin dashboard)

- **Total Refunds card:** Show `stats.total_refunds_amount` with `stats.currency` (e.g. "SCR 156,450"). Optionally show a trend (e.g. +8.2%) if you compute it elsewhere.
- **Pending Refunds card:** Show `stats.pending_count` (e.g. "12") and `stats.pending_amount` (e.g. "SCR 19,900") with a clock icon.
- **Completed Refunds card:** Show `stats.completed_count` (e.g. "234") and optionally a trend.
- **Success Rate card:** Show `stats.success_rate_percent` (e.g. "87.5%").
- **Table:** Bind each row to an item in `refunds`. Columns: REQUEST ID (`request_id`), EMPLOYER (name + email), AMOUNT (amount + currency, and `coins_equivalent` if present), PAYMENT METHOD (`payment_method_label`), TYPE (`type_label`), STATUS (`status` – use for badge color: pending=yellow, processing=blue, completed/approved=green, rejected=red), DATE (`date`).
- **Actions per row:**  
  - Eye icon → call **View details** (§ 4) with this refund `id`.  
  - Checkmark (only when `status === 'pending'`) → call **Approve** (§ 6).  
  - Cross (only when `status === 'pending'`) → call **Reject** (§ 7).  
  - Undo/Refresh (when `status` is `processing` or `approved`) → call **Revert** (§ 8).
- **Search:** "Search refunds..." sends `search` query param and refetches (dashboard or list endpoint).
- **All Status dropdown:** Sends `status` (e.g. `pending`, `completed`); use `all` or omit for all statuses.
- **Pagination:** "Showing X of Y refunds" from `pagination.per_page`, `pagination.total`; Previous/Next use `page` param.

---

## 2. List refunds (table only)

Same as the table part of the dashboard; use when you only need to refresh the list or change page/status/search without re-fetching stats.

### Request

```
GET /api/admin/refunds
```

| Query param | Type   | Description |
|-------------|--------|-------------|
| `per_page`  | int    | Items per page. |
| `page`      | int    | Page number. |
| `status`    | string | `all`, `pending`, `processing`, `approved`, `completed`, `rejected`. |
| `search`    | string | Search request ID, company name, or email. |

### Response

**Status:** `200 OK`

```json
{
  "refunds": [ ... ],
  "pagination": { "current_page": 1, "per_page": 10, "total": 5, "last_page": 1 }
}
```

### User story

- When the user changes the status filter, search, or page, call this endpoint (or the dashboard with the same params) and replace the table body. Use `pagination.total` for "Showing X of Y refunds".

---

## 3. Get companies for Add Refund dropdown

Returns a list of companies (id and name) for the **Add Refund** modal. The frontend should **not** ask the user to type a company ID; instead, when the modal opens, call this endpoint and show a **dropdown of company names**. When the user selects a company, the form stores its `id` and sends it as `company_id` when creating the refund.

### Request

```
GET /api/admin/refunds/companies
```

No query parameters. Call this when the user opens the Add Refund modal.

### Response

**Status:** `200 OK`

```json
{
  "companies": [
    { "id": 1, "name": "Tech Solutions Ltd" },
    { "id": 2, "name": "Global Solutions" },
    { "id": 5, "name": "Creative Agency" }
  ]
}
```

### User story

- When the user clicks **"+ Add Refund"** and the modal opens, immediately call `GET /api/admin/refunds/companies`.
- Populate a **dropdown** with the returned list: display each company’s **name**, and use each company’s **id** as the value.
- Do **not** show an "Employer ID" or "Company ID" text input; the only company selector is this dropdown (company by name → id is sent as `company_id` on submit).
- User selects one company from the dropdown; on submit, send the selected `id` as `company_id` in the POST body.

---

## 4. Add Refund

Creates a new refund request (admin-initiated for a company). Powers the **"Create Refund"** button in the Add Refund modal.

### Request

```
POST /api/admin/refunds
```

**Body (JSON):**

| Field              | Type   | Required | Description |
|--------------------|--------|----------|-------------|
| `company_id`       | int    | No       | Company ID from the company dropdown (select company by name in the modal). |
| `amount`           | number | Yes      | Refund amount. |
| `currency`        | string | No       | Default `SCR`. |
| `coins_equivalent` | int    | No       | Equivalent in coins. |
| `payment_method`   | string | No       | `card`, `mobile_money`, `bank`. |
| `type`             | string | Yes      | `job`, `advertisement`, `coins`, `tender`. |
| `reason`           | string | No       | Reason for refund (employer-facing). |

**Note:** Only `company_id` is needed to identify the payer. Do not send or display an "Employer ID" field; the backend resolves employer/company from `company_id`.

**Example:**

```json
{
  "company_id": 5,
  "amount": 2500,
  "currency": "SCR",
  "coins_equivalent": 500,
  "payment_method": "card",
  "type": "job",
  "reason": "Duplicate charge for same job posting"
}
```

### Response

**Status:** `201 Created`

```json
{
  "message": "Refund request created",
  "refund": {
    "id": 2,
    "request_id": "REF-2026-002",
    "employer_name": "Tech Solutions Ltd",
    "employer_email": "contact@techsolutions.sc",
    "amount": 2500,
    "currency": "SCR",
    "coins_equivalent": 500,
    "payment_method": "card",
    "payment_method_label": "Card",
    "type": "job",
    "type_label": "Job",
    "status": "pending",
    "date": "1/18/2026 2:00:00 PM",
    "created_at": "2026-01-18T14:00:00.000000Z"
  }
}
```

### User story

- **Modal fields:** Company (dropdown from § 3 – company names, value = id), Amount (required), Currency (read-only SCR), Coins equivalent (optional), Payment method (dropdown: Card, Mobile Money, Bank), Type (dropdown: Job, Advertisement, Coins, Tender), Reason (optional text area). Do **not** show an Employer ID or raw Company ID input.
- When the user clicks **"Create Refund"**, send `POST /api/admin/refunds` with the selected `company_id` (from the company dropdown) and the other form values.
- On success: show "Refund request created", close the modal, and refresh the list (call dashboard or list endpoint) so the new row appears.

---

## 5. View details (single refund)

Returns full details for one refund request. Used when the user clicks the **eye** icon.

### Request

```
GET /api/admin/refunds/{id}
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Refund request ID (primary key). |

### Response

**Status:** `200 OK`

```json
{
  "refund": {
    "id": 1,
    "request_id": "REF-2026-001",
    "employer_name": "Tech Solutions Ltd",
    "employer_email": "contact@techsolutions.sc",
    "amount": 2500,
    "currency": "SCR",
    "coins_equivalent": 500,
    "payment_method": "card",
    "payment_method_label": "Card",
    "type": "job",
    "type_label": "Job",
    "status": "pending",
    "date": "1/18/2026 1:15:00 AM",
    "created_at": "2026-01-18T01:15:00.000000Z",
    "reason": "Duplicate charge for same job posting",
    "admin_notes": null,
    "processed_at": null,
    "processed_by": null,
    "company_id": 5,
    "employer_id": 10
  }
}
```

**Error:** `404` — Refund request not found.

### User story

- When the user clicks the **eye** icon on a row, call `GET /api/admin/refunds/{id}` and open a detail modal or side panel.
- Show all fields: request ID, employer name/email, amount, currency, coins, payment method, type, status, date, reason, admin notes, processed at/by.
- From the detail view you can show **Approve** / **Reject** (if status is pending) or **Revert** (if status is approved/processing); call the corresponding action endpoint and then refresh this detail and the list.

---

## 6. View reports

Returns aggregated data for the **View Reports** section (e.g. stats by status and by type). Use for a reports page or export.

### Request

```
GET /api/admin/refunds/reports
```

No query parameters required.

### Response

**Status:** `200 OK`

```json
{
  "stats": {
    "total_refunds_amount": 156450,
    "pending_count": 12,
    "pending_amount": 19900,
    "completed_count": 234,
    "success_rate_percent": 87.5,
    "currency": "SCR"
  },
  "by_status": [
    { "status": "pending", "count": 12, "total_amount": 19900 },
    { "status": "completed", "count": 200, "total_amount": 120000 },
    { "status": "approved", "count": 34, "total_amount": 16450 },
    { "status": "rejected", "count": 30, "total_amount": 0 }
  ],
  "by_type": [
    { "type": "job", "count": 150, "total_amount": 80000 },
    { "type": "tender", "count": 60, "total_amount": 40000 },
    { "type": "coins", "count": 40, "total_amount": 26450 },
    { "type": "advertisement", "count": 26, "total_amount": 10000 }
  ]
}
```

### User story

- When the user clicks **View Reports**, call `GET /api/admin/refunds/reports` and open a reports view or modal.
- Display the same summary stats and tables/charts for `by_status` and `by_type` (count and total amount per status and per type).
- **Download:** Use this data (or the list endpoint with a large `per_page` / multiple pages) to build a CSV/Excel on the client, or add a dedicated export endpoint later.

---

## 7. Approve refund

Moves a **pending** refund to **approved** and records processor and time. Used for the **checkmark** action.

### Request

```
PUT /api/admin/refunds/{id}/approve
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Refund request ID. |

**Body (JSON):** Optional.

| Field         | Type   | Description |
|---------------|--------|-------------|
| `admin_notes` | string | Admin note (stored on the refund). |

### Response

**Status:** `200 OK`

```json
{
  "message": "Refund approved",
  "refund": { ... }
}
```

`refund` is the same list-item shape (§ 1) with updated `status` and dates.

**Errors:**

- `404` — Refund request not found.
- `422` — Only pending refunds can be approved.

### User story

- Show the checkmark only when `status === 'pending'`. On click, optionally ask for confirmation and/or admin notes, then call `PUT /api/admin/refunds/{id}/approve`.
- On success: show "Refund approved", refresh the row or the full list so the status and actions update (e.g. row now shows Approved and Revert instead of Approve/Reject).

---

## 8. Reject refund

Moves a **pending** refund to **rejected** and records processor and time. Used for the **cross** action.

### Request

```
PUT /api/admin/refunds/{id}/reject
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Refund request ID. |

**Body (JSON):** Optional.

| Field         | Type   | Description |
|---------------|--------|-------------|
| `admin_notes` | string | Reason or note (e.g. for employer). |

### Response

**Status:** `200 OK`

```json
{
  "message": "Refund rejected",
  "refund": { ... }
}
```

**Errors:**

- `404` — Refund request not found.
- `422` — Only pending refunds can be rejected.

### User story

- Show the cross icon only when `status === 'pending'`. On click, optionally prompt for a reason (admin_notes), then call `PUT /api/admin/refunds/{id}/reject`.
- On success: show "Refund rejected", refresh the row/list; the row shows status "rejected" (red) and no Approve/Reject/Revert.

---

## 9. Revert refund

Sets an **approved** or **processing** refund back to **pending** (e.g. cancel or resubmit). Used for the **undo/refresh** icon.

### Request

```
PUT /api/admin/refunds/{id}/revert
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Refund request ID. |

**Body:** None required.

### Response

**Status:** `200 OK`

```json
{
  "message": "Refund reverted to pending",
  "refund": { ... }
}
```

**Errors:**

- `404` — Refund request not found.
- `422` — Only approved or processing refunds can be reverted.

### User story

- Show the undo/refresh icon only when `status` is `approved` or `processing`. On click, confirm (e.g. "Revert to pending?"), then call `PUT /api/admin/refunds/{id}/revert`.
- On success: show "Refund reverted to pending", refresh the row/list; the row shows status "pending" and Approve/Reject icons again.

---

## Refund item shape (list and detail)

List items (dashboard, index, and after create/approve/reject/revert) contain:

| Field                  | Type   | Description |
|------------------------|--------|-------------|
| `id`                   | int    | Primary key (use for show/approve/reject/revert). |
| `request_id`           | string | Display ID (e.g. REF-2026-001). |
| `employer_name`        | string | Company or payer name. |
| `employer_email`       | string | Contact email. |
| `amount`               | float  | Refund amount. |
| `currency`             | string | e.g. SCR. |
| `coins_equivalent`     | int \| null | Optional coins. |
| `payment_method`       | string | Raw value. |
| `payment_method_label` | string | e.g. Card, Bank, Mobile Money. |
| `type`                 | string | job, advertisement, coins, tender. |
| `type_label`           | string | Job, Advertisement, Coins, Tender. |
| `status`               | string | pending, processing, approved, completed, rejected. |
| `date`                 | string | Formatted date/time. |
| `created_at`           | string | ISO 8601. |

Detail response adds: `reason`, `admin_notes`, `processed_at`, `processed_by`, `company_id`, `employer_id`.

---

## Summary: endpoints and when to use them

| Purpose              | Method | Endpoint | User story |
|----------------------|--------|----------|------------|
| Load Refunds page    | GET    | `/api/admin/refunds/dashboard` | Initial load: stats + first page of list. |
| Refresh list / filter / page | GET | `/api/admin/refunds` | Status filter, search, pagination. |
| Get companies for dropdown | GET | `/api/admin/refunds/companies` | When Add Refund modal opens: fetch list, show company names in dropdown (value = id). |
| Add Refund           | POST   | `/api/admin/refunds` | "Create Refund" submit with selected `company_id` from dropdown (no Employer ID). |
| View one refund      | GET    | `/api/admin/refunds/{id}` | Eye icon → detail modal/panel. |
| View reports         | GET    | `/api/admin/refunds/reports` | "View Reports" button. |
| Approve              | PUT    | `/api/admin/refunds/{id}/approve` | Checkmark on pending row. |
| Reject               | PUT    | `/api/admin/refunds/{id}/reject` | Cross on pending row. |
| Revert               | PUT    | `/api/admin/refunds/{id}/revert` | Undo on approved/processing row. |

**Download:** Use `GET /api/admin/refunds` with a large `per_page` (or multiple pages) and build CSV/Excel on the client, or add a dedicated export endpoint later.
