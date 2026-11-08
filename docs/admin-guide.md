# Admin guide

Sign in at `/login`. Only accounts with the admin flag can use the panel, and public registration is disabled. To create or reset an admin from the server, run `php artisan portfolio:admin you@example.com`.

## How every list page works

All content modules share the same list and form behaviour:

- **Search:** the search box filters as you type.
- **Filters:** drop-downs where relevant (status, category, menu location).
- **Sorting:** click a column header with arrows; click again to reverse.
- **Pagination:** shown below the table.
- **Quick toggles:** switches in the table (Published, Featured, Visible and so on) save immediately.
- **Edit:** click the title or the pencil icon. **View on site** (arrow icon) appears for published projects.
- **Delete:** the bin icon asks for confirmation first. Deleting keeps your current search and filters.
- **Forms:** fields are grouped into sections. Required fields are marked `*`. Server validation errors appear under each field, with a summary at the top. The save bar stays at the bottom of the screen and shows "Unsaved changes" when relevant.
- **Feedback:** success, warning and error messages appear as toasts in the top-right corner.

## Overview

### Dashboard (`/admin`)

Live counts: total, published and featured projects, experience records, messages, unread messages. Also shows a **Finish setting up** checklist (missing email, LinkedIn or GitHub, photo, project thumbnails), recent messages, recently updated projects, and configuration status (résumé source, notifications, blog, media count).

### Messages (`/admin/messages`)

Contact form inbox. Filter by All, Unread or Read and search by name, email or subject. Opening a message marks it read. A message page has **Reply by email** (opens your mail client), **Mark unread** and **Delete**, plus technical details (IP address and user agent) for spotting abuse.

## Content

### Profile (`/admin/profile`)

Single form with four groups:

- **Identity:** name, short name (header logo), professional title, secondary title, profile photo.
- **Hero:** headline, supporting description, years of experience, years with primary stack, primary stack label, core stack list, availability badge (on/off and text).
- **Contact details:** public email, location, phone (and whether to show it), work preference, languages.
- **About content:** professional summary (used on About, the résumé and structured data), plus Markdown blocks: introduction, engineering philosophy, career overview, technical specialisation, professional interests, remote collaboration. Empty blocks are hidden.

### Experience (`/admin/experiences`)

Job title, company, employment type, location, focus line, company website, start and end month (or **I currently work here**), description, responsibilities, key contributions, technology stack, published, and sort order. The public timeline is always most recent first.

### Projects (`/admin/projects`)

- **Basics:** title, URL slug (generated if blank), category, period, client, type, short description (shown on cards), personal contribution, technologies.
- **Media and links:** thumbnail; gallery of screenshots and diagrams with captions (reorder with the arrows); live demo URL; source URL; extra links.
- **Case study:** 12 optional Markdown sections. Only sections with content appear on the public page.
- **Publishing:** status (Draft or Published), publish date (a future date schedules it), feature on homepage, sort order.
- **SEO:** meta title and description for the case study page.
  Filter the list by status or category. The **Featured** switch controls homepage placement.

### Project categories (`/admin/project-categories`)

Name, slug, description, order. The list shows how many projects use each category. Filters on `/projects` appear once at least two categories have published projects (and can be turned off in Settings).

### Technologies (`/admin/technologies`)

Shared list linked to projects and experience. Shows how many projects and roles use each one.

### Skills (`/admin/skills`) and Skill categories (`/admin/skill-categories`)

Categories are the columns of the Technical Skills section (Backend, Frontend…). Skills belong to one category and can be filtered by category. There are no proficiency percentages, by design.

### Engineering approach (`/admin/principles`)

The blocks in the "How I build software" section: title, icon, Markdown body, published, order.

### Education (`/admin/education`) and Certifications (`/admin/certifications`)

Optional. The public section hides itself when both are empty. Certifications support issuer, type (certification, training or award), issue and expiry month, credential ID, credential URL and a short note. Only the year is shown publicly.

### Blog posts (`/admin/posts`)

Title, slug, excerpt, Markdown body, cover image, status, publish date, meta description. The blog is only public when it is enabled in **Settings**.

## Website

### Homepage sections (`/admin/sections`)

Turn each homepage section on or off (switch in the list), change its order (Order field) and edit its eyebrow label, heading and intro. Sections without content are hidden automatically. Sections can't be created or deleted.

### Navigation (`/admin/navigation`)

Header and footer menu items: label, URL, menu, open in new tab, visible, order. Use `/#about`, `/#experience`, `/#projects`, `/#skills`, `/#approach`, `/#education` or `/#contact` for homepage anchors, `/projects`-style paths for pages, or full `https://` URLs. `javascript:` and similar schemes are rejected.

### Social links (`/admin/social-links`)

Platform, label, URL (`https://` or `mailto:`), show in hero, published, order. Published links appear in the hero (if enabled), contact section and footer, and in the structured data used by search engines. The seeded LinkedIn and GitHub links contain `PLACEHOLDER` and are unpublished until you edit them.

### Résumé (`/admin/resume`)

Choose what **Download résumé** delivers:

- **Generated PDF:** built automatically from your profile, experience, skills, projects and education. Use **Preview** to view it.
- **Uploaded PDF:** upload your own PDF (up to 5 MB, real PDF files only). It is stored privately and served only through `/resume/download`. **Remove** switches back to the generated version.

### Media library (`/admin/media`)

Upload JPG, PNG, WebP or GIF images (up to 8 MB; drag and drop supported). Each upload is re-encoded, stripped of metadata and given WebP versions automatically. Click an image to edit its **alt text** and caption, copy its URL, or delete it. Images missing alt text are flagged. Images can also be uploaded straight from any image field through the picker.

### SEO (`/admin/seo`)

Per page (Home, About, Projects, Résumé, Contact, Privacy, Blog): title, meta description (with character counter), social share image and **Hide from search engines**. Project case studies use their own SEO fields. Pages can't be added or deleted here.

### Settings (`/admin/settings`)

- **General:** default page title, footer note, Google Search Console verification code.
- **Contact form:** accept messages on or off, intro text, success message, email notifications (on or off, and the address). The address defaults to `CONTACT_NOTIFY_EMAIL` from `.env`. Use **Send test email** (top of the page) to confirm delivery; it warns you if mail is still set to the log driver.
- **Features:** project category filters; enable the blog.
- **Privacy policy:** Markdown content for `/privacy`.

## Account

Use the user menu at the bottom of the sidebar for **Settings** (name and email, password, two-factor authentication, passkeys, appearance) and **Log out**.
