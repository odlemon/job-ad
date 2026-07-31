# Admin: Manage Job Ad (Modal Data & Delete)

Endpoints for the **Manage Job Ad** modal: job details, link to the public job post, applicants who shared/saved (stats and list), and delete job ad.

**Base URL:** `/api/admin`  
**Auth:** All endpoints require admin authentication: `Authorization: Bearer <admin_sanctum_token>`.

---

## 1. Get Manage Job Ad data (modal)

Returns everything needed to render the **Manage Job Ad** modal: job info, link to the job post, and lists of applicants who shared and who saved the job (with counts and profile links).

### Request

```
GET /api/admin/job-ads/{id}/manage
```

**Path parameter:**

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
    "company": {
      "id": 5,
      "name": "Analytics Corp"
    },
    "location": "Chicago, IL",
    "job_post_url": "https://your-domain.com/jobs/1"
  },
  "applicants_who_shared": [
    {
      "seeker_id": 10,
      "name": "John Doe",
      "initials": "JD",
      "platform": "LinkedIn",
      "shared_at": "2025-01-15",
      "profile_url": "https://your-domain.com/job-seekers/10"
    },
    {
      "seeker_id": 11,
      "name": "Jane Smith",
      "initials": "JS",
      "platform": "Twitter",
      "shared_at": "2025-01-16",
      "profile_url": "https://your-domain.com/job-seekers/11"
    },
    {
      "seeker_id": 12,
      "name": "Mike Johnson",
      "initials": "MJ",
      "platform": "Facebook",
      "shared_at": "2025-01-17",
      "profile_url": "https://your-domain.com/job-seekers/12"
    }
  ],
  "applicants_who_saved": [
    {
      "seeker_id": 20,
      "name": "Sarah Williams",
      "initials": "SW",
      "saved_at": "2025-01-14",
      "profile_url": "https://your-domain.com/job-seekers/20"
    },
    {
      "seeker_id": 21,
      "name": "David Brown",
      "initials": "DB",
      "saved_at": "2025-01-15",
      "profile_url": "https://your-domain.com/job-seekers/21"
    }
  ],
  "stats": {
    "applicants_who_shared_count": 3,
    "applicants_who_saved_count": 5
  }
}
```

**Field notes:**

- **job.job_post_url** – Use for the “Quick View Job Post” button (opens the public job page in a new tab or same window).
- **applicants_who_shared** – One entry per share: seeker name, initials (e.g. for avatar), platform (LinkedIn, Twitter, Facebook, etc.), date, and **profile_url** for “View Profile”.
- **applicants_who_saved** – One entry per save: seeker name, initials, **saved_at** date, and **profile_url** for “View Profile”.
- **stats** – Use for section titles like “Applicants Who Shared (3)” and “Applicants Who Saved (5)”.

If no one has shared or saved yet, the arrays are empty and the counts are `0`.

**Error:** `404` – Job ad not found.

```json
{
  "message": "Job ad not found"
}
```

---

## 2. Delete Job Ad

Permanently deletes a job advertisement (soft delete: record is marked deleted and excluded from normal queries).

### Request

```
DELETE /api/admin/job-ads/{id}
```

**Path parameter:**

| Parameter | Type | Description        |
|-----------|------|--------------------|
| `id`      | int  | Job advertisement ID |

### Response

**Status:** `200 OK`

```json
{
  "success": true,
  "message": "Job ad deleted successfully."
}
```

**Error:** `404` – Job ad not found.

```json
{
  "message": "Job ad not found"
}
```

---

## Summary

| Endpoint | Method | Purpose |
|----------|--------|--------|
| `/api/admin/job-ads/{id}/manage` | GET    | Data for Manage Job Ad modal: job details, **job_post_url**, applicants who shared, applicants who saved, counts |
| `/api/admin/job-ads/{id}`        | DELETE | Delete the job ad |

- **Link to job post:** Use `job.job_post_url` from the manage response for “Quick View Job Post”.
- **Stats and responses:** Use `applicants_who_shared` and `applicants_who_saved` with `stats.applicants_who_shared_count` and `stats.applicants_who_saved_count` for the modal sections and labels.

---

## Backend: Sharing and saving

- **Saved jobs** – Stored in `saved_jobs` (existing). Each row: `seeker_id`, `job_id`, `saved_at`. The manage endpoint uses this for “Applicants Who Saved”.
- **Job shares** – Stored in `job_shares` (new). Each row: `job_id`, `seeker_id`, `platform` (e.g. `linkedin`, `twitter`, `facebook`), `shared_at`. Run the migration `create_job_shares_table` and record shares when a seeker shares a job so the manage endpoint can return “Applicants Who Shared”.
