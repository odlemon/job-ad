# Advertisements Management API

Advertisements endpoints: overview (job ads + tender ads), admin tender fetch, and public tenders.

Admin auth: `Authorization: Bearer <token>`.

---

## 1. Advertisements overview

**`GET /api/admin/advertisements/overview`**

| Query (optional) | Description |
|------------------|-------------|
| `type` | `all`, `job_ads`, `tender_ads` |
| `status` | `all`, `active`, `pending_approval`, `expired` |
| `search` | Title, company/entity, reference |
| `page` | Default `1` |
| `per_page` | Default `15`, max `100` |

**Response — 200 OK**

```json
{
  "summary": {
    "total_ads": { "value": 1721, "change_percent": 0 },
    "active_job_ads": { "value": 1289, "change_percent": 5.2 },
    "active_tender_ads": { "value": 432, "change_percent": -2.1 },
    "pending_approval": { "value": 67, "change_percent": 0 }
  },
  "filters": {
    "type": ["all", "job_ads", "tender_ads"],
    "status": ["all", "active", "pending_approval", "expired"]
  },
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 1721,
    "last_page": 115
  },
  "advertisements": [
    {
      "id": 1,
      "type": "job_ad",
      "title": "Senior React Developer",
      "company_or_entity": "Tech Corp Inc",
      "category": "Technology",
      "views_count": 1234,
      "applications_count": 45,
      "start_date": "2025-01-10",
      "end_date": null,
      "amount": null,
      "currency": null,
      "status": "active",
      "created_at": "2025-01-10T09:00:00.000000Z"
    },
    {
      "id": 2,
      "type": "tender_ad",
      "title": "Construction Project Tender",
      "company_or_entity": "BuildCo Ltd",
      "category": "Construction",
      "views_count": 567,
      "applications_count": 12,
      "start_date": "2025-01-10",
      "end_date": "2025-02-10",
      "amount": 499000,
      "currency": "USD",
      "status": "active",
      "created_at": "2025-01-09T14:00:00.000000Z"
    }
  ]
}
```

---

## 2. List tenders (admin)

**`GET /api/admin/tenders`**

| Query (optional) | Description |
|------------------|-------------|
| `status` | `draft`, `pending_approval`, `active`, `expired` |
| `search` | Title, entity name, reference number |
| `page` | Page number |
| `per_page` | Default `15`, max `100` |

**Response — 200 OK**

```json
{
  "tenders": [
    {
      "id": 1,
      "title": "Supply of Medical Equipment and Supplies",
      "slug": "supply-of-medical-equipment-and-supplies",
      "reference_number": "TND-2026-003-MED",
      "tender_type": "RFQ",
      "category_id": 2,
      "category": { "id": 2, "name": "Healthcare" },
      "status": "active",
      "overview": {
        "description": "Procurement of medical equipment and consumables for regional health facilities.",
        "summary": "Procurement of essential medical equipment and consumables for 15 county health facilities.",
        "scope_of_work": "Supply of various medical equipment including patient monitors, ultrasound machines, laboratory equipment, surgical instruments, and consumables. All items must meet international quality standards.",
        "requirements": [
          "Licensed medical equipment supplier",
          "WHO/FDA approved products",
          "Local presence or authorized distributor"
        ]
      },
      "tender_information": {
        "budget_range": "$1,200,000 - $1,500,000",
        "budget_min": 1200000,
        "budget_max": 1500000,
        "currency": "USD",
        "sector": "Medical Supplies",
        "procuring_entity": "Kenya Medical Supplies Authority",
        "entity_name": "Ministry of Health",
        "country_region": "Kenya",
        "location": "Multiple Counties, Kenya"
      },
      "submission_details": {
        "submission_method": "Online portal submission only",
        "required_documents": [
          "Product catalogues and specifications",
          "Price quotations",
          "Authorization letters from manufacturers",
          "Quality certificates",
          "Delivery schedule"
        ],
        "eligibility_criteria": [
          "Licensed medical equipment supplier",
          "WHO/FDA approved products",
          "Local presence or authorized distributor",
          "After-sales service capability"
        ]
      },
      "attachments": [
        { "name": "Equipment_List.pdf", "type": "PDF", "size": "1.1 MB", "url": "/documents/tenders/equipment-list.pdf" },
        { "name": "Technical_Requirements.pdf", "type": "PDF", "size": "980 KB", "url": "/documents/tenders/technical-requirements.pdf" },
        { "name": "Delivery_Schedule.xlsx", "type": "Excel", "size": "234 KB", "url": "/documents/tenders/delivery-schedule.xlsx" }
      ],
      "important_dates": {
        "published_date": "2026-01-18",
        "clarification_deadline": "2026-02-15",
        "submission_deadline": "2026-02-28",
        "start_date": "2026-01-18",
        "end_date": "2026-02-28"
      },
      "performance": {
        "views_count": 342,
        "applications_count": 18
      },
      "created_by": null,
      "creator": null,
      "created_at": "2026-01-18T00:00:00.000000Z",
      "updated_at": "2026-01-18T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 7,
    "last_page": 1
  }
}
```

---

## 3. Get one tender (admin)

**`GET /api/admin/tenders/{id}`**

**Response — 200 OK**

```json
{
  "data": {
    "id": 1,
    "title": "Supply of Medical Equipment and Supplies",
    "slug": "supply-of-medical-equipment-and-supplies",
    "reference_number": "TND-2026-003-MED",
    "tender_type": "RFQ",
    "category_id": 2,
    "category": { "id": 2, "name": "Healthcare" },
    "status": "active",
    "overview": {
      "description": "Procurement of medical equipment and consumables for regional health facilities. Bidders must be registered suppliers with relevant certifications.",
      "summary": "Procurement of essential medical equipment and consumables for 15 county health facilities.",
      "scope_of_work": "Supply of various medical equipment including patient monitors, ultrasound machines, laboratory equipment, surgical instruments, and consumables. All items must meet international quality standards.",
      "requirements": [
        "Licensed medical equipment supplier",
        "WHO/FDA approved products",
        "Local presence or authorized distributor"
      ]
    },
    "tender_information": {
      "budget_range": "$1,200,000 - $1,500,000",
      "budget_min": 1200000,
      "budget_max": 1500000,
      "currency": "USD",
      "sector": "Medical Supplies",
      "procuring_entity": "Kenya Medical Supplies Authority",
      "entity_name": "Ministry of Health",
      "country_region": "Kenya",
      "location": "Multiple Counties, Kenya"
    },
    "submission_details": {
      "submission_method": "Online portal submission only",
      "required_documents": [
        "Product catalogues and specifications",
        "Price quotations",
        "Authorization letters from manufacturers",
        "Quality certificates",
        "Delivery schedule"
      ],
      "eligibility_criteria": [
        "Licensed medical equipment supplier",
        "WHO/FDA approved products",
        "Local presence or authorized distributor",
        "After-sales service capability"
      ]
    },
    "attachments": [
      { "name": "Equipment_List.pdf", "type": "PDF", "size": "1.1 MB", "url": "/documents/tenders/equipment-list.pdf" },
      { "name": "Technical_Requirements.pdf", "type": "PDF", "size": "980 KB", "url": "/documents/tenders/technical-requirements.pdf" },
      { "name": "Delivery_Schedule.xlsx", "type": "Excel", "size": "234 KB", "url": "/documents/tenders/delivery-schedule.xlsx" }
    ],
    "important_dates": {
      "published_date": "2026-01-18",
      "clarification_deadline": "2026-02-15",
      "submission_deadline": "2026-02-28",
      "start_date": "2026-01-18",
      "end_date": "2026-02-28"
    },
    "performance": {
      "views_count": 342,
      "applications_count": 18
    },
    "created_by": null,
    "creator": null,
    "created_at": "2026-01-18T00:00:00.000000Z",
    "updated_at": "2026-01-18T00:00:00.000000Z"
  }
}
```

**Response — 404 Not Found**

```json
{
  "message": "Tender not found"
}
```

---

## 4. Approve tender (admin)

**`PUT /api/admin/tenders/{id}/approve`**

Changes the tender status to `active`. Only tenders not already `active` can be approved.

**Response — 200 OK**

```json
{
  "message": "Tender approved",
  "data": {
    "id": 1,
    "title": "Supply of Medical Equipment and Supplies",
    "slug": "supply-of-medical-equipment-and-supplies",
    "reference_number": "TND-2026-003-MED",
    "tender_type": "RFQ",
    "category_id": 2,
    "category": { "id": 2, "name": "Healthcare" },
    "status": "active",
    "overview": {
      "description": "Procurement of medical equipment and consumables for regional health facilities.",
      "summary": "Procurement of essential medical equipment and consumables for 15 county health facilities.",
      "scope_of_work": "Supply of various medical equipment including patient monitors, ultrasound machines, laboratory equipment, surgical instruments, and consumables.",
      "requirements": [
        "Licensed medical equipment supplier",
        "WHO/FDA approved products",
        "Local presence or authorized distributor"
      ]
    },
    "tender_information": {
      "budget_range": "$1,200,000 - $1,500,000",
      "budget_min": 1200000,
      "budget_max": 1500000,
      "currency": "USD",
      "sector": "Medical Supplies",
      "procuring_entity": "Kenya Medical Supplies Authority",
      "entity_name": "Ministry of Health",
      "country_region": "Kenya",
      "location": "Multiple Counties, Kenya"
    },
    "submission_details": {
      "submission_method": "Online portal submission only",
      "required_documents": ["Product catalogues and specifications", "Price quotations", "Authorization letters from manufacturers", "Quality certificates", "Delivery schedule"],
      "eligibility_criteria": ["Licensed medical equipment supplier", "WHO/FDA approved products", "Local presence or authorized distributor", "After-sales service capability"]
    },
    "attachments": [
      { "name": "Equipment_List.pdf", "type": "PDF", "size": "1.1 MB", "url": "/documents/tenders/equipment-list.pdf" }
    ],
    "important_dates": {
      "published_date": "2026-01-18",
      "clarification_deadline": "2026-02-15",
      "submission_deadline": "2026-02-28",
      "start_date": "2026-01-18",
      "end_date": "2026-02-28"
    },
    "performance": { "views_count": 342, "applications_count": 18 },
    "created_by": null,
    "creator": null,
    "created_at": "2026-01-18T00:00:00.000000Z",
    "updated_at": "2026-03-03T12:00:00.000000Z"
  }
}
```

**Response — 422 (already active)**

```json
{
  "message": "Tender is already active"
}
```

**Response — 404**

```json
{
  "message": "Tender not found"
}
```

---

## 5. Reject tender (admin)

**`PUT /api/admin/tenders/{id}/reject`**

Changes the tender status to `rejected`. Optional `reason` field in the body.

**Body (optional):**

| Field    | Type   | Description |
|----------|--------|-------------|
| `reason` | string | Reason for rejection (logged in admin activity) |

**Response — 200 OK**

```json
{
  "message": "Tender rejected",
  "data": {
    "id": 6,
    "title": "Cleaning and Facility Management Services",
    "slug": "cleaning-and-facility-management-services",
    "reference_number": "TND-2026-024-CFM",
    "tender_type": "EOI",
    "category_id": 2,
    "category": { "id": 2, "name": "Healthcare" },
    "status": "rejected",
    "overview": {
      "description": "Provision of cleaning, pest control, and general facility management services for government buildings over a 3-year period.",
      "summary": "Comprehensive cleaning and facility management for 8 government office buildings in Central Province.",
      "scope_of_work": "Daily cleaning of offices, washrooms, and common areas. Weekly deep cleaning and carpet shampooing. Monthly pest control treatments.",
      "requirements": ["Registered facility management company", "Minimum 3 years experience in similar contracts", "Adequate staffing capacity"]
    },
    "tender_information": {
      "budget_range": "$320,000 - $400,000",
      "budget_min": 320000,
      "budget_max": 400000,
      "currency": "USD",
      "sector": "Facility Services",
      "procuring_entity": "State Department for Public Works",
      "entity_name": "State Department for Public Works",
      "country_region": "Kenya",
      "location": "Central Province, Kenya"
    },
    "submission_details": {
      "submission_method": "Physical and electronic submission",
      "required_documents": ["Company registration documents", "List of current and past contracts", "Staff deployment plan", "Equipment and chemicals inventory", "Health and safety policy"],
      "eligibility_criteria": ["Registered with relevant regulatory body", "At least 3 active facility management contracts", "Staff trained in occupational health and safety", "Public liability insurance"]
    },
    "attachments": [
      { "name": "Building_Specifications.pdf", "type": "PDF", "size": "1.8 MB", "url": "/documents/tenders/building-specifications.pdf" }
    ],
    "important_dates": {
      "published_date": "2026-03-01",
      "clarification_deadline": "2026-03-20",
      "submission_deadline": "2026-03-30",
      "start_date": "2026-03-01",
      "end_date": "2026-03-30"
    },
    "performance": { "views_count": 95, "applications_count": 0 },
    "created_by": null,
    "creator": null,
    "created_at": "2026-03-01T00:00:00.000000Z",
    "updated_at": "2026-03-03T12:00:00.000000Z"
  }
}
```

**Response — 422 (already rejected)**

```json
{
  "message": "Tender is already rejected"
}
```

**Response — 404**

```json
{
  "message": "Tender not found"
}
```

---

## 6. List active tenders (public, no auth)

**`GET /api/tenders`**

| Query (optional) | Description |
|------------------|-------------|
| `search` | Title, entity, reference, description |
| `category_id` | Category id |
| `page` | Page number |
| `per_page` | Default `15`, max `50` |

**Response — 200 OK**

```json
{
  "tenders": [
    {
      "id": 1,
      "title": "Supply of Medical Equipment and Supplies",
      "slug": "supply-of-medical-equipment-and-supplies",
      "description": "Procurement of medical equipment and consumables for regional health facilities.",
      "summary": "Procurement of essential medical equipment and consumables for 15 county health facilities.",
      "reference_number": "TND-2026-003-MED",
      "tender_type": "RFQ",
      "category": { "id": 2, "name": "Healthcare" },
      "entity_name": "Ministry of Health",
      "sector": "Medical Supplies",
      "procuring_entity": "Kenya Medical Supplies Authority",
      "country_region": "Kenya",
      "location": "Multiple Counties, Kenya",
      "budget_range": "$1,200,000 - $1,500,000",
      "currency": "USD",
      "submission_deadline": "2026-02-28",
      "views_count": 342,
      "created_at": "2026-01-18T00:00:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 15,
    "total": 5,
    "last_page": 1
  }
}
```

---

## 7. Get one tender by id or slug (public, no auth)

**`GET /api/tenders/{idOrSlug}`**

View count is incremented on each request.

**Response — 200 OK**

```json
{
  "data": {
    "id": 1,
    "title": "Supply of Medical Equipment and Supplies",
    "slug": "supply-of-medical-equipment-and-supplies",
    "reference_number": "TND-2026-003-MED",
    "tender_type": "RFQ",
    "category": { "id": 2, "name": "Healthcare" },
    "status": "active",
    "overview": {
      "description": "Procurement of medical equipment and consumables for regional health facilities. Bidders must be registered suppliers with relevant certifications.",
      "summary": "Procurement of essential medical equipment and consumables for 15 county health facilities.",
      "scope_of_work": "Supply of various medical equipment including patient monitors, ultrasound machines, laboratory equipment, surgical instruments, and consumables. All items must meet international quality standards.",
      "requirements": [
        "Licensed medical equipment supplier",
        "WHO/FDA approved products",
        "Local presence or authorized distributor"
      ]
    },
    "tender_information": {
      "budget_range": "$1,200,000 - $1,500,000",
      "budget_min": 1200000,
      "budget_max": 1500000,
      "currency": "USD",
      "sector": "Medical Supplies",
      "procuring_entity": "Kenya Medical Supplies Authority",
      "entity_name": "Ministry of Health",
      "country_region": "Kenya",
      "location": "Multiple Counties, Kenya"
    },
    "submission_details": {
      "submission_method": "Online portal submission only",
      "required_documents": [
        "Product catalogues and specifications",
        "Price quotations",
        "Authorization letters from manufacturers",
        "Quality certificates",
        "Delivery schedule"
      ],
      "eligibility_criteria": [
        "Licensed medical equipment supplier",
        "WHO/FDA approved products",
        "Local presence or authorized distributor",
        "After-sales service capability"
      ]
    },
    "attachments": [
      { "name": "Equipment_List.pdf", "type": "PDF", "size": "1.1 MB", "url": "/documents/tenders/equipment-list.pdf" },
      { "name": "Technical_Requirements.pdf", "type": "PDF", "size": "980 KB", "url": "/documents/tenders/technical-requirements.pdf" },
      { "name": "Delivery_Schedule.xlsx", "type": "Excel", "size": "234 KB", "url": "/documents/tenders/delivery-schedule.xlsx" }
    ],
    "important_dates": {
      "published_date": "2026-01-18",
      "clarification_deadline": "2026-02-15",
      "submission_deadline": "2026-02-28",
      "start_date": "2026-01-18",
      "end_date": "2026-02-28"
    },
    "views_count": 343,
    "created_at": "2026-01-18T00:00:00.000000Z",
    "updated_at": "2026-01-18T00:00:00.000000Z"
  }
}
```

**Response — 404 Not Found**

```json
{
  "message": "Tender not found"
}
```

---

## Summary

| # | Endpoint | Auth |
|---|----------|------|
| 1 | `GET /api/admin/advertisements/overview` | Admin |
| 2 | `GET /api/admin/tenders` | Admin |
| 3 | `GET /api/admin/tenders/{id}` | Admin |
| 4 | `PUT /api/admin/tenders/{id}/approve` | Admin |
| 5 | `PUT /api/admin/tenders/{id}/reject` | Admin |
| 6 | `GET /api/tenders` | None |
| 7 | `GET /api/tenders/{idOrSlug}` | None |
