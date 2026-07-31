# Admin: Financials Management API

This document describes the API for the **Admin Financials Management** dashboard: summary statistics (Total Revenue, This Month, Pending Payments), Revenue by Category (Job Ads, Tender Ads, Website Ads, Course Ads, Coins, LPO), and the transaction list with filters (All, Job Ads, Tender Ads, etc.).

All endpoints require admin authentication: `Authorization: Bearer <admin_sanctum_token>`.

**Base URL:** `/api/admin`

**Transaction categories (type filter):** `job_ads` | `tender_ads` | `website_ads` | `course_ads` | `coins` | `lpo`

---

## 1. Dashboard (all statistics + all transactions)

Returns everything needed to render the Financials page in one call: the three summary cards, the six revenue-by-category rows, and the first page of **all** transactions.

### Request

```
GET /api/admin/financials/dashboard
```

| Query param | Type | Description |
|-------------|------|-------------|
| `per_page`  | int  | Transactions per page (1–100, default 10). |
| `page`      | int  | Page number (default 1). |

### Response

**Status:** `200 OK`

```json
{
  "stats": {
    "total_revenue_all_time": 125410,
    "this_month_revenue": 37200,
    "pending_payments": 1598,
    "pending_count": 2,
    "currency": "SCR"
  },
  "revenue_by_category": [
    {
      "category": "job_ads",
      "label": "Job Ads",
      "total": 24850,
      "this_month": 8490
    },
    {
      "category": "tender_ads",
      "label": "Tender Ads",
      "total": 45230,
      "this_month": 15990
    },
    {
      "category": "website_ads",
      "label": "Website Ads",
      "total": 18940,
      "this_month": 5980
    },
    {
      "category": "course_ads",
      "label": "Course Ads",
      "total": 12450,
      "this_month": 3990
    },
    {
      "category": "coins",
      "label": "Coins",
      "total": 8940,
      "this_month": 2750
    },
    {
      "category": "lpo",
      "label": "LPO (Local Purchase Order)",
      "total": 15000,
      "this_month": 0
    }
  ],
  "transactions": [
    {
      "id": 1,
      "transaction_id": "TXN-001234",
      "category": "job_ads",
      "category_label": "Job Ads",
      "payer_name": "Tech Corp Inc",
      "description": "Featured Job Posting - Senior Developer",
      "payment_method": "credit_card",
      "payment_method_label": "Credit Card",
      "amount": 499,
      "currency": "SCR",
      "status": "completed",
      "date": "2006-03-10",
      "time": "10:30 AM",
      "datetime": "2006-03-10T10:30:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 10,
    "last_page": 1
  }
}
```

### User story (what to implement on the admin dashboard)

- **Summary cards:**  
  - **Total Revenue (All Time):** Show `stats.total_revenue_all_time` with `stats.currency` (e.g. "SCR 125,410").  
  - **This Month:** Show `stats.this_month_revenue` with currency (e.g. "SCR 37,200").  
  - **Pending Payments:** Show `stats.pending_payments` with currency and a badge/count of `stats.pending_count` (e.g. "SCR 1,598" and "2 pending").
- **Revenue by Category:** For each item in `revenue_by_category`, render a row/card with: icon (by `category`), `label`, "Total" = `total` in currency, "This month" = `this_month` in currency.
- **Transaction list (All Transactions):** Use `transactions` for the table. Each row: payer name, description, transaction ID, payment method label, date & time, amount with currency, status (e.g. green "Completed" or yellow "Pending"). Use `pagination` for "All Transactions (10)" and next/prev.
- **Initial load:** Call this endpoint once when the Financials page loads to populate summary, revenue by category, and the first page of all transactions.

---

## 2. Transactions – All (no filter)

Same as the transaction list returned by the dashboard, but as a dedicated endpoint so the dashboard can refresh only the list or load more pages without re-fetching stats.

### Request

```
GET /api/admin/financials/transactions
```

| Query param | Type | Description |
|-------------|------|-------------|
| `per_page` | int  | Items per page (1–100, default 10). |
| `page`     | int  | Page number. |
| `search`   | string | Search in transaction_id, payer_name, description. |
| `status`   | string | Filter by status: `completed`, `pending`, `failed`. |

Omit `type` (or use no category filter) to get **all** transactions.

### Response

**Status:** `200 OK`

```json
{
  "transactions": [
    {
      "id": 1,
      "transaction_id": "TXN-001234",
      "category": "job_ads",
      "category_label": "Job Ads",
      "payer_name": "Tech Corp Inc",
      "description": "Featured Job Posting - Senior Developer",
      "payment_method": "credit_card",
      "payment_method_label": "Credit Card",
      "amount": 499,
      "currency": "SCR",
      "status": "completed",
      "date": "2006-03-10",
      "time": "10:30 AM",
      "datetime": "2006-03-10T10:30:00.000000Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "per_page": 10,
    "total": 10,
    "last_page": 1
  }
}
```

### User story

- When the user has the **All Transactions** tab selected, call this endpoint (no `type`). Use the response to render the table and pagination. Optional: use `search` for "Search transactions..." and `status` if you add a status filter.

---

## 3. Transactions – Job Ads

Returns only transactions where the revenue category is **Job Ads** (e.g. job posting or job campaign payments).

### Request

```
GET /api/admin/financials/transactions?type=job_ads
```

| Query param | Type | Description |
|-------------|------|-------------|
| `type`     | string | **Required for this view:** `job_ads`. |
| `per_page` | int  | Items per page. |
| `page`     | int  | Page number. |
| `search`   | string | Search transaction_id, payer_name, description. |
| `status`   | string | completed | pending | failed. |

### Response

Same shape as **§ 2**; `transactions` array contains only rows with `category: "job_ads"`.

### User story

- When the user clicks the **Job Ads** filter tab, call `GET /api/admin/financials/transactions?type=job_ads`. Replace the current table with the returned list and show the count from `pagination.total` (e.g. "Job Ads (5)").

---

## 4. Transactions – Tender Ads

Returns only transactions for **Tender Ads**.

### Request

```
GET /api/admin/financials/transactions?type=tender_ads
```

| Query param | Type | Description |
|-------------|------|-------------|
| `type`     | string | **Required:** `tender_ads`. |
| `per_page`, `page`, `search`, `status` | — | Same as § 2. |

### Response

Same as § 2; only `category: "tender_ads"` items.

### User story

- When the user clicks the **Tender Ads** tab, call with `type=tender_ads` and show the list and count.

---

## 5. Transactions – Website Ads

Returns only transactions for **Website Ads** (e.g. homepage banner, website placements).

### Request

```
GET /api/admin/financials/transactions?type=website_ads
```

| Query param | Type | Description |
|-------------|------|-------------|
| `type`     | string | **Required:** `website_ads`. |
| Other params | — | Same as § 2. |

### Response

Same as § 2; only `category: "website_ads"` items.

### User story

- When the user clicks the **Website Ads** tab, call with `type=website_ads` and update the table and header count.

---

## 6. Transactions – Course Ads

Returns only transactions for **Course Ads** (e.g. featured course promotions).

### Request

```
GET /api/admin/financials/transactions?type=course_ads
```

| Query param | Type | Description |
|-------------|------|-------------|
| `type`     | string | **Required:** `course_ads`. |

### Response

Same as § 2; only `category: "course_ads"` items.

### User story

- When the user clicks the **Course Ads** tab, call with `type=course_ads` and update the list.

---

## 7. Transactions – Coins (deposits)

Returns only transactions for **Coins** (e.g. employer coin purchases/deposits).

### Request

```
GET /api/admin/financials/transactions?type=coins
```

| Query param | Type | Description |
|-------------|------|-------------|
| `type`     | string | **Required:** `coins`. |

### Response

Same as § 2; only `category: "coins"` items.

### User story

- When the user clicks the **Coins** tab, call with `type=coins` and show only coin/deposit transactions.

---

## 8. Transactions – LPO (Local Purchase Order)

Returns only transactions for **LPO** (Local Purchase Order).

### Request

```
GET /api/admin/financials/transactions?type=lpo
```

| Query param | Type | Description |
|-------------|------|-------------|
| `type`     | string | **Required:** `lpo`. |

### Response

Same as § 2; only `category: "lpo"` items.

### User story

- When the user clicks the **LPO** tab, call with `type=lpo` and show only LPO transactions.

---

## Transaction item shape (all list endpoints)

Every transaction in the `transactions` array has this structure:

| Field                 | Type   | Description |
|-----------------------|--------|-------------|
| `id`                  | int    | Internal payment ID. |
| `transaction_id`      | string | Display ID (e.g. TXN-001234). |
| `category`            | string | job_ads \| tender_ads \| website_ads \| course_ads \| coins \| lpo. |
| `category_label`      | string | Human-readable category name. |
| `payer_name`          | string | Company or payer name. |
| `description`         | string | e.g. "Featured Job Posting - Senior Developer". |
| `payment_method`      | string | credit_card, bank_transfer, lpo, coin. |
| `payment_method_label`| string | e.g. "Credit Card", "Bank Transfer". |
| `amount`              | float  | Numeric amount. |
| `currency`            | string | e.g. SCR. |
| `status`              | string | completed, pending, failed. |
| `date`                | string | Y-m-d. |
| `time`                | string | e.g. "10:30 AM". |
| `datetime`            | string | ISO 8601. |

---

## Summary: endpoints and when to use them

| Purpose                         | Method | Endpoint | User story |
|---------------------------------|--------|----------|------------|
| Full financials page load       | GET    | `/api/admin/financials/dashboard` | Load summary cards, revenue by category, and first page of all transactions. |
| All transactions (tab or refresh) | GET  | `/api/admin/financials/transactions` | Show all transactions; use when "All Transactions" is selected. |
| Job Ads only                    | GET    | `/api/admin/financials/transactions?type=job_ads` | When user selects "Job Ads" tab. |
| Tender Ads only                 | GET    | `/api/admin/financials/transactions?type=tender_ads` | When user selects "Tender Ads" tab. |
| Website Ads only                | GET    | `/api/admin/financials/transactions?type=website_ads` | When user selects "Website Ads" tab. |
| Course Ads only                 | GET    | `/api/admin/financials/transactions?type=course_ads` | When user selects "Course Ads" tab. |
| Coins (deposits) only           | GET    | `/api/admin/financials/transactions?type=coins` | When user selects "Coins" tab. |
| LPO only                         | GET    | `/api/admin/financials/transactions?type=lpo` | When user selects "LPO" tab. |

**Search and filter:** For any of the transaction endpoints, send `search` for the "Search transactions..." input and `status` if you add a status dropdown. Use `page` and `per_page` for pagination. **Download:** Use the same endpoint with a larger `per_page` (or loop pages) and generate the file on the client, or add a dedicated export endpoint later.
