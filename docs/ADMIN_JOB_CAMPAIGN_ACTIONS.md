# Admin: Job Campaign Actions (Same as Employer)

Admin can perform the same actions as the employer on job campaigns and job ads. All endpoints require admin authentication: `Authorization: Bearer <admin_sanctum_token>`.

**Base URL:** `/api/admin`

---

## 1. View applicants for a job

Returns the list of applicants for a specific job (from the job campaign / job ad), with summary stats and views count.

**UI:** Use `invite_sent_at` or `invited` on each application to show whether the applicant has been invited (e.g. badge “Invited” or disable “Invite” when `invited` is true).

### Request

```
GET /api/admin/job-ads/{id}/applicants
```

| Parameter | Type | Description        |
|-----------|------|--------------------|
| `id`      | int  | Job advertisement ID |

### Response

**Status:** `200 OK`

| Field | Type | Description |
|-------|------|-------------|
| `job` | object | Job summary (id, title, company, location). |
| `applications` | array | List of applications; each item includes invite status (see below). |
| `stats` | object | Counts: total, shortlisted, selected, rejected. |
| `views_count` | int | View count for the job/campaign. |

**Each application object:**

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | Application ID (use for Invite endpoint). |
| `first_name`, `last_name`, `email`, `phone` | string | Applicant contact info. |
| `status` | string | e.g. applied, shortlisted, hired, rejected. |
| `in_talent_pool` | boolean | Whether in talent pool. |
| **`invite_sent_at`** | string \| null | ISO 8601 date when invite was sent; `null` if never invited. |
| **`invited`** | boolean | `true` if this applicant has been invited; use this to show “Invited” in the UI and avoid inviting twice. |
| `cover_letter`, `resume_path`, `created_at` | — | Application details. |
| `job_seeker` | object \| null | Seeker profile (seeker_id, name, profile_photo). |

```json
{
  "job": {
    "id": 1,
    "title": "Data Analyst",
    "company": { "id": 5, "name": "Analytics Corp" },
    "location": "Chicago, IL"
  },
  "applications": [
    {
      "id": 101,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "status": "applied",
      "in_talent_pool": false,
      "invite_sent_at": "2025-01-18T14:00:00.000000Z",
      "invited": true,
      "cover_letter": "I am interested...",
      "resume_path": "resumes/john-doe.pdf",
      "created_at": "2025-01-15T10:00:00.000000Z",
      "job_seeker": {
        "seeker_id": 10,
        "first_name": "John",
        "last_name": "Doe",
        "profile_photo": "photos/10.jpg"
      }
    },
    {
      "id": 102,
      "first_name": "Jane",
      "last_name": "Smith",
      "email": "jane@example.com",
      "status": "applied",
      "in_talent_pool": true,
      "invite_sent_at": null,
      "invited": false,
      "created_at": "2025-01-16T09:00:00.000000Z",
      "job_seeker": { "seeker_id": 11, "first_name": "Jane", "last_name": "Smith", "profile_photo": null }
    }
  ],
  "stats": {
    "total": 12,
    "shortlisted": 2,
    "selected": 0,
    "rejected": 1
  },
  "views_count": 150
}
```

**Error:** `404` — Job ad not found.

---

## 2. Invite applicant

Sends an invite email to an applicant (to apply to the job). Same behavior as employer “Invite” action.

**UI:** After a successful invite, the backend sets `invite_sent_at` on the application. The **View applicants** response includes `invite_sent_at` and `invited` for each applicant—use these to show “Invited” in the dashboard and to disable or hide the Invite button for applicants who were already invited.

### Request

```
POST /api/admin/applications/{id}/invite
```

| Parameter | Type | Description   |
|-----------|------|---------------|
| `id`      | int  | Application ID |

**Body:** None required.

### Response

**Status:** `200 OK`

```json
{
  "message": "Invitation sent successfully"
}
```

**Errors:**

- `404` — Application not found.
- `400` — Applicant has no email.
- `500` — Failed to send invite email (e.g. mail provider error).

```json
{
  "message": "Failed to send invite email",
  "error": "ZeptoMail API error: ..."
}
```

---

## 3. Extend listing (campaign)

Extends the campaign’s end date by a number of days and/or upgrades campaign type. Admin: no coin deduction.

**UI:** Use the **Current plans (campaign types)** table below to build the extend form: e.g. a “Plan” dropdown with `id` and `name`, and a “Days” input (0–365). Send the selected plan’s `id` as `campaign_type_id` and the chosen days as `days`. You need the **campaign ID** (not job ID) for the path—e.g. from the job’s campaigns list or from the job campaigns dashboard.

### Current plans (campaign types)

Use this list to construct the Extend UI (dropdown of plans, display name and duration). IDs may vary by environment; fetch from your API or use the list below as reference.

| id | slug       | name       | duration_days | coins_price | scr_price | est_reach_min | est_reach_max |
|----|------------|------------|---------------|-------------|-----------|---------------|---------------|
| 1  | growthhire | GrowthHire | 15            | 25          | 500       | 5000          | 8000          |
| 2  | smarthire  | SmartHire  | 30            | 45          | 900       | 12000         | 18000         |
| 3  | powerhire  | PowerHire  | 30            | 60          | 1200      | 25000         | 35000         |

**Full campaign type shape (for reference):**

- `id` (int) — use for `campaign_type_id` in extend request.
- `slug` (string) — e.g. `growthhire`, `smarthire`, `powerhire`.
- `name` (string) — display name for dropdown.
- `duration_days` (int) — default duration for the plan.
- `coins_price`, `scr_price` (int) — pricing (admin extend does not deduct).
- `est_reach_min`, `est_reach_max` (int) — estimated reach.
- `features` (array of strings) — plan features (optional for UI).
- `is_popular` (boolean) — can be used to highlight a plan.
- `sort_order` (int) — order for dropdown (1, 2, 3).

### Request

```
POST /api/admin/campaigns/{id}/extend
```

| Parameter | Type | Description   |
|-----------|------|---------------|
| `id`      | int  | Campaign ID   |

**Body (JSON):**

| Field              | Type | Required | Description                                          |
|--------------------|------|----------|------------------------------------------------------|
| `days`             | int  | No       | Number of days to add to campaign end date (0–365). |
| `campaign_type_id` | int  | No       | New campaign type ID (one of the plan ids above).   |

At least one of `days` or `campaign_type_id` should be sent; otherwise the response will indicate no changes.

**Example:**

```json
{
  "days": 14,
  "campaign_type_id": 2
}
```

### Response

**Status:** `200 OK`

```json
{
  "message": "Listing extended by 14 day(s).",
  "campaign": {
    "id": 3,
    "ends_at": "2025-02-28",
    "duration_days": 28,
    "campaign_type": { "id": 2, "name": "SmartHire" }
  }
}
```

**Error:** `404` — Campaign not found.

---

## 4. Edit job post (get form data)

Returns job data and categories for the edit form. Same data as employer edit.

### Request

```
GET /api/admin/job-ads/{id}/edit
```

| Parameter | Type | Description        |
|-----------|------|--------------------|
| `id`      | int  | Job advertisement ID |

### Response

**Status:** `200 OK`

```json
{
  "job": {
    "id": 1,
    "title": "Data Analyst",
    "slug": "data-analyst",
    "description": "...",
    "requirements": "...",
    "benefits": "...",
    "employment_type": "full_time",
    "experience_level": "mid",
    "salary_min": "50000",
    "salary_max": "70000",
    "currency": "USD",
    "hide_salary": false,
    "location": "Chicago, IL",
    "island": null,
    "district": null,
    "is_remote": false,
    "work_environment": "hybrid",
    "education_level": "bachelor",
    "status": "published",
    "company_id": 5,
    "category_id": 2,
    "published_at": "2025-01-10T08:00:00.000000Z",
    "created_at": "2025-01-08T12:00:00.000000Z",
    "updated_at": "2025-01-10T08:00:00.000000Z"
  },
  "categories": [
    { "id": 1, "name": "Technology", "is_active": 1, "sort_order": 1 },
    { "id": 2, "name": "Finance", "is_active": 1, "sort_order": 2 }
  ]
}
```

**Error:** `404` — Job ad not found.

---

## 5. Update job post

Updates the job ad. Same validation as employer update.

### Request

```
PUT /api/admin/job-ads/{id}
```

| Parameter | Type | Description        |
|-----------|------|--------------------|
| `id`      | int  | Job advertisement ID |

**Body (JSON or form):**

| Field             | Type    | Required | Description                    |
|-------------------|---------|----------|--------------------------------|
| `title`           | string  | Yes      | Max 255                        |
| `description`     | string  | Yes      |                                |
| `category_id`     | int     | No       | Must exist in job_categories   |
| `requirements`    | string  | No       |                                |
| `benefits`        | string  | No       |                                |
| `employment_type` | string  | No       | Max 255                        |
| `experience_level`| string  | No       | Max 255                        |
| `salary_min`      | string  | No       |                                |
| `salary_max`      | string  | No       |                                |
| `currency`        | string  | No       | Max 3                          |
| `hide_salary`     | boolean | No       |                                |
| `location`        | string  | No       | Max 255                        |
| `island`          | string  | No       | Max 255                        |
| `district`        | string  | No       | Max 255                        |
| `is_remote`       | boolean | No       |                                |
| `work_environment`| string  | No       | Max 255                        |
| `education_level` | string  | No       | Max 255                        |
| `status`          | string  | No       | draft, published, closed, archived |

**Example (JSON):**

```json
{
  "title": "Senior Data Analyst",
  "description": "We are looking for...",
  "category_id": 2,
  "requirements": "5+ years experience",
  "employment_type": "full_time",
  "experience_level": "senior",
  "salary_min": "60000",
  "salary_max": "85000",
  "currency": "USD",
  "hide_salary": false,
  "location": "Chicago, IL",
  "is_remote": false,
  "work_environment": "hybrid",
  "education_level": "bachelor",
  "status": "published"
}
```

### Response

**Status:** `200 OK`

```json
{
  "message": "Job posting updated successfully.",
  "job": {
    "id": 1,
    "title": "Senior Data Analyst",
    "slug": "senior-data-analyst",
    "status": "published",
    "company": { "id": 5, "name": "Analytics Corp" },
    "category": { "id": 2, "name": "Finance" },
    "location": "Chicago, IL",
    "published_at": "2025-01-10T08:00:00.000000Z",
    "updated_at": "2025-01-20T14:00:00.000000Z"
  }
}
```

**Error:** `404` — Job ad not found. `422` — Validation error.

---

## 6. Post listing / Pause (toggle status)

Activates (publishes) or pauses the job. Toggles between `published` and `draft`.

### Request

```
POST /api/admin/job-ads/{id}/toggle-status
```

| Parameter | Type | Description        |
|-----------|------|--------------------|
| `id`      | int  | Job advertisement ID |

**Body:** None required.

### Response

**Status:** `200 OK`

**When activating (was draft):**

```json
{
  "message": "Job activated successfully",
  "status": "published"
}
```

**When pausing (was published):**

```json
{
  "message": "Job paused successfully",
  "status": "draft"
}
```

**Error:** `404` — Job ad not found.

---

## 7. Share (by job ad)

Returns the public job post URL and increments the job’s campaign share count (uses the job’s first campaign if any).

### Request

```
POST /api/admin/job-ads/{id}/share
```

| Parameter | Type | Description        |
|-----------|------|--------------------|
| `id`      | int  | Job advertisement ID |

**Body:** None required.

### Response

**Status:** `200 OK`

```json
{
  "url": "https://your-domain.com/jobs/1",
  "message": "Share count updated."
}
```

**Error:** `404` — Job ad not found.

---

## 8. Share (by campaign id)

Same as employer: increment campaign share count and return the job’s public URL.

### Request

```
POST /api/admin/campaigns/{id}/share
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id`      | int  | Campaign ID |

**Body:** None required.

### Response

**Status:** `200 OK`

```json
{
  "url": "https://your-domain.com/jobs/1",
  "message": "Share count updated."
}
```

**Error:** `404` — Campaign not found.

---

## 9. Delete job ad

Soft-deletes the job advertisement. Documented in [ADMIN_JOB_AD_MANAGE.md](ADMIN_JOB_AD_MANAGE.md); summarized here.

### Request

```
DELETE /api/admin/job-ads/{id}
```

### Response

**Status:** `200 OK`

```json
{
  "success": true,
  "message": "Job ad deleted successfully."
}
```

---

## Summary

| Action              | Method | Endpoint                              | Description |
|---------------------|--------|----------------------------------------|-------------|
| View applicants      | GET    | `/api/admin/job-ads/{id}/applicants`   | List applicants for the job + stats + views_count |
| Invite applicant     | POST   | `/api/admin/applications/{id}/invite`  | Send invite email to applicant |
| Extend listing       | POST   | `/api/admin/campaigns/{id}/extend`     | Add days and/or upgrade campaign type (no coins) |
| Get edit form        | GET    | `/api/admin/job-ads/{id}/edit`         | Job + categories for edit form |
| Update job post      | PUT    | `/api/admin/job-ads/{id}`              | Update job fields (same as employer) |
| Post listing / Pause | POST   | `/api/admin/job-ads/{id}/toggle-status`| Toggle published ↔ draft |
| Share (by job)       | POST   | `/api/admin/job-ads/{id}/share`        | Get job URL + increment share count |
| Share (by campaign)  | POST   | `/api/admin/campaigns/{id}/share`      | Get job URL + increment campaign share count |
| Delete job ad        | DELETE | `/api/admin/job-ads/{id}`              | Soft-delete job ad |

All of the above require admin authentication.
