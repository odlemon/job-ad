# Scoop API — Backend Status (Laravel)

**Date:** 2026-07-10  
**Base URL:** `http://localhost:8000/api`  
**Auth:** `Authorization: Bearer <token>`

## Seed credentials (local)

| Field | Value |
|-------|--------|
| Email | `scoop.seeker@example.com` |
| Password | `password123` |
| User type | `job_seeker` |

Login:

```http
POST /api/auth/login
Content-Type: application/json

{ "email": "scoop.seeker@example.com", "password": "password123" }
```

## Missing / incomplete API data — FE reply

| # | Item | Status | Notes / workaround |
|---|------|--------|--------------------|
| 1–2 | Workplace + socials + benefits/values on `GET /companies/{id}` | **Shipped** | `workplace_description`, `culture_benefits`, `working_hours`, `linkedin`/`twitter`/`facebook`/`instagram`, `benefits[]`, `values[]`. Seeded on companies #1–2. |
| 3 | Similar jobs | **Shipped** | Read `similar_jobs` (and `other_company_jobs`) from `GET /jobs/{id}` — sibling keys beside `data`, no second call. |
| 4 | Review aggregates | **Shipped** | `rating_distribution` `{ "1"…"5" }` + `aspect_averages` on company show; also in reviews `meta`. |
| 5 | `followers_count` | **Shipped** | Live count from `followed_companies`; increments on follow / decrements on unfollow (no denormalized column). |
| 6 | Per-section profile flags | **Shipped** | `profile_sections` map on `GET /job-seeker/profile` (+ strength aggregates). |
| 7 | Share card | **Shipped** | `GET /job-seeker/share-card` → name, email, phone, location, social_links, `qr_url: null`. |
| 8 | Unified search | **Won’t do** | Keep three parallel calls. `GET /jobs/search` accepts `query`, `education`, `sort=newest` (mapped to `latest`). |
| 9 | Category icons | **Shipped** | `GET /meta/job-categories` items include `icon` (emoji; DB column or name fallback). |
| 10 | Advertise banner | **Shipped** | `GET /meta/banners` → `{ data: [{ image_url, title, link, placement: "home_advertise" }] }`. |
| P4 | Tender attachment download / zip | **Shipped** | Real resolve: remote URL → redirect; local file → stream; missing → 404/422 with message (not fake success). |
| P4 | Course enroll | **Shipped** | `POST /courses/{id}/enroll` → `201` + row; duplicate → `422`. |
| P4 | Skills / languages / certs field names | **Shipped** | Scoop names (`name`, `level`, …) + legacy keys kept. |
| P4 | Applications list shape | **Shipped** | `{ data, meta }` with nested `job` via Scoop presenter. |

### Example snippets

**Company detail (excerpt):**

```json
{
  "data": {
    "id": 1,
    "workplace_description": "Open-plan offices…",
    "benefits": [{ "title": "Health cover", "description": "…", "icon": "heart" }],
    "values": [{ "title": "Craft", "description": "…" }],
    "instagram": "https://www.instagram.com/techsolutions.sc",
    "rating_distribution": { "1": 0, "2": 0, "3": 0, "4": 2, "5": 1 },
    "aspect_averages": { "work_life_balance": 3.7 },
    "followers_count": 0
  }
}
```

**Job detail similar jobs:**

```json
{
  "data": { "id": 1, "title": "…" },
  "similar_jobs": [ { "id": 2, "title": "…" } ],
  "other_company_jobs": [ { "id": 3, "title": "…" } ]
}
```

**Profile sections:**

```json
{
  "data": {
    "profile_sections": {
      "personal_info": true,
      "about": true,
      "photo": false,
      "preferences": true,
      "salary": false,
      "discovery": false,
      "experience": true,
      "education": true,
      "skills": true,
      "languages": false,
      "certifications": false,
      "documents": false,
      "references": false
    },
    "profile_strength_percent": 54
  }
}
```

## Path confirmation / renames

| Contract path | Status | Notes |
|---------------|--------|-------|
| Auth login/register/logout/me | Live | User shaped for Scoop |
| Auth OTP / forgot / reset / change / account / 2fa | Live | 2FA is stub |
| `GET/PUT /job-seeker/profile` | Live | Scoop Profile + `profile_sections` |
| `GET /job-seeker/share-card` | Live | `qr_url` null (no QR generation) |
| `POST /job-seeker/profile/photo` | Live | Accepts `photo` or `profile_photo` |
| `GET /job-seeker/summary` | Live | |
| Nested CRUD experiences/educations/skills/languages/certs/refs | Live | Scoop + legacy field names |
| Hobbies / social-links | Live | Hobbies stored as JSON array on profile |
| Documents + download | Live | |
| Settings | Live | |
| Notifications | Live | Also keeps Blade `PUT .../mark-all-read` aliases |
| Jobs published/search/detail | Live | Scoop Job schema + `meta`; `similar_jobs` on detail |
| `POST/DELETE /jobs/{id}/save`, `POST .../apply`, `.../report` | Live | Old `/job-seeker/applications` + `/saved-jobs` still work |
| Recommended / invitations | Live | Invitations from `invite_sent_at` |
| Companies detail/jobs/reviews/follow | Live | Workplace, socials, benefits, review aggregates |
| Tenders list/detail | Live | Scoop fields + legacy nested keys |
| Tender zip/attachment download | Live | Real file resolve; 404/422 if missing |
| Courses + featured providers | Live (seeded) | Real enrollments table |
| Meta endpoints | Live | Includes `icon` on categories + `/meta/banners` |

## Employer web UI

Unchanged. Employer Blade/session routes were not rewritten. Shared services are reused; Scoop uses Sanctum API routes + presenters.

## Ready for frontend wiring

See [`FRONTEND_WIRING.md`](./FRONTEND_WIRING.md). Point `src/api.js` base URL to `http://localhost:8000/api` and replace demo mocks per screen checklist.

Closed stubs: [`BACKEND_ISSUES.md`](./BACKEND_ISSUES.md).

OpenAPI: [`openapi.yaml`](./openapi.yaml) remains the contract; this file confirms implementation status.
