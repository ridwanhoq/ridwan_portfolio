# Content management guide

Practical guidance for keeping the portfolio accurate and effective for international recruiters.

## First steps after installing

The dashboard's **Finish setting up** panel lists what is still missing. In order:

1. **Profile → Contact details:** add a public email address.
2. **Social links:** edit LinkedIn and GitHub, replace `PLACEHOLDER` with your real profile URL, and switch **Published** on.
3. **Profile → Identity:** optionally upload a recent, professional photo. Nothing is shown if you don't.
4. **Projects:** add a 16:9 thumbnail to each project (a screenshot without confidential data).
5. **Projects → Case study:** fill in the sections you can describe confidently (see below).
6. **Résumé:** preview the generated PDF, or upload your own.
7. **SEO → Home:** confirm the title and description; optionally upload a 1200×630 share image.
8. **Settings:** turn on email notifications once mail is configured.

## Writing rules for this site

- **Only publish what is true and verifiable.** Don't add metrics, user counts, revenue or performance gains unless you can back them up in an interview.
- **Respect client confidentiality.** Describe problems and decisions, not proprietary code, internal URLs, credentials or personal data. Blur screenshots where needed.
- **Use first person and plain English.** "I designed the schema…" reads better than "Responsible for…".
- **Keep cards short.** The project _short description_ is one or two sentences; the _personal contribution_ says what you did.

## Writing a strong case study

Every section is optional; aim for 3–6 good ones rather than 12 thin ones.

| Section                | What to write                                                    |
| ---------------------- | ---------------------------------------------------------------- |
| Project overview       | One paragraph: what the product is and who uses it.              |
| Business context       | Why it was built; the organisation's situation.                  |
| Problem statement      | The specific problem the system solved.                          |
| Technical challenges   | The two or three hardest parts and how you solved them.          |
| My role                | Your responsibilities and what you personally built.             |
| Architecture decisions | Key choices and why (monolith vs services, queues, caching…).    |
| Database design        | Main entities and relationships; notable indexes or constraints. |
| API development        | Endpoints, auth model, versioning, integrations.                 |
| Security               | Authentication, authorisation, validation, data protection.      |
| Performance            | What you measured and what you changed.                          |
| Testing and deployment | Test strategy, CI/CD, UAT, release process.                      |
| Results                | Outcomes you can verify.                                         |

Markdown basics: `## Heading`, `**bold**`, `- list item`, `1. numbered`, `` `inline code` ``, fenced code blocks with three backticks, `[link text](https://…)`. Use **Preview** in the editor to check formatting. Raw HTML is removed for security.

Add diagrams as images with the **Diagram** type in the gallery. They are shown separately from screenshots.

## Images

- Upload the largest sensible version (up to 2400 px is kept). WebP copies are created automatically.
- Always set **alt text** in the Media library: describe what the image shows ("Admin dashboard showing attendance by district").
- SVG files aren't accepted; export diagrams as PNG.

## Homepage structure

Use **Homepage sections** to reorder or hide sections. A section with no content (for example, no published certifications or education) is hidden automatically, so you never show an empty block.

## SEO tips

- Titles: under 60 characters, starting with what the page is about.
- Descriptions: 120–160 characters, written for people.
- Mention your target roles naturally (Senior Software Engineer, Senior PHP/Laravel Developer, Backend Engineer). Avoid keyword stuffing.
- After launch, add the Search Console verification code in **Settings** and submit `https://your-domain/sitemap.xml`.

## Keeping the résumé in sync

The generated PDF is rebuilt from your content automatically. If you upload your own PDF, remember to replace it when your experience changes.
