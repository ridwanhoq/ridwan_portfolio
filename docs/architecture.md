# Architecture

## Overview

A single Laravel 13 application. Vue 3 pages are rendered through Inertia.js, so there is no separate API service, no client-side router and no global client store. Laravel controllers return Inertia responses; Vue pages receive typed props.

```
Browser ──► nginx ──► PHP-FPM (Laravel)
                         │  routes/web.php    public site
                         │  routes/admin.php  admin panel (auth + verified + admin)
                         │  routes/settings.php account settings (Fortify)
                         ├─► MySQL           content, sessions, cache
                         ├─► storage/app/public   uploaded images (served via /storage)
                         ├─► storage/app/private  uploaded résumé PDF (never public)
                         └─► Node SSR server (optional, :13714) renders the first HTML
```

## Backend

| Layer              | Location                            | Notes                                                                                                                                                                                                               |
| ------------------ | ----------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Public controllers | `app/Http/Controllers/Site`         | Thin. Read data through `PortfolioContent`, attach SEO through `Seo`.                                                                                                                                               |
| Admin controllers  | `app/Http/Controllers/Admin`        | 14 modules extend `ResourceController` (search, filters, sort, pagination, toggles, transactions, toasts). Profile, settings, résumé, media and messages have bespoke controllers.                                  |
| Validation         | `app/Http/Requests`                 | One Form Request per write endpoint. `AdminRequest` provides slug generation, month-to-date conversion, list cleaning and the `SAFE_URL` rule.                                                                      |
| Authorization      | `app/Policies`, `EnsureUserIsAdmin` | `ContentPolicy` (admin only), `SingletonPolicy` (edit only: SEO pages, sections, profile), `ContactMessagePolicy` (no create/edit). Registered in `AppServiceProvider`.                                             |
| Services           | `app/Services`                      | `PortfolioContent` (cached read models), `Seo` (meta + JSON-LD), `Settings` (typed key/value), `MediaService` (secure image pipeline), `ResumeService` (upload or generated PDF), `Markdown` (sanitised rendering). |
| Transformations    | `app/Http/Resources`                | API Resources shape public payloads (`ProjectResource` only includes case-study sections that have content).                                                                                                        |
| Caching            | `app/Support/ContentCache`          | Versioned cache keys. Every content model uses `FlushesContentCache`, so any save or delete invalidates all public caches. Works with any cache store, with no tags needed.                                         |
| Errors             | `app/Support/ErrorPage`             | Branded Inertia pages for 403/404/429/500/503; a 419 (expired CSRF token) becomes a toast and redirects back.                                                                                                       |

### Key decisions

- **Inertia instead of a REST API + SPA.** The site and admin are one product with one team; Inertia keeps routing, validation and authorization on the server and removes an API layer that nobody else consumes.
- **Fortify session auth** (from the starter kit) with registration disabled. `is_admin` is not mass assignable. No Sanctum or Passport.
- **Markdown for rich text.** It's rendered on the server with `html_input=strip` and `allow_unsafe_links=false`, so admins can write headings, lists, links and code blocks, while raw HTML and `javascript:` links never reach the page. The output is rendered with `v-html` only through the `Prose` component.
- **One image pipeline.** All images go through `MediaService`: MIME and extension checks, a pixel-count limit, decoding and re-encoding with GD (which strips EXIF data and appended payloads), random filenames, and WebP full-size and 720px variants. SVG is rejected.
- **Server-rendered SEO.** Meta tags are rendered in `resources/views/app.blade.php` from the controller's `seo` view data. With SSR enabled, `<SeoHead>` renders the same tags from the SSR server. Both use the same `data-inertia` keys, so client-side navigation replaces tags instead of duplicating them. JSON-LD is always rendered by Blade.
- **Contact notifications** are sent after the response (`dispatch(...)->afterResponse()`), so no queue worker is needed and a slow mail server never delays the visitor.
- **Generated résumé.** dompdf renders `resources/views/resume/pdf.blade.php` (single column, real text, no images) from the same data as the site, so it is never out of date. An uploaded PDF can replace it.

## Frontend

| Area                | Location                                                                                                                                                                                                                           |
| ------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Entry, layouts      | `resources/js/app.ts`. `SiteLayout` loads eagerly; admin and auth layouts are code-split so public visitors never download them.                                                                                                   |
| Design tokens       | `resources/css/app.css`: colour variables for light and dark (the palette in the brief), typography scale (`text-display`, `text-title`, `text-lead`, `eyebrow`), prose styles, reduced-motion and print rules.                    |
| Public components   | `resources/js/components/site`: header (sticky, active section, mobile menu), footer, theme toggle, project card, timeline, skills, education, contact form, responsive image, SEO head.                                           |
| Public pages        | `resources/js/pages/site`                                                                                                                                                                                                          |
| Admin configuration | `resources/js/admin/resources.ts` defines the table columns and form fields for every module.                                                                                                                                      |
| Admin generic pages | `pages/admin/resource/Index.vue` and `Form.vue` render any module from that configuration.                                                                                                                                         |
| Admin fields        | `components/admin/fields`: text, textarea, select, switch, list, multi-select, Markdown (with server preview), media picker and repeater.                                                                                          |
| Shared utilities    | `lib/api.ts` (fetch wrapper with XSRF token), `composables/useConfirm.ts` (promise-based dialog replacing `confirm()`), `lib/flashToast.ts` (server flash → toast), `composables/useActiveSection.ts`, `composables/useReveal.ts`. |

No component calls `alert()` or `confirm()`. All feedback uses toasts (vue-sonner) and the confirmation dialog.

## Request lifecycle (public page)

1. Route → `Site\*Controller` → `PortfolioContent` reads from cache (or the database, then caches).
2. `Seo::forPage()` merges per-page settings from `seo_settings` with any overrides and builds JSON-LD.
3. `SiteController::page()` returns `Inertia::render()` with props plus `seo` view data.
4. With SSR, the Node server renders the HTML; otherwise Blade outputs the shell and meta tags, and Vue mounts in the browser.
5. `HandleInertiaRequests` shares `site` (navigation, socials, availability), which is also cached.
