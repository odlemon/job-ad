# Admin: Tender Ads Management

This document describes the API and UI behavior for the **Admin Tender Ads Management** dashboard: stats (low cards), status tabs, tender list, search/filter, and the five actions per tender (Preview & Review, Approve, Request Edits, Reject, Manage).

All endpoints require admin authentication: `Authorization: Bearer <admin_sanctum_token>`.

**Base URL:** `/api/admin`

---

## 1. Dashboard (list + stats + status counts)

Single endpoint that returns everything needed to render the dashboard: the six low-card stats, the status tab counts, and the paginated list of tenders (with fields for each row and for the action buttons).

### Request

```
GET /api/admin/tenders/dashboard
```

| Query param | Type | Description |
|-------------|------|-------------|
| `status`    | string | Filter list by status: `pending_approval`, `approved`, `flagged`, `expired`, `all_tenders` (default). |
| `search`    | string | Search by title, organization (entity_name), or reference number. |
| `page`      | int  | Page number (default 1). |
| `per_page`  | int  | Items per page (1–100, default 15). |

### Response

**Status:** `200 OK`

- **stats** — Values for the six low cards (and for any percentage deltas you compute elsewhere).
- **status_counts** — Counts per tab; use for tab labels and active state.
- **tenders** — Array of list items; each item includes engagement metrics and all data needed for the five actions.
- **pagination** — For the list pagination UI.

```json
{
  "stats": {
    "total_tender_ads": 432,
    "pending_approval": 67,
    "active_tenders": 289,
    "flagged": 12,
    "expired": 45,
    "total_budget_value": 45200000,
    "applications": 1234
  },
  "status_counts": {
    "pending_approval": 67,
    "approved": 289,
    "flagged": 12,
    "expired": 45,
    "all_tenders": 432
  },
  "tenders": [
    {
      "id": 1,
      "title": "Construction of New Highway Infrastructure",
      "reference_number": "Ref: TDR-2025-001234",
      "status": "pending_approval",
      "display_status": "pending approval",
      "is_featured": false,
      "organization": "Ministry of Transport",
      "location": "Victoria, Seychelles",
      "budget_min": 5000000,
      "budget_max": 8000000,
      "currency": "USD",
      "budget_display": "USD 5,000,000 - 8,000,000",
      "deadline": "2025-04-15",
      "views_count": 245,
      "applications_count": 12,
      "shares_count": 8,
      "saved_count": 15
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 432,
    "last_page": 29
  }
}
```

### UI integration

- **Low cards:** Bind each card to the corresponding key in `stats` (e.g. Total Tender Ads → `stats.total_tender_ads`, Pending Approval → `stats.pending_approval`, etc.). Percentage changes are not returned; compute them client-side or from a separate analytics source if needed.
- **Tabs:** Use `status_counts` for the count next to each tab label. When the user selects a tab, call the same endpoint with `status` set to that tab’s value (`pending_approval`, `approved`, `flagged`, `expired`, or `all_tenders`).
- **Search:** When the user types and submits search, call with `search=<query>` (and keep current `status` and `per_page`).
- **List row:** For each item in `tenders`, show title, `reference_number`, status badge from `display_status`, organization, location, `budget_display`, deadline, and the four engagement mini-cards (views, applications, shares, saved). Use `id` for all action endpoints below.
- **Export:** Use the same filters (`status`, `search`) and a larger `per_page` (or multiple pages) to fetch data for export; generate the file on the client or via a dedicated export endpoint if you add one.

---

## 2. Preview & Review

Opens the full tender detail view (e.g. modal or side panel) so the admin can review content before approving or requesting edits.

### Request

```
GET /api/admin/tenders/{id}
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Tender ad ID (from the list row). |

### Response

**Status:** `200 OK`

Full tender object: overview, tender information, submission details, attachments, important dates, performance (views/applications), creator, timestamps.

```json
{
  "data": {
    "id": 1,
    "title": "Construction of New Highway Infrastructure",
    "slug": "construction-new-highway",
    "reference_number": "TDR-2025-001234",
    "tender_type": "RFP",
    "category_id": 2,
    "category": { "id": 2, "name": "Infrastructure" },
    "status": "pending_approval",
    "overview": {
      "description": "...",
      "summary": "...",
      "scope_of_work": "...",
      "requirements": []
    },
    "tender_information": {
      "budget_range": "$5,000,000 - $8,000,000",
      "budget_min": 5000000,
      "budget_max": 8000000,
      "currency": "USD",
      "entity_name": "Ministry of Transport",
      "location": "Victoria, Seychelles"
    },
    "submission_details": { ... },
    "attachments": [],
    "important_dates": {
      "submission_deadline": "2025-04-15",
      "published_date": null
    },
    "performance": { "views_count": 245, "applications_count": 12 },
    "creator": { "id": 5, "name": "Admin User" },
    "created_at": "2025-01-10T08:00:00.000000Z",
    "updated_at": "2025-01-15T12:00:00.000000Z"
  }
}
```

### UI integration

- From the list row, when the user clicks **Preview & Review**, call `GET /api/admin/tenders/{id}` and open a modal (or detail page) with the returned `data`.
- After the admin reviews, they can use **Approve**, **Request Edits**, or **Reject** from the same context; after any of those actions, refresh the list or update local state (e.g. remove from “Pending Approval” and/or update the row).

---

## 3. Approve

Marks the tender as approved (active) so it appears on the public tenders page.

### Request

```
PUT /api/admin/tenders/{id}/approve
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Tender ad ID. |

**Body:** None required.

### Response

**Status:** `200 OK`

```json
{
  "message": "Tender approved",
  "data": { ... }
}
```

`data` is the full tender object (same shape as **Preview & Review**).

**Errors:**

- `404` — Tender not found.
- `422` — Tender is already active.

### UI integration

- When the user clicks **Approve** on a row (or from the preview modal), call `PUT /api/admin/tenders/{id}/approve`.
- On success: show a success message; remove the tender from the “Pending Approval” list (or refresh the dashboard) and update the row’s status to “approved”/“active”.
- On 422: show “Tender is already active” and optionally refresh the list.

---

## 4. Request Edits

Sends the tender back to the employer with status “changes requested” and an optional message (e.g. what to fix).

### Request

```
POST /api/admin/tenders/{id}/request-edits
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Tender ad ID. |

**Body (JSON):**

| Field     | Type   | Required | Description                    |
|-----------|--------|----------|--------------------------------|
| `message` | string | No       | Instructions for the employer. |

**Example:**

```json
{
  "message": "Please clarify the budget range and add the required documents."
}
```

### Response

**Status:** `200 OK`

```json
{
  "message": "Edit request sent",
  "data": { ... }
}
```

`data` is the full tender object; `status` will be `changes_requested`. If the backend stores it, `edit_request_message` will contain the admin’s message.

**Error:** `404` — Tender not found.

### UI integration

- When the user clicks **Request Edits**, open a small form (or modal) to optionally enter a message, then call `POST /api/admin/tenders/{id}/request-edits` with `{ "message": "..." }`.
- On success: show “Edit request sent”; refresh the list or move the tender out of “Pending Approval” (e.g. into a “Changes requested” state if you have a tab for it).
- The employer can later resubmit; the tender may return to `pending_approval` when they do.

---

## 5. Reject

Rejects the tender so it is not published.

### Request

```
PUT /api/admin/tenders/{id}/reject
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Tender ad ID. |

**Body (JSON):** Optional.

| Field   | Type   | Required | Description        |
|---------|--------|----------|--------------------|
| `reason`| string | No       | Rejection reason.  |

**Example:**

```json
{
  "reason": "Scope does not match our procurement criteria."
}
```

### Response

**Status:** `200 OK`

```json
{
  "message": "Tender rejected",
  "data": { ... }
}
```

`data` is the full tender object; `status` will be `rejected`.

**Errors:**

- `404` — Tender not found.
- `422` — Tender is already rejected.

### UI integration

- When the user clicks **Reject**, optionally prompt for a reason, then call `PUT /api/admin/tenders/{id}/reject` with `{ "reason": "..." }`.
- On success: show “Tender rejected”; remove from the current list or move to a “Rejected” state and refresh.
- On 422: show “Tender is already rejected” and refresh if needed.

---

## 6. Manage

Opens the full tender data for management (e.g. edit, archive, or other admin-only options). The same data as **Preview & Review** is used; the difference is intent (managing vs. reviewing).

### Request

```
GET /api/admin/tenders/{id}
```

Same as **Preview & Review**: `GET /api/admin/tenders/{id}`.

### Response

Same as **Preview & Review**: full tender in `data`.

### UI integration

- When the user clicks **Manage**, call `GET /api/admin/tenders/{id}` and open a “Manage” modal or page that shows the full tender (same payload as preview). From there you can:
  - Show the same detail view.
  - Add extra actions (e.g. edit fields, archive, change status) when you have backend support for them.
- Reuse the same integration as **Preview & Review**; only the label and context (e.g. “Manage” vs “Preview & Review”) differ in the UI.

---

## Summary: endpoints and actions

| Action            | Method | Endpoint                                  | Purpose |
|-------------------|--------|-------------------------------------------|---------|
| Dashboard         | GET    | `/api/admin/tenders/dashboard`            | Stats, status counts, paginated list (and filters). |
| Preview & Review  | GET    | `/api/admin/tenders/{id}`                 | Full tender detail for review. |
| Approve           | PUT    | `/api/admin/tenders/{id}/approve`         | Set status to active (public). |
| Request Edits     | POST   | `/api/admin/tenders/{id}/request-edits`   | Set status to changes_requested; optional message. |
| Reject            | PUT    | `/api/admin/tenders/{id}/reject`         | Set status to rejected; optional reason. |
| Manage            | GET    | `/api/admin/tenders/{id}`                 | Full tender detail for management (same as preview). |

---

## Status values and tabs

- **pending_approval** — Awaiting admin decision; show in “Pending Approval” tab.
- **approved** (stored as **active**) — Live on the platform; show in “Approved” tab.
- **flagged** — Flagged for review; show in “Flagged” tab (if you have this status).
- **expired** — Either status `expired` or submission_deadline in the past; show in “Expired” tab.
- **all_tenders** — No status filter; use for “All Tenders” tab.

The dashboard list is filtered by the selected tab via the `status` query parameter; the same parameter applies when exporting or loading more pages.
