# Testing guide

Three automated suites, plus static analysis. All of them run in CI (`.github/workflows/tests.yml`).

| Suite                    | Tool                                                  | Command                                 | Count     |
| ------------------------ | ----------------------------------------------------- | --------------------------------------- | --------- |
| Backend feature and unit | Pest 5                                                | `php artisan test`                      | 181 tests |
| Frontend components      | Vitest (Jest-compatible API) + Vue Test Utils + jsdom | `npm test`                              | 17 tests  |
| End-to-end browser       | Playwright (Chromium, desktop + Pixel 7)              | `npm run build && npm run test:browser` | 19 tests  |
| PHP static analysis      | Larastan level 7                                      | `composer types:check`                  | 0 errors  |
| PHP style                | Pint                                                  | `composer lint:check`                   |           |
| TS types                 | vue-tsc                                               | `npm run types:check`                   |           |
| JS lint and format       | Vite+ (oxlint, oxfmt)                                 | `npm run check`                         |           |

## Backend (Pest)

Runs on in-memory SQLite (`phpunit.xml`), with SSR disabled. Portfolio tests live in `tests/Feature/Portfolio` and seed the real CV content through `seedPortfolio()` (`tests/Pest.php`).

| File                             | Covers                                                                                                                                                                                                                                                                                                                                                                                            |
| -------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `PublicPagesTest`                | Every public page renders the right Inertia component; database-driven content; experience ordering; draft and scheduled projects hidden; only filled case-study sections shown; section toggles and ordering; unpublished socials hidden; branded 404; blog gating; generated PDF; security headers                                                                                              |
| `SeoTest`                        | Server-rendered title, description, canonical, OG and Twitter tags; Person, WebSite, CreativeWork and Breadcrumb JSON-LD; admin SEO changes and noindex; Search Console tag; sitemap contents; robots.txt in both modes                                                                                                                                                                           |
| `ContactFormTest`                | Stores valid messages; server validation; honeypot; timing token (too fast, tampered); rate limit; admin notification on and off; form disabled                                                                                                                                                                                                                                                   |
| `AdminAccessTest`                | Guests redirected, non-admins forbidden, admins allowed on all 23 admin pages; write protection; registration disabled; `is_admin` not mass assignable; login throttling; post-login redirect                                                                                                                                                                                                     |
| `AdminCrudTest`                  | Search, filters, sort whitelist, pagination; experience CRUD with relations; project save with technologies, gallery and links in one transaction; unsafe URL rejection; unique slugs; toggle whitelist; cache invalidation reaching the public site; settings; fixed SEO pages and sections; navigation URLs; messages; dashboard counts; Markdown preview sanitising; delete keeps list filters |
| `UploadsTest`                    | Re-encoding and WebP variants; SVG, PHP and PDF disguised as images rejected; size limit; non-admin upload blocked; deleting files and detaching relations; alt text; résumé upload kept private; fake PDFs rejected; fallback to the generated PDF                                                                                                                                               |
| `tests/Unit`                     | Markdown sanitising; versioned content cache                                                                                                                                                                                                                                                                                                                                                      |
| `tests/Feature/Auth`, `Settings` | Starter kit authentication and account settings, adapted to admin-only access                                                                                                                                                                                                                                                                                                                     |

## Frontend (Vitest)

Tests are in `resources/js/tests`. Inertia's `Link`, `Head` and `SeoHead` are stubbed in `setup.ts`.

- `ThemeToggle.test.ts`: toggling the theme, persistence in localStorage and cookie, ARIA state.
- `ContactForm.test.ts`: labels, hidden honeypot, posting with the spam token, accessible server errors, error toasts, never calling `alert()`.
- `api.test.ts`: XSRF header, first validation message surfaced, network and non-JSON failures.
- `confirm.test.ts`: promise-based confirmation dialog.
- `fields.test.ts`: switch semantics, list field add/edit/reorder/remove, multi-select.
- `projects.test.ts`: project card links, category filtering, filters hidden when there are too few projects.

## Browser (Playwright)

`playwright.config.ts` starts PHP's built-in server against a **throwaway SQLite database** (`database/e2e.sqlite`), clears the cache, and runs `migrate:fresh --seed` first. Your MySQL data is never touched. A setup step signs in once and reuses the session, so admin tests don't trip the login throttle.

Covered: homepage sections with no console errors; primary navigation and active section; skip link; theme persistence across reloads; card → case study; category filter; résumé download; contact validation and success toast; 404 page; a crawl of every internal link on public pages; admin login and dashboard numbers; wrong-password message; technology create, edit and delete with the confirmation dialog; list toggle reflected on the homepage; media upload and attaching it to a project; contact message arriving in the inbox. Mobile (`@mobile`): menu open, Escape and navigate; no horizontal overflow on six pages.

Note: uploads made by browser tests are written to `storage/app/public/media`.

## Lighthouse (manual)

Measured locally on Lighthouse 12's default **mobile** profile (simulated slow 4G and CPU throttling), with SSR enabled and responses gzip-compressed as in the nginx config:

| Page                                          | Performance | Accessibility | Best practices | SEO  |
| --------------------------------------------- | ----------- | ------------- | -------------- | ---- |
| `/`                                           | 94          | 100           | 100            | 100  |
| `/projects/{slug}`                            | 94–96       | 100           | 100            | 100  |
| `/about`, `/projects`, `/contact`, `/privacy` | 95–96       | 100           | 100            | 100* |
| `/resume`                                     | 94          | 100           | 100            | 100* |

Desktop profile: homepage Performance 99. SEO 100 was measured on `/` and a case study with indexing enabled (`SEO_ALLOW_INDEXING=true`, the production default). \*On the other pages the only failing SEO audit was the `robots.txt` block that applies outside production, so they are expected to reach 100 in production too. Local runs without indexing enabled show SEO 66 on purpose.

Without SSR and compression (plain `php artisan serve`), mobile Performance drops to about 70, because the page can only paint after JavaScript loads. Use the production setup for real measurements.

To reproduce:

```bash
npx lighthouse https://your-domain.com/ --view
```
