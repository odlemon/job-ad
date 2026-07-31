# Admin: Coin Management API

This document describes the API for the **Admin Coins Management** dashboard: summary statistics (Total Coins Sold, Coins in Circulation, Active Packages, Revenue This Month), the **Packages** tab (list, Add Package, Edit, delete, toggle status). These packages are the ones shown to employers when they run job campaigns (purchase coins).

All endpoints require admin authentication: `Authorization: Bearer <admin_sanctum_token>`.

**Base URL:** `/api/admin`

---

## 1. Dashboard (coin management stats)

Returns the four summary metrics for the top cards. Call this when the Coin Management page loads.

### Request

```
GET /api/admin/coins/dashboard
```

No query parameters.

### Response

**Status:** `200 OK`

```json
{
  "stats": {
    "total_coins_sold": 124500,
    "coins_in_circulation": 45230,
    "employers_with_coins": 784,
    "active_packages": 4,
    "total_packages": 4,
    "revenue_this_month": 48950,
    "currency": "SCR"
  }
}
```

| Field | Type | Description |
|-------|------|-------------|
| `total_coins_sold` | int | Total number of coins ever sold (from completed coin payments). |
| `coins_in_circulation` | int | Sum of all employers’ current coin balances. |
| `employers_with_coins` | int | Number of employers with balance > 0 (e.g. for “784 employers” subtitle). |
| `active_packages` | int | Number of packages with status `active`. |
| `total_packages` | int | Total number of packages. |
| `revenue_this_month` | float | Revenue from completed coin payments in the current month (SCR). |
| `currency` | string | e.g. `SCR`. |

### User story (what to implement on the admin dashboard)

- **Total Coins Sold card:** Show `stats.total_coins_sold` (e.g. 124,500). Optionally show a trend % (e.g. +15.3%) if you compute it elsewhere.
- **Coins in Circulation card:** Show `stats.coins_in_circulation` (e.g. 45,230) and below it “`stats.employers_with_coins` employers”.
- **Active Packages card:** Show `stats.active_packages` (e.g. 4) and “All enabled” or “X of Y enabled” using `stats.total_packages`.
- **Revenue (This Month) card:** Show `stats.currency` and `stats.revenue_this_month` (e.g. “SCR 48,950”). Optionally show a trend % (e.g. +22.8%).

---

## 2. List packages (Packages tab)

Returns all coin packages for the Packages tab. Used to render the package cards (name, coins, price, description, status, edit icon).

### Request

```
GET /api/admin/coins/packages
```

| Query param | Type | Description |
|-------------|------|-------------|
| `status` | string | Optional. `active` or `inactive` to filter. |

### Response

**Status:** `200 OK`

```json
{
  "packages": [
    {
      "id": 1,
      "name": "Starter Pack",
      "coins_amount": 50,
      "price": 250,
      "currency": "SCR",
      "description": "Perfect for small businesses starting their recruitment journey",
      "status": "active",
      "sort_order": 1,
      "icon": null,
      "created_at": "2026-01-01T00:00:00.000000Z",
      "updated_at": "2026-01-01T00:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Business Pack",
      "coins_amount": 150,
      "price": 600,
      "currency": "SCR",
      "description": "Best value for growing companies with regular hiring needs",
      "status": "active",
      "sort_order": 2,
      "icon": null,
      "created_at": "2026-01-01T00:00:00.000000Z",
      "updated_at": "2026-01-01T00:00:00.000000Z"
    }
  ]
}
```

### User story

- When the user opens the **Packages** tab (or the Coin Management page with Packages active), call `GET /api/admin/coins/packages`.
- Render each item as a card: **name**, **coins_amount** (e.g. “50”), **price** with **currency** (e.g. “SCR 250.00”), **description**, **status** badge (e.g. green “Active” when `status === 'active'`).
- Show an **edit** icon on each card that opens the edit modal/form for that package (`id`).
- **+ Add Package** button opens the create form; submit uses the Create package endpoint (§ 4).

---

## 3. Get one package (for edit form or detail)

Returns a single package by id. Use when opening the Edit modal to pre-fill the form.

### Request

```
GET /api/admin/coins/packages/{id}
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | int | Package ID. |

### Response

**Status:** `200 OK`

```json
{
  "package": {
    "id": 1,
    "name": "Starter Pack",
    "coins_amount": 50,
    "price": 250,
    "currency": "SCR",
    "description": "Perfect for small businesses starting their recruitment journey",
    "status": "active",
    "sort_order": 1,
    "icon": null,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z"
  }
}
```

**Error:** `404` — Package not found.

### User story

- When the user clicks the **edit** icon on a package card, call `GET /api/admin/coins/packages/{id}` and open the Edit modal with the returned `package` (name, coins_amount, price, currency, description, status, sort_order, icon).

---

## 4. Create package (Add Package)

Creates a new coin package. These packages are shown to employers when they purchase coins for job campaigns.

### Request

```
POST /api/admin/coins/packages
```

**Body (JSON):**

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Package name (e.g. “Starter Pack”). |
| `coins_amount` | int | Yes | Number of coins in the package (min 1). |
| `price` | number | Yes | Price (min 0). |
| `currency` | string | No | Default `SCR`. |
| `description` | string | No | Short description for the card. |
| `status` | string | No | `active` or `inactive`. Default `active`. |
| `sort_order` | int | No | Display order (default 0). |
| `icon` | string | No | Optional icon identifier. |

**Example:**

```json
{
  "name": "Starter Pack",
  "coins_amount": 50,
  "price": 250,
  "currency": "SCR",
  "description": "Perfect for small businesses starting their recruitment journey",
  "status": "active",
  "sort_order": 1
}
```

### Response

**Status:** `201 Created`

```json
{
  "message": "Package created",
  "package": {
    "id": 1,
    "name": "Starter Pack",
    "coins_amount": 50,
    "price": 250,
    "currency": "SCR",
    "description": "Perfect for small businesses starting their recruitment journey",
    "status": "active",
    "sort_order": 1,
    "icon": null,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-01T00:00:00.000000Z"
  }
}
```

### User story

- When the user clicks **+ Add Package**, open a form with: Name (required), Coins amount (required), Price (required), Currency (default SCR), Description, Status (dropdown: Active / Inactive), Sort order (optional).
- On submit, send `POST /api/admin/coins/packages` with the form data.
- On success: show “Package created”, close the modal, and refresh the package list (call `GET /api/admin/coins/packages`) so the new package card appears.

---

## 5. Update package (Edit)

Updates an existing coin package.

### Request

```
PUT /api/admin/coins/packages/{id}
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | int | Package ID. |

**Body (JSON):** Same fields as Create; all optional (only send fields that change). Use `sometimes|required` semantics: if a field is sent, it is validated.

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Package name. |
| `coins_amount` | int | Number of coins. |
| `price` | number | Price. |
| `currency` | string | Currency. |
| `description` | string | Description. |
| `status` | string | `active` or `inactive`. |
| `sort_order` | int | Display order. |
| `icon` | string | Icon identifier. |

**Example:**

```json
{
  "name": "Starter Pack (Updated)",
  "price": 275,
  "description": "Updated description"
}
```

### Response

**Status:** `200 OK`

```json
{
  "message": "Package updated",
  "package": {
    "id": 1,
    "name": "Starter Pack (Updated)",
    "coins_amount": 50,
    "price": 275,
    "currency": "SCR",
    "description": "Updated description",
    "status": "active",
    "sort_order": 1,
    "icon": null,
    "created_at": "2026-01-01T00:00:00.000000Z",
    "updated_at": "2026-01-15T10:00:00.000000Z"
  }
}
```

**Error:** `404` — Package not found.

### User story

- When the user saves the **Edit** modal, send `PUT /api/admin/coins/packages/{id}` with the changed fields (or full form).
- On success: show “Package updated”, close the modal, and refresh the package list so the card shows the new data.

---

## 6. Delete package

Permanently deletes a coin package. Use with care; if employers have purchased this package, consider disabling it (`status: inactive`) instead.

### Request

```
DELETE /api/admin/coins/packages/{id}
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | int | Package ID. |

### Response

**Status:** `200 OK`

```json
{
  "message": "Package deleted"
}
```

**Error:** `404` — Package not found.

### User story

- If you expose a “Delete” action (e.g. in edit modal or menu), confirm with the user, then call `DELETE /api/admin/coins/packages/{id}`.
- On success: show “Package deleted”, close the modal if open, and refresh the package list (and dashboard if needed).

---

## 7. Toggle package status (Active / Inactive)

Switches a package between `active` and `inactive`. Inactive packages can be hidden from employers on the campaign/purchase flow.

### Request

```
PUT /api/admin/coins/packages/{id}/toggle-status
```

| Parameter | Type | Description |
|-----------|------|-------------|
| `id` | int | Package ID. |

No body.

### Response

**Status:** `200 OK`

```json
{
  "message": "Package status updated",
  "package": {
    "id": 1,
    "name": "Starter Pack",
    "coins_amount": 50,
    "price": 250,
    "currency": "SCR",
    "description": "...",
    "status": "inactive",
    "sort_order": 1,
    "icon": null,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

**Error:** `404` — Package not found.

### User story

- If you add an “Enable/Disable” or “Active/Inactive” toggle on the card or in the edit modal, call `PUT /api/admin/coins/packages/{id}/toggle-status`.
- On success: update the card’s status badge and, if needed, refresh the list or dashboard stats.

---

## Package item shape (all endpoints)

Every package object in the API has this structure:

| Field | Type | Description |
|-------|------|-------------|
| `id` | int | Primary key. |
| `name` | string | Display name. |
| `coins_amount` | int | Coins in the package. |
| `price` | float | Price. |
| `currency` | string | e.g. SCR. |
| `description` | string \| null | Card description. |
| `status` | string | `active` or `inactive`. |
| `sort_order` | int | Order in list. |
| `icon` | string \| null | Optional icon. |
| `created_at` | string | ISO 8601. |
| `updated_at` | string | ISO 8601. |

---

## Summary: endpoints and when to use them

| Purpose | Method | Endpoint | User story |
|--------|--------|----------|------------|
| Load Coin Management page | GET | `/api/admin/coins/dashboard` | Load the four summary cards. |
| Load Packages tab | GET | `/api/admin/coins/packages` | Show package cards; optional `?status=active`. |
| Open Edit modal | GET | `/api/admin/coins/packages/{id}` | Pre-fill edit form. |
| Add Package | POST | `/api/admin/coins/packages` | “+ Add Package” form submit. |
| Save Edit | PUT | `/api/admin/coins/packages/{id}` | Save changes in edit modal. |
| Delete package | DELETE | `/api/admin/coins/packages/{id}` | After confirmation. |
| Toggle Active/Inactive | PUT | `/api/admin/coins/packages/{id}/toggle-status` | Enable/disable without full edit. |

---

## Notes for employers (job campaigns)

- The same **coin packages** (active only) should be exposed to employers when they choose to buy coins for job campaigns (e.g. a “Buy coins” or “Packages” step). Use `GET /api/admin/coins/packages` with `status=active` or a dedicated public/employer endpoint that returns only active packages.
- When an employer completes a coin purchase, create a **Payment** with `category: 'coins'`, `amount` = package price, `coins_amount` = package coins_amount, `coin_package_id` = package id, and credit the employer’s `coin_balance` by `coins_amount`. Then dashboard stats (Total Coins Sold, Revenue This Month) will update automatically.
