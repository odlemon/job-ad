# Admin Advertisements Data Endpoints

These endpoints return **job campaigns (job ads)** and/or **tender ads** for the admin dashboard. All require admin authentication (`Authorization: Bearer <token>` with a valid admin Sanctum token).

**Base URL:** `/api/admin` (e.g. `https://your-domain.com/api/admin`)

---

## 1. Combined: Job campaigns + Tender ads

Returns both job campaigns (job ads from all employers) and tender ads (from all employers) in a single response.

### Request

```
GET /api/admin/advertisements/all
```

**Query parameters (optional):**

| Parameter | Type   | Description                                      |
|-----------|--------|--------------------------------------------------|
| `search`  | string | Filter by title, company name, or entity/reference (tenders) |
| `status`  | string | For job ads: `active`, `draft`, `closed`. For tenders: `active`, `pending_approval`, `rejected`, `expired` |

### Response

**Status:** `200 OK`

```json
{
  "job_campaigns": [
    {
      "id": 1,
      "title": "Senior Software Engineer",
      "slug": "senior-software-engineer",
      "status": "published",
      "company_id": 5,
      "company": {
        "id": 5,
        "name": "Acme Corp"
      },
      "category_id": 2,
      "category": {
        "id": 2,
        "name": "Technology"
      },
      "location": "Victoria",
      "employment_type": "full_time",
      "views_count": 120,
      "applications_count": 8,
      "published_at": "2025-02-01T10:00:00.000000Z",
      "created_at": "2025-01-28T14:00:00.000000Z",
      "updated_at": "2025-02-01T10:00:00.000000Z",
      "campaigns": [
        {
          "id": 3,
          "campaign_type_id": 1,
          "campaign_type": {
            "id": 1,
            "name": "Featured"
          },
          "status": "active",
          "duration_days": 14,
          "launched_at": "2025-02-01T10:00:00.000000Z",
          "ends_at": "2025-02-15T10:00:00.000000Z",
          "views_count": 80,
          "clicks_count": 12
        }
      ]
    }
  ],
  "tender_ads": [
    {
      "id": 2,
      "title": "IT Infrastructure Upgrade",
      "slug": "it-infrastructure-upgrade",
      "reference_number": "REF-2025-002",
      "tender_type": "RFP",
      "status": "active",
      "entity_name": "Ministry of ICT",
      "category_id": 2,
      "category": {
        "id": 2,
        "name": "Technology"
      },
      "location": "Victoria",
      "views_count": 45,
      "applications_count": 3,
      "submission_deadline": "2025-03-15",
      "created_by": 10,
      "creator": {
        "id": 10,
        "name": "Jane Employer"
      },
      "created_at": "2025-02-10T09:00:00.000000Z",
      "updated_at": "2025-02-10T09:00:00.000000Z"
    }
  ],
  "meta": {
    "job_campaigns_count": 1,
    "tender_ads_count": 1
  }
}
```

---

## 2. Job campaigns (job ads) only

Returns only job advertisements (job campaigns) from all employers, with optional pagination and filters.

### Request

```
GET /api/admin/advertisements/job-campaigns
```

**Query parameters (optional):**

| Parameter  | Type   | Description                                                |
|------------|--------|------------------------------------------------------------|
| `search`   | string | Filter by job title or company name                        |
| `status`   | string | `active` (published), `draft`, or `closed`                 |
| `per_page` | int    | Items per page (1–100, default 15)                         |
| `page`     | int    | Page number (default 1)                                    |

### Response

**Status:** `200 OK`

```json
{
  "job_campaigns": [
    {
      "id": 1,
      "title": "Senior Software Engineer",
      "slug": "senior-software-engineer",
      "status": "published",
      "company_id": 5,
      "company": {
        "id": 5,
        "name": "Acme Corp"
      },
      "category_id": 2,
      "category": {
        "id": 2,
        "name": "Technology"
      },
      "location": "Victoria",
      "employment_type": "full_time",
      "views_count": 120,
      "applications_count": 8,
      "published_at": "2025-02-01T10:00:00.000000Z",
      "created_at": "2025-01-28T14:00:00.000000Z",
      "updated_at": "2025-02-01T10:00:00.000000Z",
      "campaigns": [
        {
          "id": 3,
          "campaign_type_id": 1,
          "campaign_type": {
            "id": 1,
            "name": "Featured"
          },
          "status": "active",
          "duration_days": 14,
          "launched_at": "2025-02-01T10:00:00.000000Z",
          "ends_at": "2025-02-15T10:00:00.000000Z",
          "views_count": 80,
          "clicks_count": 12
        }
      ]
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 42,
    "last_page": 3
  }
}
```

---

## 3. Tender ads only

Returns only tender advertisements from all employers. This is the existing tenders list endpoint.

### Request

```
GET /api/admin/tenders
```

**Query parameters (optional):**

| Parameter  | Type   | Description                                |
|------------|--------|--------------------------------------------|
| `search`   | string | Filter by title, entity name, or reference |
| `status`   | string | `active`, `pending_approval`, `rejected`, `expired` |
| `per_page` | int    | Items per page (1–100, default 15)         |

### Response

**Status:** `200 OK`

```json
{
  "tenders": [
    {
      "id": 2,
      "title": "IT Infrastructure Upgrade",
      "slug": "it-infrastructure-upgrade",
      "reference_number": "REF-2025-002",
      "tender_type": "RFP",
      "category_id": 2,
      "category": {
        "id": 2,
        "name": "Technology"
      },
      "status": "active",
      "overview": {
        "description": "Full description of the tender...",
        "summary": "Brief summary",
        "scope_of_work": "Scope details",
        "requirements": ["Requirement 1", "Requirement 2"]
      },
      "tender_information": {
        "budget_range": "$50,000 - $100,000",
        "budget_min": 50000,
        "budget_max": 100000,
        "currency": "USD",
        "sector": "ICT",
        "procuring_entity": "Ministry of ICT",
        "entity_name": "Ministry of ICT",
        "country_region": "Seychelles",
        "location": "Victoria"
      },
      "submission_details": {
        "submission_method": "Online portal",
        "required_documents": ["Company registration", "Tax certificate"],
        "eligibility_criteria": ["Registered company", "5+ years experience"]
      },
      "attachments": [
        {
          "name": "Specification.pdf",
          "url": "https://example.com/uploads/tender-documents/spec.pdf",
          "type": "PDF",
          "size": "1.2 MB"
        }
      ],
      "important_dates": {
        "published_date": "2025-02-01",
        "clarification_deadline": "2025-03-01",
        "submission_deadline": "2025-03-15",
        "start_date": null,
        "end_date": null
      },
      "performance": {
        "views_count": 45,
        "applications_count": 3
      },
      "created_by": 10,
      "creator": {
        "id": 10,
        "name": "Jane Employer"
      },
      "created_at": "2025-02-10T09:00:00.000000Z",
      "updated_at": "2025-02-10T09:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 12,
    "last_page": 1
  }
}
```

---

## 4. Job campaigns dashboard (stats + status counts + job list)

Returns data for the admin **Job Campaigns** screen: summary stats (Active Job Listing, Total Views, Clicks, Applications, Shares, Saved), status tab counts (Ad Approval, Flagged, All Ads, Scheduled, Active, Paused, Expired, Draft), and a filterable, paginated list of job ads for the table.

### Request

```
GET /api/admin/advertisements/job-campaigns/dashboard
```

**Query parameters (optional):**

| Parameter  | Type   | Description                                                                 |
|------------|--------|-----------------------------------------------------------------------------|
| `status`   | string | Tab filter: `ad_approval`, `flagged`, `all_ads`, `scheduled`, `active`, `paused`, `expired`, `draft` (default `all_ads`) |
| `search`   | string | Filter by job title or company name                                         |
| `posted_by`| int    | Company ID (filter by employer)                                             |
| `location` | string | Filter by location (partial match)                                          |
| `per_page` | int    | Items per page for the jobs list (1–100, default 10)                        |
| `page`     | int    | Page number for the jobs list (default 1)                                   |

### Response

**Status:** `200 OK`

```json
{
  "stats": {
    "active_job_listings": 2,
    "total_views": 150,
    "total_clicks": 10,
    "applications": 17,
    "shares": 4,
    "saved": 20
  },
  "status_counts": {
    "ad_approval": 2,
    "flagged": 0,
    "all_ads": 8,
    "scheduled": 1,
    "active": 2,
    "paused": 0,
    "expired": 1,
    "draft": 1
  },
  "jobs": [
    {
      "id": 1,
      "title": "Senior Software Engineer",
      "slug": "senior-software-engineer",
      "company": {
        "id": 5,
        "name": "Tech Innovators Inc."
      },
      "location": "Multiple locations",
      "campaign_type": "Featured",
      "posted_at": "2025-01-10T08:00:00.000000Z",
      "posted_by": "Tech Innovators Inc.",
      "expiring_at": "2025-01-17T08:00:00.000000Z",
      "display_status": "ad_approval",
      "status": "draft",
      "views_count": 0,
      "applications_count": 0
    },
    {
      "id": 2,
      "title": "Marketing Specialist",
      "slug": "marketing-specialist",
      "company": {
        "id": 7,
        "name": "Creative Agency Ltd."
      },
      "location": "Los Angeles, CA",
      "campaign_type": "Featured",
      "posted_at": "2025-01-14T09:00:00.000000Z",
      "posted_by": "Creative Agency Ltd.",
      "expiring_at": "2025-01-21T09:00:00.000000Z",
      "display_status": "ad_approval",
      "status": "draft",
      "views_count": 0,
      "applications_count": 0
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 2,
    "last_page": 1
  }
}
```

**Field notes:**

- **stats**: Totals across all job ads and campaigns (views/clicks from both `JobAdvertisement` and `JobCampaign`; applications from job ads; shares/saved from campaigns).
- **status_counts**: Counts for each tab. `ad_approval` = draft job ads not yet published; `flagged` = placeholder (0 until a flag feature exists); `all_ads` = total job ads; `scheduled` = campaigns with `launched_at` in the future; `active` = published, not closed; `paused` = campaigns with status `paused`; `expired` = closed job ads or campaigns with `ends_at` in the past; `draft` = draft job ads.
- **jobs**: Each item includes `display_status` for the tab it belongs to (`ad_approval`, `active`, `scheduled`, `paused`, `expired`, `draft`), `campaign_type` (e.g. "Featured"), `posted_at` / `posted_by` / `expiring_at` for the card subtitle and "View & Approve" use.

---

## Summary

| Endpoint | Returns |
|----------|---------|
| `GET /api/admin/advertisements/all` | **Job campaigns** (job ads) + **Tender ads** in one response |
| `GET /api/admin/advertisements/job-campaigns` | **Job campaigns** (job ads) only, with pagination |
| `GET /api/admin/advertisements/job-campaigns/dashboard` | **Job campaigns dashboard**: stats, status_counts, and filterable job list |
| `GET /api/admin/tenders` | **Tender ads** only, with pagination and full detail |

All endpoints require authentication: `Authorization: Bearer <admin_sanctum_token>`.
