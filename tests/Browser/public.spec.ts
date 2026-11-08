import { expect, test } from '@playwright/test';
import { trackErrors } from './helpers';

test('homepage renders every section without console errors', async ({
    page,
}) => {
    const errors = trackErrors(page);
    await page.goto('/');

    await expect(page.getByRole('heading', { level: 1 })).toHaveText(
        'Building Reliable Software. Solving Complex Problems.',
    );
    for (const id of [
        'about',
        'experience',
        'projects',
        'skills',
        'approach',
        'education',
        'contact',
    ]) {
        await expect(page.locator(`section#${id}`)).toBeAttached();
    }
    await expect(page.locator('[data-test="availability"]')).toBeVisible();
    await expect(page.locator('[data-test="project-card"]')).toHaveCount(4);
    expect(errors).toEqual([]);
});

test('primary navigation scrolls to sections and marks the active one', async ({
    page,
}) => {
    await page.goto('/');
    await page
        .getByRole('navigation', { name: 'Primary' })
        .getByRole('link', { name: 'Skills' })
        .click();

    await expect(page).toHaveURL(/#skills$/);
    await expect(page.locator('#skills')).toBeInViewport();
    await expect(
        page
            .getByRole('navigation', { name: 'Primary' })
            .getByRole('link', { name: 'Skills' }),
    ).toHaveAttribute('aria-current', 'page');
});

test('skip link moves focus to the main content', async ({ page }) => {
    await page.goto('/');
    await page.keyboard.press('Tab');
    await expect(
        page.getByRole('link', { name: 'Skip to content' }),
    ).toBeFocused();
});

test('theme toggle switches and persists across reloads', async ({ page }) => {
    await page.emulateMedia({ colorScheme: 'light' });
    await page.goto('/');
    await expect(page.locator('html')).not.toHaveClass(/dark/);

    await page.locator('[data-test="theme-toggle"]').click();
    await expect(page.locator('html')).toHaveClass(/dark/);

    await page.reload();
    await expect(page.locator('html')).toHaveClass(/dark/);
});

test('project card opens the case study', async ({ page }) => {
    const errors = trackErrors(page);
    await page.goto('/projects');
    await page.getByRole('link', { name: /Sirajganj Economic Zone/ }).click();

    await expect(page).toHaveURL(/\/projects\/sezl-corporate-website-and-cms$/);
    await expect(page.getByRole('heading', { level: 1 })).toContainText(
        'Sirajganj Economic Zone',
    );
    await expect(
        page.getByRole('heading', { name: 'Architecture decisions' }),
    ).toBeVisible();
    await expect(page).toHaveTitle(/Sirajganj Economic Zone .* — Case study/);
    expect(errors).toEqual([]);
});

test('project category filter narrows the list', async ({ page }) => {
    await page.goto('/projects');
    await page.getByRole('button', { name: /Websites & CMS/ }).click();
    await expect(page.locator('[data-test="project-card"]')).toHaveCount(2);
    await expect(page).toHaveURL(/category=websites-cms/);
});

test('résumé downloads as a PDF', async ({ page }) => {
    await page.goto('/resume');
    const [download] = await Promise.all([
        page.waitForEvent('download'),
        page.locator('[data-test="resume-download"]').click(),
    ]);

    expect(download.suggestedFilename()).toBe(
        'mohammad-ridwanul-haque-resume.pdf',
    );
});

test('contact form validates and then sends', async ({ page }) => {
    await page.goto('/contact');
    const form = page.locator('[data-test="contact-form"]');

    await form.locator('[data-test="contact-submit"]').click();
    await expect(page.locator('#contact-name-error')).toBeVisible();
    await expect(
        page.getByText('Please check the highlighted fields'),
    ).toBeVisible();

    await page.fill('#contact-name', 'Jane Recruiter');
    await page.fill('#contact-email', 'jane@example.com');
    await page.fill('#contact-subject', 'Senior Laravel role');
    await page.fill(
        '#contact-message',
        'We would like to discuss a remote Senior Laravel position with you.',
    );
    await page.waitForTimeout(3200); // the spam timer requires a few seconds on the page
    await form.locator('[data-test="contact-submit"]').click();

    await expect(page.getByText('Thanks for your message')).toBeVisible();
    await expect(page.locator('#contact-name')).toHaveValue('');
});

test('unknown pages show the branded 404', async ({ page }) => {
    const response = await page.goto('/this-page-does-not-exist');
    expect(response?.status()).toBe(404);
    await expect(page.getByRole('heading', { level: 1 })).toHaveText(
        'Page not found',
    );
    await page.getByRole('link', { name: 'Back to home' }).click();
    await expect(page).toHaveURL(/\/$/);
});

test('every internal link on public pages resolves', async ({
    page,
    request,
}) => {
    const seen = new Set<string>();
    const queue = [
        '/',
        '/about',
        '/projects',
        '/resume',
        '/contact',
        '/privacy',
    ];
    const broken: string[] = [];

    while (queue.length) {
        const path = queue.shift()!;
        if (seen.has(path)) continue;
        seen.add(path);

        const response = await request.get(path);
        if (response.status() >= 400) {
            broken.push(`${path} → ${response.status()}`);
            continue;
        }
        if (!(response.headers()['content-type'] ?? '').includes('text/html'))
            continue;

        await page.goto(path);
        const hrefs = await page.$$eval('a[href]', (as) =>
            as.map((a) => a.getAttribute('href') ?? ''),
        );
        for (const href of hrefs) {
            if (!href.startsWith('/') || href.startsWith('//')) continue;
            const clean = href.split('#')[0] || '/';
            if (!seen.has(clean) && !clean.startsWith('/admin'))
                queue.push(clean);
        }
    }

    expect(broken).toEqual([]);
    expect(seen.size).toBeGreaterThan(8);
});

test('mobile menu opens, closes with Escape and navigates @mobile', async ({
    page,
}) => {
    await page.goto('/');
    const button = page.locator('[data-test="mobile-menu-button"]');
    await button.click();
    await expect(page.locator('[data-test="mobile-menu"]')).toBeVisible();
    await expect(button).toHaveAttribute('aria-expanded', 'true');

    await page.keyboard.press('Escape');
    await expect(page.locator('[data-test="mobile-menu"]')).toBeHidden();
    await expect(button).toBeFocused();

    await button.click();
    await page
        .locator('[data-test="mobile-menu"]')
        .getByRole('link', { name: 'Projects' })
        .click();
    await expect(page.locator('[data-test="mobile-menu"]')).toBeHidden();
    await expect(page.locator('#projects')).toBeInViewport();
});

test('layout does not overflow horizontally on mobile @mobile', async ({
    page,
}) => {
    for (const path of [
        '/',
        '/about',
        '/projects',
        '/projects/itlms-training-and-learning-management-system',
        '/resume',
        '/contact',
    ]) {
        await page.goto(path);
        const overflow = await page.evaluate(
            () =>
                document.documentElement.scrollWidth -
                document.documentElement.clientWidth,
        );
        expect(overflow, path).toBeLessThanOrEqual(0);
    }
});
