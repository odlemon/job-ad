# Public API (for external / mobile projects)

**Backend base URL (development):** `http://localhost:8000`  
**API prefix:** `/api`

This API uses **Sanctum token-based authentication** — same pattern as the Admin API.  
After login or registration you receive a Bearer token. Send it in the `Authorization` header:

```
Authorization: Bearer <your-token-here>
```

All endpoints listed below are the **existing application routes**. They accept **both** Bearer tokens and session cookies, so the same routes work for the frontend and for external projects.

---

## Base URL

- **Local:** `http://localhost:8000`
- **Production:** Set to your deployed backend URL (e.g. `https://api.yourdomain.com`)

---

## Endpoints Overview

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| `POST` | `/api/auth/register` | No | Register a new user (job seeker or employer) |
| `POST` | `/api/auth/login` | No | Login & get Bearer token |
| `POST` | `/api/auth/logout` | Yes | Logout & revoke token |
| `GET` | `/api/auth/me` | Yes | Get authenticated user + profile |
| `GET` | `/api/jobs/published` | No | List published jobs (paginated, cached) |
| `GET` | `/api/jobs/search` | No | Search & filter published jobs |
| `GET` | `/api/jobs/{id}` | No | Get single job details + similar jobs |
| `GET` | `/api/job-seeker/profile` | Yes | Get full job seeker profile |
| `PUT` | `/api/job-seeker/profile` | Yes | Update job seeker profile |
| `GET` | `/api/job-seeker/applications` | Yes | List my job applications |
| `GET` | `/api/job-seeker/applications/{id}` | Yes | Get application details |
| `DELETE` | `/api/job-seeker/applications/{id}/withdraw` | Yes | Withdraw an application |
| `POST` | `/api/job-seeker/applications` | Yes | Submit a job application |
| `GET` | `/api/job-seeker/saved-jobs` | Yes | List saved jobs |
| `POST` | `/api/job-seeker/saved-jobs` | Yes | Save a job |
| `DELETE` | `/api/job-seeker/saved-jobs/{jobId}` | Yes | Unsave a job |
| `GET` | `/api/job-seeker/experiences` | Yes | List work experiences |
| `POST` | `/api/job-seeker/experiences` | Yes | Add work experience |
| `PUT` | `/api/job-seeker/experiences/{id}` | Yes | Update work experience |
| `DELETE` | `/api/job-seeker/experiences/{id}` | Yes | Delete work experience |
| `GET` | `/api/job-seeker/educations` | Yes | List education history |
| `POST` | `/api/job-seeker/educations` | Yes | Add education |
| `PUT` | `/api/job-seeker/educations/{id}` | Yes | Update education |
| `DELETE` | `/api/job-seeker/educations/{id}` | Yes | Delete education |
| `GET` | `/api/job-seeker/skills` | Yes | List skills |
| `POST` | `/api/job-seeker/skills` | Yes | Add a skill |
| `PUT` | `/api/job-seeker/skills/{id}` | Yes | Update a skill |
| `DELETE` | `/api/job-seeker/skills/{id}` | Yes | Delete a skill |
| `GET` | `/api/job-seeker/languages` | Yes | List languages |
| `POST` | `/api/job-seeker/languages` | Yes | Add a language |
| `PUT` | `/api/job-seeker/languages/{id}` | Yes | Update a language |
| `DELETE` | `/api/job-seeker/languages/{id}` | Yes | Delete a language |
| `GET` | `/api/job-seeker/certifications` | Yes | List certifications |
| `POST` | `/api/job-seeker/certifications` | Yes | Add a certification |
| `PUT` | `/api/job-seeker/certifications/{id}` | Yes | Update a certification |
| `DELETE` | `/api/job-seeker/certifications/{id}` | Yes | Delete a certification |
| `GET` | `/api/job-seeker/references` | Yes | List references |
| `POST` | `/api/job-seeker/references` | Yes | Add a reference |
| `PUT` | `/api/job-seeker/references/{id}` | Yes | Update a reference |
| `DELETE` | `/api/job-seeker/references/{id}` | Yes | Delete a reference |
| `GET` | `/api/job-seeker/followed-companies` | Yes | List followed companies |
| `POST` | `/api/job-seeker/followed-companies` | Yes | Follow a company |
| `DELETE` | `/api/job-seeker/followed-companies/{companyId}` | Yes | Unfollow a company |
| `GET` | `/api/job-seeker/category-preferences` | Yes | List category preferences |
| `POST` | `/api/job-seeker/category-preferences` | Yes | Add category preference |
| `DELETE` | `/api/job-seeker/category-preferences/{categoryId}` | Yes | Remove category preference |

---

## Authentication Endpoints

### POST /api/auth/register

Register a new user. Returns a Bearer token.

**Request — Headers:** `Content-Type: application/json`

**Request — Body (JSON)**

```json
{
  "user_type": "job_seeker",
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "phone": "+1234567890",
  "date_of_birth": "1995-06-15",
  "gender": "male",
  "employment_status": "currently_employed",
  "highest_education": "Bachelor's Degree"
}
```

For employer registration, send `user_type: "employer"` instead:

```json
{
  "user_type": "employer",
  "first_name": "Jane",
  "last_name": "Smith",
  "email": "jane@company.com",
  "password": "secret123",
  "password_confirmation": "secret123",
  "company_name": "Tech Corp",
  "industry": "Technology",
  "company_size": "50-100",
  "website": "https://techcorp.com"
}
```

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `user_type` | string | ✓ | `job_seeker` or `employer` |
| `first_name` | string | ✓ | |
| `last_name` | string | ✓ | |
| `email` | string | ✓ | Must be unique |
| `password` | string | ✓ | Min 6 characters |
| `password_confirmation` | string | ✓ | Must match password |
| `phone` | string | | |
| *Additional fields for job_seeker* | | | |
| `date_of_birth` | date | ✓ | `YYYY-MM-DD` |
| `gender` | string | ✓ | `male`, `female`, `other`, `prefer_not_to_say` |
| `employment_status` | string | ✓ | `currently_employed`, `unemployed`, `student`, `self_employed`, `retired` |
| `highest_education` | string | ✓ | |
| *Additional fields for employer* | | | |
| `company_name` | string | ✓ | |
| `industry` | string | ✓ | |
| `company_size` | string | ✓ | |
| `website` | string | | |

**Response — 201 Created**

```json
{
  "message": "Registration successful",
  "token": "2|abc123def456...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "user_type": "job_seeker"
  }
}
```

**Response — 422 Validation Failed**

```json
{
  "message": "Validation failed",
  "errors": {
    "email": ["The email has already been taken."],
    "password": ["The password field must be at least 6 characters."]
  }
}
```

---

### POST /api/auth/login

Login and receive a Bearer token.

**Request — Headers:** `Content-Type: application/json`

**Request — Body (JSON)**

```json
{
  "email": "john.doe@example.com",
  "password": "secret123"
}
```

**Response — 200 OK**

```json
{
  "message": "Login successful",
  "token": "1|xyz789...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "user_type": "job_seeker",
    "is_active": true,
    "is_verified": true
  }
}
```

**Response — 401 Unauthorized**

```json
{
  "message": "Invalid credentials"
}
```

---

### POST /api/auth/logout

Revoke the current token. Requires Bearer token or session.

**Request — Headers:**
```
Authorization: Bearer <token>
```

**Response — 200 OK**

```json
{
  "message": "Logout successful"
}
```

---

### GET /api/auth/me

Get the authenticated user and their profile.

**Request — Headers:**
```
Authorization: Bearer <token>
```

**Response — 200 OK**

```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john.doe@example.com",
    "user_type": "job_seeker",
    "phone": "+1234567890",
    "is_active": true,
    "is_verified": false,
    "last_login": "2026-05-26T10:30:00.000000Z",
    "job_seeker": {
      "seeker_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "profile_photo": null,
      "bio": null,
      "location": "Malé, Maldives",
      "phone": "+1234567890",
      "gender": "male",
      "date_of_birth": "1995-06-15",
      "employment_status": "currently_employed",
      "highest_education": "Bachelor's Degree",
      "driving_license": false,
      "cv_file_path": null
    }
  }
}
```

---

## Job Endpoints

### GET /api/jobs/published

List published jobs (paginated, cached 30 min).

**Query Parameters**

| Param | Type | Description |
|-------|------|-------------|
| `per_page` | int | Max 50, default 15 |
| `page` | int | Page number |

**Response — 200 OK**

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "title": "Senior Software Engineer",
      "slug": "senior-software-engineer",
      "description": "We are looking for...",
      "employment_type": "full_time",
      "experience_level": "senior",
      "salary_min": "5000",
      "salary_max": "8000",
      "currency": "USD",
      "hide_salary": false,
      "location": "Malé",
      "is_remote": true,
      "views_count": 120,
      "applications_count": 5,
      "status": "published",
      "published_at": "2026-05-20T08:00:00.000000Z",
      "company": {
        "id": 1,
        "name": "Tech Corp",
        "slug": "tech-corp",
        "logo": "https://example.com/logo.png",
        "industry": "Technology"
      },
      "category": {
        "id": 2,
        "name": "Information Technology"
      }
    }
  ],
  "total": 72,
  "per_page": 15,
  "current_page": 1,
  "last_page": 5
}
```

---

### GET /api/jobs/search

Search and filter published jobs.

**Query Parameters**

| Param | Type | Description |
|-------|------|-------------|
| `keyword` | string | Search in title, description |
| `category_id` | int | Filter by category |
| `location` | string | Filter by location |
| `employment_type` | string | `full_time`, `part_time`, `contract`, `internship`, `temporary` |
| `experience_level` | string | `entry`, `mid`, `senior`, `lead`, `executive` |
| `salary_min` | int | Minimum salary |
| `salary_max` | int | Maximum salary |
| `is_remote` | boolean | `true` or `false` |
| `sort` | string | `newest` (default), `oldest`, `salary_high`, `salary_low` |
| `per_page` | int | Max 50, default 15 |
| `page` | int | Page number |

**Response — 200 OK** — Same format as `GET /api/jobs/published`.

---

### GET /api/jobs/{id}

Get a single published job with similar jobs and company info.

**Response — 200 OK**

```json
{
  "data": {
    "id": 1,
    "company_id": 1,
    "category_id": 2,
    "title": "Senior Software Engineer",
    "slug": "senior-software-engineer",
    "description": "Full job description...",
    "requirements": "• 5+ years experience...",
    "benefits": "• Competitive salary...",
    "employment_type": "full_time",
    "experience_level": "senior",
    "salary_min": "5000",
    "salary_max": "8000",
    "currency": "USD",
    "location": "Malé",
    "is_remote": true,
    "status": "published",
    "published_at": "2026-05-20T08:00:00.000000Z",
    "company": {
      "id": 1,
      "name": "Tech Corp",
      "slug": "tech-corp",
      "logo": "https://example.com/logo.png",
      "industry": "Technology",
      "website": "https://techcorp.mv"
    },
    "category": {
      "id": 2,
      "name": "Information Technology",
      "slug": "information-technology"
    }
  },
  "similar_jobs": [...],
  "total_company_jobs": 3,
  "review_stats": {
    "avg_rating": 4.5,
    "review_count": 12
  }
}
```

**Response — 404 Not Found**

```json
{
  "message": "Job not found"
}
```

---

## Job Seeker Profile Endpoints

All endpoints under `/api/job-seeker/*` require authentication via Bearer token or session.

### GET /api/job-seeker/profile

Get the full job seeker profile with all relations.

**Request — Headers:**
```
Authorization: Bearer <token>
```

**Response — 200 OK**

```json
{
  "data": {
    "seeker_id": 1,
    "user_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "profile_photo": "https://example.com/photo.jpg",
    "bio": "Experienced software engineer...",
    "location": "Malé, Maldives",
    "phone": "+1234567890",
    "gender": "male",
    "date_of_birth": "1995-06-15",
    "employment_status": "currently_employed",
    "highest_education": "Bachelor's Degree",
    "driving_license": true,
    "job_preferences": ["full_time", "remote"],
    "expected_salary_min": 5000,
    "expected_salary_max": 8000,
    "linkedin_url": "https://linkedin.com/in/johndoe",
    "public_profile": true,
    "open_to_opportunities": true,
    "cv_file_path": "https://example.com/cv.pdf",
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john.doe@example.com",
      "phone": "+1234567890",
      "user_type": "job_seeker"
    },
    "experiences": [...],
    "educations": [...],
    "skills": [...],
    "languages": [...],
    "certifications": [...],
    "references": [...],
    "documents": [...],
    "category_preferences": [...]
  }
}
```

### PUT /api/job-seeker/profile

Update the job seeker profile.

**Request — Headers:**
```
Authorization: Bearer <token>
Content-Type: application/json
```

**Request — Body (JSON)**

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "bio": "Updated bio...",
  "location": "Malé, Maldives",
  "phone": "+1234567890",
  "gender": "male",
  "employment_status": "currently_employed",
  "highest_education": "Master's Degree",
  "driving_license": true,
  "job_preferences": ["full_time", "part_time"],
  "expected_salary_min": 6000,
  "expected_salary_max": 10000,
  "linkedin_url": "https://linkedin.com/in/johndoe",
  "public_profile": true,
  "open_to_opportunities": true
}
```

**Response — 200 OK**

```json
{
  "message": "Profile updated successfully",
  "data": { ... }
}
```

---

## Application Endpoints

### GET /api/job-seeker/applications

List all applications for the authenticated job seeker.

**Request — Headers:**
```
Authorization: Bearer <token>
```

**Query Parameters:** `per_page` (default 15), `page`

**Response — 200 OK**

```json
{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "job_advertisement_id": 1,
      "seeker_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "status": "pending",
      "in_talent_pool": false,
      "interview_scheduled_at": null,
      "interview_status": null,
      "created_at": "2026-05-22T14:30:00.000000Z",
      "job_advertisement": {
        "id": 1,
        "title": "Senior Software Engineer",
        "slug": "senior-software-engineer",
        "employment_type": "full_time",
        "salary_min": "5000",
        "salary_max": "8000",
        "currency": "USD",
        "location": "Malé",
        "is_remote": true,
        "company": {
          "id": 1,
          "name": "Tech Corp",
          "slug": "tech-corp",
          "logo": "https://example.com/logo.png"
        },
        "category": {
          "id": 2,
          "name": "Information Technology"
        }
      }
    }
  ],
  "total": 35,
  "per_page": 15,
  "current_page": 1,
  "last_page": 3
}
```

### GET /api/job-seeker/applications/{id}

Get a specific application.

**Response — 200 OK**

```json
{
  "application": {
    "id": 1,
    "job_advertisement_id": 1,
    "status": "shortlisted",
    "interview_scheduled_at": "2026-06-01T10:00:00.000000Z",
    "interview_location": "Tech Corp Office, Malé",
    "interview_status": "pending",
    "created_at": "2026-05-22T14:30:00.000000Z",
    "updated_at": "2026-05-25T09:00:00.000000Z",
    "job_advertisement": {
      "id": 1,
      "title": "Senior Software Engineer",
      "description": "Full job description...",
      "salary_min": "5000",
      "salary_max": "8000",
      "location": "Malé",
      "is_remote": true,
      "company": {
        "id": 1,
        "name": "Tech Corp",
        "slug": "tech-corp"
      }
    }
  }
}
```

### DELETE /api/job-seeker/applications/{id}/withdraw

Withdraw an application.

**Response — 200 OK**

```json
{
  "message": "Application withdrawn successfully"
}
```

### POST /api/job-seeker/applications

Submit a new job application.

**Request — Headers:** `Authorization: Bearer <token>`, `Content-Type: application/json`

**Request — Body**

```json
{
  "job_advertisement_id": 1,
  "additional_info": {}
}
```

**Response — 201 Created**

```json
{
  "message": "Application submitted successfully",
  "application": { ... }
}
```

---

## Saved Jobs Endpoints

### GET /api/job-seeker/saved-jobs

**Request — Headers:** `Authorization: Bearer <token>`

**Query Parameters:** `per_page` (default 15), `page`

### POST /api/job-seeker/saved-jobs

**Request — Body:** `{ "job_id": 1 }`

**Response — 201:** `{ "message": "Job saved successfully" }`

### DELETE /api/job-seeker/saved-jobs/{jobId}

**Response — 200:** `{ "message": "Job unsaved successfully" }`

---

## Error Responses

### 401 Unauthenticated

```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden

```json
{
  "message": "Unauthorized. Job seeker access required."
}
```

### 422 Validation Failed

```json
{
  "message": "Validation failed",
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

### 404 Not Found

```json
{
  "message": "Job not found"
}
```
