# Scoop API Contract — Backend Handoff

**Audience:** Backend agent / API team  
**Consumer:** Vue Scoop app (`src/api.js` base URL)  
**Base URL:** `http://localhost:8000/api`  
**Auth:** `Authorization: Bearer <token>` (frontend stores token in `localStorage.token`)

This document is the source of truth for APIs the frontend needs. Implement these endpoints, then return OpenAPI (or confirm this file) with any field renames so the frontend can wire live calls.

---

## Conventions

| Rule | Detail |
|------|--------|
| Content-Type | `application/json` (except multipart uploads) |
| Resource envelope | `{ "data": ... }` for GET list/detail |
| Auth success | `{ "token", "user" }` at top level for login / register / OTP verify |
| Errors | `{ "message": "..." }` + HTTP 401 / 403 / 404 / 422 |
| Field names | `snake_case` |
| Pagination | `?page=1&limit=20` → `{ "data": [], "meta": { "current_page", "last_page", "total", "per_page" } }` |
| Dates | ISO 8601 timestamps; `YYYY-MM-DD` for DOB; `YYYY-MM` for month ranges |
| App user type | `user_type: "job_seeker"` only |

---

## 1. Authentication

### Endpoints

| Method | Path | Body | Success |
|--------|------|------|---------|
| POST | `/auth/login` | `{ email, password }` | `{ token, user }` |
| POST | `/auth/register` | see Register body | `{ token, user }` **or** `{ message }` then OTP flow |
| POST | `/auth/logout` | — (Bearer) | 200 |
| GET | `/auth/me` | — (Bearer) | `{ user }` (optional nested `user.job_seeker`) |
| POST | `/auth/otp/send` | `{ email }` | `{ message }` |
| POST | `/auth/otp/verify` | `{ email, code }` (6 digits) | `{ token, user }` |
| POST | `/auth/otp/resend` | `{ email }` | `{ message }` |
| POST | `/auth/forgot-password` | `{ email }` | `{ message }` |
| POST | `/auth/reset-password` | `{ email, token, password, password_confirmation }` | `{ message }` |
| POST | `/auth/change-password` | `{ current_password, password, password_confirmation }` | `{ message }` |
| DELETE | `/auth/account` | optional `{ password }` | 200 |
| POST | `/auth/2fa/enable` | stub OK | |
| POST | `/auth/2fa/disable` | stub OK | |

### Register body

```json
{
  "first_name": "Alex",
  "last_name": "Thompson",
  "date_of_birth": "1995-01-15",
  "gender": "male",
  "employment_status": "currently_employed",
  "highest_education": "bachelor",
  "job_preferences": ["full-time", "part-time"],
  "email": "test@test.com",
  "password": "secret12",
  "password_confirmation": "secret12",
  "user_type": "job_seeker"
}
```

**Enums (single set — frontend will normalize UI labels):**

- `gender`: `male` | `female` | `non_binary` | `other` | `prefer_not_to_say`
- `employment_status`: `currently_employed` | `employed_part_time` | `self_employed` | `unemployed` | `student` | `retired` | `prefer_not_to_say`
- `highest_education`: `high_school` | `certificate_diploma` | `associate` | `bachelor` | `master` | `doctorate` | `other` | `prefer_not_to_say`
- `job_preferences` (register + profile): job types / modes / levels as strings, e.g. `full-time`, `part-time`, `contract`, `temporary`, `internship`, `freelance`, `remote`, `on-site`, `hybrid`, `entry`, `mid`, `senior`, `lead`, `manager`, `director`

### User object

```json
{
  "id": 1,
  "name": "Alex Thompson",
  "first_name": "Alex",
  "last_name": "Thompson",
  "email": "test@test.com",
  "phone": "+1 (555) 123-4567",
  "user_type": "job_seeker",
  "email_verified_at": "2026-03-01T00:00:00Z",
  "created_at": "2026-03-01T00:00:00Z"
}
```

**Frontend screens:** `/auth/login`, `/auth/register` (5 steps + OTP).

---

## 2. Job seeker profile (core)

| Method | Path | Notes |
|--------|------|-------|
| GET | `/job-seeker/profile` | `{ data: Profile }` |
| PUT | `/job-seeker/profile` | Partial update OK |
| POST | `/job-seeker/profile/photo` | `multipart/form-data` field `photo` → `{ data: { profile_photo: url } }` |
| GET | `/job-seeker/summary` | hub Personal Summary counts |

### Profile

```json
{
  "first_name": "Alex",
  "last_name": "Thompson",
  "email": "test@test.com",
  "phone": "+1 (555) 123-4567",
  "location": "San Francisco, CA",
  "gender": "male",
  "date_of_birth": "1995-01-15",
  "employment_status": "currently_employed",
  "highest_education": "high_school",
  "driving_license": false,
  "profile_photo": null,
  "bio": "…",
  "job_preferences": ["full-time", "on-site", "mid"],
  "job_discovery_categories": ["Education", "Technology & IT"],
  "expected_salary_min": 120000,
  "expected_salary_max": 180000,
  "joined_label": "Joined Since March 2026",
  "profile_strength_percent": 92,
  "profile_sections_completed": 12,
  "profile_sections_total": 13
}
```

### Summary

```json
{
  "data": {
    "applied": 3,
    "saved": 5,
    "invited": 0,
    "discovery": 12,
    "companies": 2
  }
}
```

**Screens:** Profile hub, Personal Info + `/profile/edit/:field`, About Me, Job Preferences, Salary Range, Job Discovery.

---

## 3. Profile nested resources (CRUD)

Pattern: `GET|POST /job-seeker/{collection}` · `PUT|PATCH|DELETE /job-seeker/{collection}/{id}`

### Experiences — `/job-seeker/experiences`

```json
{
  "id": 1,
  "job_title": "Senior Software Engineer",
  "company_name": "Tech Corp",
  "start_date": "2020-01",
  "end_date": null,
  "is_current": true,
  "description": "…"
}
```

### Educations — `/job-seeker/educations`

```json
{
  "id": 1,
  "institution": "Stanford University",
  "degree": "Bachelor of Science",
  "field_of_study": "Computer Science",
  "start_date": "2012-09",
  "end_date": "2016-06",
  "is_current": false,
  "details": null
}
```

### Skills — `/job-seeker/skills`

`{ id, name, level }` — `level`: `beginner` | `intermediate` | `advanced` | `expert` | `null`

### Languages — `/job-seeker/languages`

`{ id, name, level }` — `level`: `native` | `fluent` | `advanced` | `intermediate` | `basic`

### Hobbies — `/job-seeker/hobbies`

`{ id, name }`

### Social links — `/job-seeker/social-links`

`{ id, platform, url }` — `platform`: `linkedin` | `facebook` | `twitter` | `instagram` | `github` | `other`

### Certifications — `/job-seeker/certifications`

```json
{
  "id": 1,
  "name": "AWS Certified Solutions Architect",
  "issuer": "Amazon Web Services",
  "issued_at": "2022-03-15",
  "expires": false,
  "expires_at": null,
  "document_url": null
}
```

Optional: `POST /job-seeker/certifications/{id}/document` (multipart).

### References — `/job-seeker/references`

`{ id, name, title, location, email, phone }`

### Documents — `/job-seeker/documents`

| Method | Path |
|--------|------|
| GET | `/job-seeker/documents` |
| POST | `/job-seeker/documents` | multipart: `name`, `file` (PDF/DOC/DOCX) |
| DELETE | `/job-seeker/documents/{id}` |
| GET | `/job-seeker/documents/{id}/download` |

List item: `{ id, name, size_bytes, size_label, created_at, mime_type, url }`

---

## 4. Settings

| Method | Path | Body |
|--------|------|------|
| GET | `/job-seeker/settings` | — |
| PUT | `/job-seeker/settings` | `{ app_notifications, email_notifications, job_alerts, application_updates, marketing_emails }` (booleans) |

---

## 5. Notifications

| Method | Path |
|--------|------|
| GET | `/notifications?category=&is_read=&page=&limit=` |
| GET | `/notifications/unread-count` → `{ data: { count } }` |
| PATCH | `/notifications/{id}/read` |
| PATCH | `/notifications/read-all` |
| DELETE | `/notifications/{id}` |
| DELETE | `/notifications/read` |

```json
{
  "id": "n1",
  "category": "applications",
  "title": "Application Submitted",
  "body": "…",
  "read": false,
  "created_at": "2026-03-27T10:00:00Z"
}
```

`category`: `applications` | `alerts` (omit for All).

---

## 6. Jobs

### Job resource

```json
{
  "id": 1,
  "title": "Dive Instructor",
  "employer_name": "Blue Water Diving Center",
  "employer_id": 10,
  "verified": true,
  "location": "Mahe",
  "salary_min": 9000,
  "salary_max": 13000,
  "salary_currency": "SCR",
  "category": { "id": 1, "name": "Hospitality & Tourism" },
  "job_type": { "id": 1, "name": "Full-time" },
  "positions_available": 2,
  "created_at": "2026-03-27T00:00:00Z",
  "expiry_date": "2027-01-01T00:00:00Z",
  "description": "…",
  "is_saved": false,
  "application_status": null,
  "employer": {
    "id": 10,
    "name": "Blue Water Diving Center",
    "size": "50-100",
    "industry": "Tourism",
    "rating": 4.5,
    "reviews_count": 120,
    "jobs_count": 8,
    "location": "Mahe",
    "website": "https://example.com",
    "email": "hr@example.com",
    "phone": "+2480000000",
    "working_hours": "Mon–Fri 08:00–17:00",
    "about_us": "…",
    "logo_url": null,
    "cover_url": null
  }
}
```

### Endpoints

| Method | Path | Query / body |
|--------|------|----------------|
| GET | `/jobs/published` | Home popular list → `{ data: Job[] }` |
| GET | `/jobs/search` | `query`, `category` (csv), `location` (csv), `job_type` (csv), `education`, `sort=newest` |
| GET | `/jobs/{id}` | Detail |
| POST | `/jobs/{id}/apply` | 200 or 422 if already applied |
| POST | `/jobs/{id}/save` | |
| DELETE | `/jobs/{id}/save` | |
| POST | `/jobs/{id}/report` | `{ reason }` |

### Job seeker lists

| Method | Path | Notes |
|--------|------|-------|
| GET | `/job-seeker/applications` | `status`: `applied` \| `in_review` \| `interview` \| `offered` \| `rejected`; include `applied_at`, `updated_at`, `status_message`, nested `job` |
| GET | `/job-seeker/saved-jobs` | |
| GET | `/job-seeker/recommended-jobs` | Driven by prefs + discovery categories |
| GET | `/job-seeker/invitations` | |
| POST | `/job-seeker/invitations/{id}/accept` | |
| POST | `/job-seeker/invitations/{id}/decline` | |

### Meta (recommended)

`GET /meta/job-categories`, `/meta/locations`, `/meta/job-types`, `/meta/education-levels`

---

## 7. Companies

| Method | Path |
|--------|------|
| GET | `/companies/{id}` |
| GET | `/companies/{id}/jobs` |
| GET | `/companies/{id}/reviews` |
| POST | `/companies/{id}/reviews` | `{ rating, title, body }` |
| POST | `/companies/{id}/follow` |
| DELETE | `/companies/{id}/follow` |
| GET | `/job-seeker/followed-companies` |

Company: `{ id, name, location, logo_url, cover_url, industry, size, rating, reviews_count, jobs_count, followers_count, about_us, website, email, phone, working_hours, is_following }`.

---

## 8. Tenders

| Method | Path |
|--------|------|
| GET | `/tenders` | `?q=` |
| GET | `/tenders/{id}` | |
| GET | `/tenders/{id}/attachments/{attachmentId}/download` | |
| GET | `/tenders/{id}/documents/zip` | Download all |
| POST | `/tenders/{id}/clarifications` | `{ message }` |
| POST | `/tenders/{id}/report` | `{ reason }` |

Required fields: `tags[]` (`{ label, type }`), `deadline`, `deadline_long`, `budget`, `location`, `reference`, `sector`, `procuring_entity`, `country`, `scope`, `requirements[]`, `submission: { method, required_documents[], eligibility[] }`, `attachments: [{ id, name, size }]`, `dates: [{ label, value }]`, `title`, `summary`.

---

## 9. Courses

| Method | Path |
|--------|------|
| GET | `/courses` | `?q=` |
| GET | `/courses/{id}` | |
| GET | `/training-providers/featured` | `{ name, subtitle, courses_available, tagline }` |
| POST | `/courses/{id}/enroll` | Stub OK (no enroll UI yet) |

Course: `id, title, badge, badges[], level, duration, format, price, image_url, provider, instructor, seats, start_date, phone, email, overview`.

---

## 10. Share

No dedicated API. Share card uses profile + social links. Optional later: `GET /job-seeker/share-card`.

---

## Backend implementation priority

1. Auth: login, register, OTP, me, logout  
2. Profile GET/PUT + photo  
3. Jobs: published, search, detail, apply  
4. Nested profile CRUD  
5. Applications / saved / recommended / invitations / followed companies  
6. Notifications + settings  
7. Tenders + Courses  
8. Companies + reviews  

---

## Deliverable back to frontend

Please return (or update this repo with):

1. Confirmation of final paths / any renames  
2. OpenAPI file (or confirm [`openapi.yaml`](./openapi.yaml))  
3. Example request/response per priority-1 endpoint  
4. Seed credentials for local testing  

**Backend status (implemented):** see [`BACKEND_STATUS.md`](./BACKEND_STATUS.md) for seed credentials, OTP notes, path confirmation table, and examples.

Then the frontend will replace demo mocks with live `src/api.js` calls.
