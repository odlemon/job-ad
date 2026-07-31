# Admin Dashboard API — Response Structure

**Endpoint:** `GET /api/admin/dashboard`  
**Auth:** `Authorization: Bearer <token>`

Single endpoint that returns all data needed to render the admin dashboard (KPIs, charts, user flow, recent signups, recent payments, ads about to expire).

---

## Response — 200 OK

```json
{
  "kpis": {
    "total_job_seekers": {
      "value": 45234,
      "change_percent": 12.5
    },
    "total_employers": {
      "value": 3456,
      "change_percent": 8.2
    },
    "active_job_ads": {
      "value": 1289,
      "change_percent": 15.3
    },
    "active_tender_ads": {
      "value": 0,
      "change_percent": 0
    },
    "pending_approvals": {
      "value": 67,
      "change_percent": -3.2
    },
    "revenue_this_month": {
      "value": 0,
      "change_percent": 0,
      "currency": "USD"
    }
  },
  "daily_job_applications": {
    "labels": ["Day 1", "Day 2", "Day 3", "Day 4", "Day 5", "Day 6", "Day 7", "Day 8", "Day 9", "Day 10", "Day 11", "Day 12", "Day 13", "Day 14"],
    "data": [42, 38, 55, 61, 48, 72, 65, 58, 70, 81, 76, 89, 85, 92]
  },
  "active_categories": [
    { "name": "Technology", "count": 456 },
    { "name": "Healthcare", "count": 234 },
    { "name": "Finance", "count": 189 },
    { "name": "Education", "count": 167 },
    { "name": "Marketing", "count": 143 },
    { "name": "Construction", "count": 100 }
  ],
  "user_flow": {
    "job_views": 12456,
    "job_clicks": 8234,
    "applications": 1289
  },
  "recent_signups": [
    {
      "name": "John Smith",
      "email": "john@example.com",
      "signed_up_at": "2025-01-18T10:30:00.000000Z",
      "user_type": "Job Seeker"
    },
    {
      "name": "Tech Corp Inc",
      "email": "hr@techcorp.com",
      "signed_up_at": "2025-01-18T09:15:00.000000Z",
      "user_type": "Employer"
    },
    {
      "name": "Sarah Johnson",
      "email": "sarah@example.com",
      "signed_up_at": "2025-01-18T08:45:00.000000Z",
      "user_type": "Job Seeker"
    }
  ],
  "recent_payments": [],
  "ads_about_to_expire": [
    {
      "title": "Senior React Developer",
      "company_name": "Tech Corp",
      "views": 1234,
      "applications": 45,
      "days_until_expiry": 2,
      "type": "Job Ad",
      "job_id": 101
    },
    {
      "title": "Marketing Manager",
      "company_name": "Startup Hub",
      "views": 882,
      "applications": 28,
      "days_until_expiry": 5,
      "type": "Job Ad",
      "job_id": 102
    }
  ]
}
```

---

## Field reference

| Section | Field | Type | Description |
|--------|--------|------|-------------|
| **kpis** | | object | Key metrics with value and % change vs previous period |
| | total_job_seekers | { value, change_percent } | Count of job seeker users |
| | total_employers | { value, change_percent } | Count of employer users |
| | active_job_ads | { value, change_percent } | Count of published job ads |
| | active_tender_ads | { value, change_percent } | Tender ads (0 if not used) |
| | pending_approvals | { value, change_percent } | Employers not yet verified |
| | revenue_this_month | { value, change_percent, currency } | Revenue (0 if no payments) |
| **daily_job_applications** | | object | Last 14 days of application counts |
| | labels | string[] | "Day 1" … "Day 14" |
| | data | number[] | Application count per day |
| **active_categories** | | array | Categories with published ad counts |
| | name | string | Category name |
| | count | number | Number of ads in that category |
| **user_flow** | | object | Funnel: views → clicks → applications |
| | job_views | number | Total job ad views |
| | job_clicks | number | Total campaign clicks (or derived) |
| | applications | number | Total applications |
| **recent_signups** | | array | Last 5 non-admin signups |
| | name | string | User display name |
| | email | string | Email |
| | signed_up_at | string | ISO 8601 datetime |
| | user_type | string | "Job Seeker" or "Employer" |
| **recent_payments** | | array | Payments (empty if no payments table) |
| **ads_about_to_expire** | | array | Campaigns ending in next 14 days |
| | title | string | Job ad title |
| | company_name | string | Company name |
| | views | number | Ad view count |
| | applications | number | Application count |
| | days_until_expiry | number | Days until campaign ends |
| | type | string | "Job Ad" (or "Tender Ad" if supported) |
| | job_id | number | Job advertisement ID |

---

## Notes

- **active_tender_ads**: No tender type in the app yet; value is always 0.
- **revenue_this_month** / **recent_payments**: No payments table; revenue is 0 and recent_payments is [].
- **ads_about_to_expire**: Based on campaign `ends_at`. If there are no campaigns with an end date in the next 14 days, the array is empty.

---

# Job Seekers Management — Response

**Endpoint:** `GET /api/admin/job-seekers/overview`  
**Auth:** `Authorization: Bearer <token>`

Returns summary cards + paginated list for the **Job Seekers Management** screen.

## Response — 200 OK

```json
{
  "summary": {
    "total_job_seekers": {
      "value": 45234,
      "change_percent": 12.4
    },
    "active_users": {
      "value": 42890,
      "change_percent": 11.8
    },
    "pending_verification": {
      "value": 1234,
      "change_percent": -5.4
    },
    "suspended_banned": {
      "value": 1110,
      "change_percent": -2.1
    }
  },
  "filters": {
    "status": ["all", "active", "suspended"]
  },
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 45234,
    "last_page": 4524
  },
  "job_seekers": [
    {
      "id": 1,
      "name": "John Smith",
      "initials": "JS",
      "joined_at": "2025-01-10T09:15:00.000000Z",
      "contact": {
        "email": "johnsmith@example.com",
        "phone": "+1 415-555-0123",
        "location": "San Francisco, USA"
      },
      "activity": {
        "applications_count": 12,
        "last_application_at": "2025-01-18T10:30:00.000000Z",
        "last_login_at": "2025-01-18T10:00:00.000000Z"
      },
      "verification": {
        "kyc_status": "verified",
        "email_verified": true
      },
      "status": "active"
    }
  ],
  "quick_actions": [
    { "key": "approve_pending_accounts", "label": "Approve Pending Accounts" },
    { "key": "review_kyc_submissions", "label": "Review KYC Submissions" },
    { "key": "view_support_tickets", "label": "View Support Tickets" }
  ],
  "recent_activity": [
    {
      "type": "application",
      "message": "John Smith applied to Senior React Developer",
      "created_at": "2025-01-18T10:30:00.000000Z"
    }
  ]
}
```

## Field reference

| Section | Field | Type | Description |
|--------|-------|------|-------------|
| **summary** | | object | Top KPI cards for job seekers |
| | total_job_seekers | { value, change_percent } | Total number of job seeker users |
| | active_users | { value, change_percent } | Job seekers with `is_active = true` |
| | pending_verification | { value, change_percent } | Job seekers with `is_verified = false / null` |
| | suspended_banned | { value, change_percent } | Job seekers with `is_active = false` |
| **filters** | status | string[] | Allowed values for `status` query param |
| **pagination** | | object | Standard pagination meta |
| | current_page | number | Current page number |
| | per_page | number | Items per page |
| | total | number | Total matching job seekers |
| | last_page | number | Last page number |
| **job_seekers** | | array | Paginated list of job seekers |
| | id | number | Internal job seeker ID (`seeker_id`) |
| | name | string | Full name (`first_name` + `last_name` or `user.name`) |
| | initials | string | Up to 2-character initials for avatar circle |
| | joined_at | string\|null | When the underlying user account was created (ISO 8601) |
| | contact.email | string\|null | Email from `users.email` |
| | contact.phone | string\|null | Phone from `job_seekers.phone` or `users.phone` |
| | contact.location | string\|null | Location from `job_seekers.location` |
| | activity.applications_count | number | Total applications from this job seeker |
| | activity.last_application_at | string\|null | Datetime of most recent application (ISO 8601) |
| | activity.last_login_at | string\|null | Last login datetime from `users.last_login` (ISO 8601) |
| | verification.kyc_status | string | `"verified"` or `"pending"` based on `users.is_verified` |
| | verification.email_verified | boolean | True if `email_verified_at` is not null |
| | status | string | `"active"`, `"pending"`, or `"suspended"` derived from flags |
| **quick_actions** | | array | Static list of quick actions to show on the page |
| | key | string | Internal key used by the frontend |
| | label | string | Human readable label |
| **recent_activity** | | array | Recent job seeker related activity (applications) |
| | type | string | Currently always `"application"` |
| | message | string | Human readable description of the event |
| | created_at | string | Datetime of the event (ISO 8601) |

---

# Employers Management — Response

**Endpoint:** `GET /api/admin/employers/overview`  
**Auth:** `Authorization: Bearer <token>`

Returns summary cards + paginated list for the **Employers Management** screen.

## Response — 200 OK

```json
{
  "summary": {
    "total_employers": {
      "value": 3456,
      "change_percent": 8.1
    },
    "active_companies": {
      "value": 3102,
      "change_percent": 6.4
    },
    "pending_verification": {
      "value": 234,
      "change_percent": -3.2
    },
    "suspended_banned": {
      "value": 120,
      "change_percent": 1.5
    }
  },
  "filters": {
    "status": ["all", "active", "pending_verification", "suspended"]
  },
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 1308,
    "last_page": 131
  },
  "employers": [
    {
      "id": 1,
      "company_name": "Tech Corp Inc",
      "contact": {
        "email": "hr@techcorp.com",
        "phone": "+1 415-555-0100",
        "location": "San Francisco, USA"
      },
      "job_ads": {
        "total": 16,
        "last_posted_at": "2025-01-18T09:15:00.000000Z"
      },
      "status": "active",
      "verification": {
        "kyc_status": "verified"
      }
    }
  ],
  "quick_actions": [
    { "key": "approve_pending_companies", "label": "Approve Pending Companies" },
    { "key": "review_job_postings", "label": "Review Job Postings" },
    { "key": "view_support_tickets", "label": "View Support Tickets" },
    { "key": "bulk_credit_top_up", "label": "Bulk Credit Top Up" }
  ],
  "recent_activity": [
    {
      "type": "job_posted",
      "message": "Tech Corp Inc posted new job \"Senior React Developer\"",
      "created_at": "2025-01-18T09:15:00.000000Z"
    }
  ]
}
```

## Field reference

| Section | Field | Type | Description |
|--------|-------|------|-------------|
| **summary** | | object | Top KPI cards for employers/companies |
| | total_employers | { value, change_percent } | Total number of employer users |
| | active_companies | { value, change_percent } | Companies with `is_active = true` |
| | pending_verification | { value, change_percent } | Employers with `verified_at = null` |
| | suspended_banned | { value, change_percent } | Employer users with `is_active = false` |
| **filters** | status | string[] | Allowed values for `status` query param |
| **pagination** | | object | Standard pagination meta |
| | current_page | number | Current page number |
| | per_page | number | Items per page |
| | total | number | Total matching employers |
| | last_page | number | Last page number |
| **employers** | | array | Paginated list of employers/companies |
| | id | number | Internal employer ID (`employer_id`) |
| | company_name | string | Name of the employer's company |
| | contact.email | string\|null | Company email or employer user email |
| | contact.phone | string\|null | Company phone or employer user phone |
| | contact.location | string\|null | Company location |
| | job_ads.total | number | Total job ads for this company |
| | job_ads.last_posted_at | string\|null | Datetime of most recent job posting (ISO 8601) |
| | status | string | `"active"`, `"pending_verification"`, or `"suspended"` |
| | verification.kyc_status | string | `"verified"` or `"pending"` based on `verified_at` |
| **quick_actions** | | array | Static list of quick actions to show on the page |
| | key | string | Internal key used by the frontend |
| | label | string | Human readable label |
| **recent_activity** | | array | Recent employer related activity (job postings) |
| | type | string | Currently always `"job_posted"` |
| | message | string | Human readable description of the event |
| | created_at | string | Datetime of the event (ISO 8601) |

