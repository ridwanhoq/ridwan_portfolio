# Portfolio — Progress

Last updated: 2026-11-08. Status: **complete and verified locally**; ready to deploy once the owner fills in the remaining personal details (see "Pending").

## Phase 1 — Repository inspection

| Item                        | Finding                                                                                                                                                    |
| --------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Laravel                     | 13.40 (Laravel Vue starter kit)                                                                                                                            |
| PHP                         | 8.4 locally. The locked dependencies (Symfony 8, Pest 5) require **8.4.1+**, so `composer.json` now says `^8.4` (it said `^8.3`, which could not install). |
| Vue                         | 3.5 + TypeScript, Inertia v3                                                                                                                               |
| CSS / UI                    | Tailwind CSS 4, shadcn-vue (reka-ui), Lucide                                                                                                               |
| Auth                        | Laravel Fortify (session auth, 2FA, passkeys, throttled login)                                                                                             |
| Build                       | Vite+ (`vp`)                                                                                                                                               |
| Database                    | Starter used SQLite; local env switched to MySQL (`my_portfolio`). Tests use SQLite in memory.                                                             |
| Existing functionality kept | Login, password reset, email verification, 2FA, passkeys, account settings, appearance                                                                     |

## Phase 2 — Architecture decisions

- Monolith: Laravel + Inertia + Vue 3. No separate API, Vue Router or Pinia (not needed with Inertia).
- Fortify session auth kept; registration disabled; `is_admin` flag (not mass assignable) + `admin` middleware + policies.
- Rich text as sanitised Markdown (raw HTML stripped, unsafe links removed).
- One secure image pipeline (GD re-encode, WebP variants, random names, SVG rejected).
- Résumé: uploaded PDF in private storage, or a PDF generated from content (dompdf). dompdf is the only new runtime dependency.
- SEO rendered on the server (Blade fallback or SSR) with matching `data-inertia` keys, so there are no duplicate tags.
- Versioned content cache, invalidated automatically by every content model.
- Optional Inertia SSR (falls back to client rendering automatically).

## Implementation checklist

### Backend

- [x] Environment configured (MySQL, app key, admin seed variables)
- [x] Migrations: 21 portfolio tables with foreign keys, cascade/null rules, unique slugs and indexes
- [x] Models, relationships, casts, scopes (`published`, `ordered`, `chronological`)
- [x] Admin middleware and policies (content, singleton, messages)
- [x] Media service (validation, re-encode, WebP, thumbnails, deletion)
- [x] Settings, SEO and content services with caching
- [x] Public controllers: home, about, projects, case study, résumé page and download, contact, privacy, blog, sitemap, robots, error pages
- [x] Admin: generic `ResourceController` (14 modules) + dashboard, profile, settings, résumé, media, messages, Markdown preview
- [x] Form Requests for every write endpoint; URL scheme validation
- [x] Contact form: validation, rate limiting (5/hour/IP + route throttle), honeypot, signed timing token, optional email notification after response
- [x] Security headers middleware; HTTPS forced in production
- [x] Seeders from the CV (no invented data; unknowns empty or unpublished); `portfolio:admin` command

### Frontend

- [x] Design tokens (brief's light/dark palette), self-hosted Inter, typography scale, focus styles, reduced motion, print styles
- [x] Public layout: sticky header, active-section indicator, accessible mobile menu, theme toggle, skip link, footer with back-to-top
- [x] Homepage sections, database-ordered and toggleable; empty sections hidden
- [x] About, Projects (category filter and search when justified), case study template, Résumé (print + PDF), Contact, Privacy, Blog, Error pages
- [x] Admin: dashboard, config-driven list and form pages, media picker, Markdown editor with server preview, repeaters, confirm dialog, toasts, loading, empty and error states
- [x] No `alert()` or `confirm()` anywhere

### Quality

- [x] Pest feature and unit tests: **181 passing**
- [x] Vitest component tests: **17 passing**
- [x] Playwright browser tests (desktop + mobile): **19 passing**
- [x] Larastan level 7: **0 errors**; Pint, oxlint/oxfmt and vue-tsc clean
- [x] Production build (client + SSR) succeeds
- [x] Internal link crawl: no broken links
- [x] Lighthouse mobile (SSR + gzip): Performance 94–96, Accessibility 100, Best Practices 100, SEO 100 with indexing enabled
- [x] Visual check of all public pages in light and dark mode, desktop and mobile; no console errors or hydration warnings

### Documentation

- [x] README.md
- [x] docs/architecture.md, database-schema.md, admin-guide.md, deployment-guide.md, testing-guide.md, content-management-guide.md

## Pending (needs the owner)

These need real information that wasn't available. The admin dashboard lists them under **Finish setting up**:

- Public contact email (Admin → Profile). The CV's email was masked.
- LinkedIn and GitHub URLs. Seeded as unpublished `PLACEHOLDER` links.
- Profile photo (optional) and project thumbnails or screenshots.
- Fuller case-study sections (architecture, database, security, results). Only facts from the CV were seeded.
- Mail settings in `.env` if contact notifications are wanted.
- Production deployment itself (server, domain, HTTPS), following docs/deployment-guide.md.

## Known issues and limitations

- Lighthouse scores above were measured locally on simulated mobile with SSR and gzip; production numbers depend on the server and network. Without SSR, mobile Performance is about 70.
- Browser tests write a small test image into `storage/app/public/media` on each run.
- The repository is not under Git yet (`git init` was not run).
- The admin index lists are paginated server-side; there is no drag-and-drop reordering (order is set with the "Sort order" field).

## Bugs found and fixed during verification

- Scroll-reveal could leave sections invisible; now never hides content already on screen, and reveals everything before printing.
- Mobile menu panel collapsed to zero height (`backdrop-filter` created a containing block); it is now positioned relative to the header.
- Admin error toasts covered the sticky Save button; admin toasts moved to the top-right.
- Deleting from a filtered admin list lost the filters; the redirect now keeps them.
- A blank name produced a confusing extra "slug is required" error; slug is now only required when its source field is filled.
- Fonts were downloaded twice (WOFF and WOFF2); now WOFF2 only, with two preloads.
- The seeder read `env()` directly, which returns null once config is cached; moved to `config/portfolio.php`.
- The résumé PDF could strand a section heading at the bottom of a page; headings now stay with their first entry.
