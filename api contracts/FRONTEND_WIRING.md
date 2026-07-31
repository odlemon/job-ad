# Frontend wiring checklist (after backend is live)

Do **not** wire these until the backend confirms [`API_CONTRACT.md`](./API_CONTRACT.md) / [`openapi.yaml`](./openapi.yaml).

Path constants live in [`src/api/endpoints.js`](../src/api/endpoints.js). HTTP client: [`src/api.js`](../src/api.js).

## Auth

| Screen / action | File | Endpoint |
|-----------------|------|----------|
| Sign In | `LoginView.vue` | `POST /auth/login` — replace `loginDemo` |
| Register steps 1–4 | `RegisterView.vue` + `signup.js` | `POST /auth/register` |
| OTP verify / resend | `RegisterView.vue` | `POST /auth/otp/verify`, `/auth/otp/resend` |
| Logout | `ProfileView.vue`, `SettingsView.vue` | `POST /auth/logout` |
| Session restore | `auth.js`, router | `GET /auth/me` |
| Forgot password | `LoginView.vue` | `POST /auth/forgot-password` |

## Profile core

| Screen | File | Endpoint |
|--------|------|----------|
| Hub + strength | `ProfileView.vue` | `GET /job-seeker/profile`, `GET /job-seeker/summary` |
| Personal info / edits | `PersonalInfoView.vue`, `PersonalInfoEditView.vue` | `PUT /job-seeker/profile`, `POST …/photo` |
| About Me | `AboutMeView.vue` | `PUT /job-seeker/profile` `{ bio }` |
| Job preferences | `JobPreferencesView.vue` | `PUT /job-seeker/profile` `{ job_preferences }` |
| Salary | `SalaryRangeView.vue` | `PUT /job-seeker/profile` `{ expected_salary_min, max }` |
| Job discovery cats | `JobDiscoveryView.vue` | `PUT /job-seeker/profile` `{ job_discovery_categories }` |

## Profile nested CRUD

| Screen | File | Collection |
|--------|------|------------|
| Work experience | `WorkExperienceView.vue` | `/job-seeker/experiences` |
| Education | `EducationView.vue` | `/job-seeker/educations` |
| Skills | `SkillsView.vue` | `/job-seeker/skills` |
| Languages | `LanguagesView.vue` | `/job-seeker/languages` |
| Hobbies | `HobbiesView.vue` | `/job-seeker/hobbies` |
| Social links | `SocialLinksView.vue` | `/job-seeker/social-links` |
| Certifications | `CertificationsView.vue` | `/job-seeker/certifications` |
| References | `ReferencesView.vue` | `/job-seeker/references` |
| Documents | `DocumentsView.vue` | `/job-seeker/documents` |

## Jobs / companies

| Screen | File | Endpoint |
|--------|------|----------|
| Home | `HomeView.vue` | `GET /jobs/published` |
| Search | `SearchResultsView.vue` | `GET /jobs/search` |
| Job detail + apply | `JobDetailView.vue` | `GET /jobs/{id}`, `POST /jobs/{id}/apply` |
| Applied | `AppliedJobsView.vue` | `GET /job-seeker/applications` |
| Saved | `SavedJobsView.vue` | `GET /job-seeker/saved-jobs` + save/unsave |
| Recommended | `RecommendedJobsView.vue` | `GET /job-seeker/recommended-jobs` |
| Invited | `InvitedJobsView.vue` | `GET /job-seeker/invitations` |
| Followed companies | `FollowedCompaniesView.vue` | `GET /job-seeker/followed-companies` |
| Company detail | `CompanyDetailView.vue` | `GET /companies/{id}` |

## Tenders / courses

| Screen | File | Endpoint |
|--------|------|----------|
| Tenders list | `TendersView.vue` | `GET /tenders` |
| Tender detail | `TenderDetailView.vue` | `GET /tenders/{id}` |
| Courses list | `CoursesView.vue` | `GET /courses`, `GET /training-providers/featured` |
| Course detail | `CourseDetailView.vue` | `GET /courses/{id}` |

## Notifications / settings

| Screen | File | Endpoint |
|--------|------|----------|
| Notifications | `NotificationsView.vue` + `stores/notifications.js` | `/notifications*` |
| Settings | `SettingsView.vue` | `GET|PUT /job-seeker/settings` |

## When wiring

1. Remove / gate `loginDemo` behind `import.meta.env.DEV` only if needed for offline demos.  
2. Keep `DEMO DATA` comments until each call is verified.  
3. Set `requiresAuth` on all `/profile/*` routes.  
4. Prefer one API client (`src/api.js`); align or remove `src/utils/api.js` token keys.  
5. **Backend is live** — use seed user `scoop.seeker@example.com` / `password123` (see [`BACKEND_STATUS.md`](./BACKEND_STATUS.md)).  
6. For register OTP flow, send header `X-Client: scoop` or body `require_otp: true`.
