    # Scoop API — Backend Issues (closed stubs)

    **Date:** 2026-07-10

    | Issue | Was | Now | Notes |
    |-------|-----|-----|-------|
    | Tender attachment download | Stub 404 | Real resolve | Redirect absolute URLs; stream local `storage`/`public` paths; clear 404 if missing |
    | Tender documents zip | Stub 404 | Real zip of local files | 422/404 with message when no local files (not fake success) |
    | Course enroll | Success stub | `course_enrollments` table | `201` create / `422` duplicate |
    | Company workplace / socials / benefits | Missing from payload | On `GET /companies/{id}` | Migration + `ScoopCompanyMetaSeeder` |
    | Review aggregates | Missing | `rating_distribution` + `aspect_averages` | Computed from `company_reviews` |
    | Profile section flags | Internal only | `profile_sections` map | FE-friendly keys |
    | Share card | Missing | `GET /job-seeker/share-card` | `qr_url` intentionally null |
    | Category icons | Missing | `icon` on meta categories | DB column + emoji fallback |
    | Home advertise banner | Missing | `GET /meta/banners` | Static marketing payload |
    | Skills/languages/certs Scoop names | Legacy only | Dual keys via `ScoopNestedPresenter` | |
    | Applications list | Raw paginator | `{ data, meta }` + nested `job` | |
    | Unified `/search` | Requested | **Won’t do** | FE keeps parallel job/company/tender calls |

    Still out of scope (by design):

    - Employer Blade UI to edit structured benefits/values
    - Real QR image generation
    - Unified search aggregator
    - Scoop Vue wiring (frontend follow-up)
