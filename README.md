# Engineering Portfolio — Laravel 13 + Vue 3

A production-ready personal portfolio for a Senior Software Engineer, with a full admin panel. Every piece of public content (profile, experience, projects and case studies, skills, education, navigation, SEO and settings) is stored in the database and edited from `/admin`.

**Stack:** Laravel 13 · PHP 8.4 · MySQL 8+ · Inertia.js v3 · Vue 3 (Composition API, TypeScript) · Tailwind CSS 4 · Vite+ · Laravel Fortify · Pest · Vitest · Playwright

## Features

- **Public site:** homepage (hero, about, experience timeline, featured projects, skills, engineering approach, education, contact), About, Projects with category filters, detailed case studies, printable Résumé with PDF download, Contact, Privacy policy, optional Blog, and branded error pages.
- **Admin panel:** dashboard with real counts, CRUD for 14 content modules, profile, settings, homepage section order and visibility, navigation, SEO per page, résumé management, media library, and contact inbox.
- **Light and dark themes:** follow the system setting, with a toggle that is remembered.
- **SEO:** server-rendered meta tags, Open Graph and Twitter cards, canonical URLs, JSON-LD (Person, WebSite, CreativeWork, BreadcrumbList, BlogPosting), dynamic `sitemap.xml` and `robots.txt`, and Search Console verification.
- **Security:** session auth with login throttling, 2FA and passkeys; admin-only policies; CSRF protection; XSS-safe Markdown; URL scheme validation; uploads re-encoded with GD; private résumé storage; honeypot, timing check and rate limits on the contact form; security headers.
- **Performance:** optional server-side rendering (SSR), WebP image variants, self-hosted WOFF2 fonts, code-split admin bundle, and cached public queries that refresh automatically when content changes.

## Requirements

| Software | Version                                                                                                                                |
| -------- | -------------------------------------------------------------------------------------------------------------------------------------- |
| PHP      | **8.4.1+** (the locked Symfony 8 and Pest 5 packages require it) with `gd` (WebP support), `pdo_mysql`, `mbstring`, `fileinfo`, `intl` |
| Composer | 2.x                                                                                                                                    |
| Node.js  | 22+ (for building assets; also needed at runtime only if SSR is used)                                                                  |
| MySQL    | 8.0+ (MariaDB 10.6+ also works). SQLite is used for automated tests.                                                                   |

## Installation (local)

```bash
git clone <repository-url> portfolio && cd portfolio
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Configure the database and the first admin account in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=portfolio
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

ADMIN_NAME="Your Name"
ADMIN_EMAIL=you@example.com
ADMIN_PASSWORD=change-me-please
```

Then create the schema, seed the content and build the assets:

```bash
php artisan migrate --seed
php artisan storage:link
npm run build
php artisan serve
```

Open <http://127.0.0.1:8000>, then sign in at <http://127.0.0.1:8000/login>. If `ADMIN_PASSWORD` is empty, the seeder generates a password and prints it once.

## Environment configuration

| Variable                                      | Purpose                                                                                                                     |
| --------------------------------------------- | --------------------------------------------------------------------------------------------------------------------------- |
| `APP_URL`                                     | Absolute site URL. Used for canonical URLs, the sitemap and image URLs.                                                     |
| `ADMIN_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Admin account created by the seeder. In production, `php artisan portfolio:admin` is preferred.                             |
| `SEO_ALLOW_INDEXING`                          | Lets search engines crawl the site. Defaults to `true` only when `APP_ENV=production`.                                      |
| `INERTIA_SSR_ENABLED`                         | Use the SSR server if it is running (default `true`; falls back to client rendering automatically).                         |
| `CONTACT_NOTIFY_EMAIL`                        | Address that receives new contact-form messages. Turns notifications on by default; can be changed in Admin → Settings.     |
| `MAIL_*`                                      | Mail transport for those notifications. `.env.example` includes a commented Gmail SMTP block (needs a Google app password). |

## Database setup

- `php artisan migrate` creates all tables. See [docs/database-schema.md](docs/database-schema.md).
- `php artisan db:seed` creates the admin user, homepage sections, navigation, SEO pages, a privacy policy, and portfolio content taken from the owner's CV. Unknown values (LinkedIn and GitHub URLs, public email, photo, thumbnails) are left empty or unpublished; the admin dashboard lists them under **Finish setting up**.
- Seeders are idempotent: running them again does not duplicate content.

## Development commands

| Command                                       | What it does                                                    |
| --------------------------------------------- | --------------------------------------------------------------- |
| `composer dev`                                | Laravel server, Vite dev server and log tailing together        |
| `npm run dev`                                 | Vite dev server only                                            |
| `php artisan test`                            | Backend tests (Pest)                                            |
| `npm test`                                    | Frontend component tests (Vitest)                               |
| `npm run build && npm run test:browser`       | End-to-end browser tests (Playwright, isolated SQLite database) |
| `npm run check`                               | Format and lint the frontend (Vite+, oxlint, oxfmt)             |
| `npm run types:check`                         | Type-check Vue and TypeScript                                   |
| `composer lint` / `composer types:check`      | Pint formatting / Larastan (level 7)                            |
| `composer ci:check`                           | Everything CI runs, except the browser tests                    |
| `php artisan portfolio:admin you@example.com` | Create an admin, or reset an admin's password                   |

## Production build

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build:ssr     # or `npm run build` without SSR
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

## Deployment

See [docs/deployment-guide.md](docs/deployment-guide.md) for a complete Linux VPS guide: nginx, HTTPS, permissions, the optional SSR service, caching and backups.

## Documentation

- [Architecture](docs/architecture.md)
- [Database schema](docs/database-schema.md)
- [Admin guide](docs/admin-guide.md): every admin page and what it does
- [Content management guide](docs/content-management-guide.md)
- [Deployment guide](docs/deployment-guide.md)
- [Testing guide](docs/testing-guide.md)
- [Progress and status](progress.md)
