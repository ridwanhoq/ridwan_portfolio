import { expect, test } from '@playwright/test';
import { adminState, login, trackErrors } from './helpers';

test('admin can sign in and sees real dashboard numbers', async ({ page }) => {
    const errors = trackErrors(page);
    await login(page);

    await expect(
        page.getByRole('heading', { name: 'Dashboard' }),
    ).toBeVisible();
    await expect(page.locator('[data-test="stat-value"]').first()).toHaveText(
        '4',
    );
    await expect(page.getByText('Finish setting up')).toBeVisible();
    expect(errors).toEqual([]);
});

test('wrong password shows an error', async ({ page }) => {
    await page.goto('/login');
    await page.fill('#email', 'admin@example.com');
    await page.fill('#password', 'nope');
    await page.click('[data-test="login-button"]');
    await expect(page.getByText(/credentials do not match/i)).toBeVisible();
});

test.describe('signed in', () => {
    test.use({ storageState: adminState });

    test('create, edit, toggle and delete a technology', async ({ page }) => {
        await page.goto('/admin/technologies');

        await page.locator('[data-test="resource-create"]').click();
        await page.locator('[data-test="resource-save"]').click();
        await expect(
            page.locator('[data-test="field-error"]').first(),
        ).toContainText('required');

        await page.fill('#field-name', 'Playwright');
        await page.locator('[data-test="resource-save"]').click();
        await expect(page.getByText('Technology created.')).toBeVisible();

        await page.fill('[data-test="resource-search"]', 'Playwright');
        await expect(
            page.locator('[data-test="resource-table"] tbody tr'),
        ).toHaveCount(1);

        await page.getByRole('link', { name: 'Playwright' }).click();
        await page.fill('#field-name', 'Playwright Test');
        await page.locator('[data-test="resource-save"]').click();
        await expect(page.getByText('Technology saved.')).toBeVisible();
        await expect(page.locator('#field-slug')).toHaveValue('playwright');

        await page.goto('/admin/technologies?search=Playwright');
        await page.locator('[data-test="resource-delete"]').click();
        await expect(
            page.locator('[data-test="confirm-dialog"]'),
        ).toBeVisible();
        await page.locator('[data-test="confirm-accept"]').click();
        await expect(page.getByText('Technology deleted.')).toBeVisible();
        await expect(page.locator('[data-test="empty-state"]')).toBeVisible();
    });

    test('toggling a project from the list updates the homepage', async ({
        page,
    }) => {
        await page.goto('/admin/projects');
        await page.getByRole('switch', { name: /Featured: Sirajganj/ }).click();
        await expect(page.getByText('Project updated.')).toBeVisible();

        await page.goto('/');
        await expect(page.locator('[data-test="project-card"]')).toHaveCount(3);

        // Restore the seeded state for other tests.
        await page.goto('/admin/projects');
        await page.getByRole('switch', { name: /Featured: Sirajganj/ }).click();
        await expect(page.getByText('Project updated.')).toBeVisible();
    });

    test('uploading an image to the media library and attaching it to a project', async ({
        page,
    }) => {
        await page.goto('/admin/media');
        await page
            .locator('[data-test="media-file"]')
            .setInputFiles('tests/Browser/fixtures/sample.png');
        await expect(page.getByText('Image uploaded.')).toBeVisible();
        await expect(page.locator('[data-test="media-grid"] li')).toHaveCount(
            1,
        );

        await page.goto('/admin/projects/1/edit');
        await page.locator('[data-test="media-choose"]').first().click();
        await page
            .locator('[data-test="media-picker"] ul button')
            .first()
            .click();
        await page.locator('[data-test="resource-save"]').click();
        await expect(page.getByText('Project saved.')).toBeVisible();

        await page.goto('/projects');
        await expect(
            page.locator('[data-test="project-card"] picture img').first(),
        ).toBeVisible();
    });

    test('message from the contact form appears in the inbox', async ({
        page,
    }) => {
        await page.goto('/contact');
        await page.fill('#contact-name', 'Jane Recruiter');
        await page.fill('#contact-email', 'jane@example.com');
        await page.fill('#contact-subject', 'Senior Laravel role');
        await page.fill(
            '#contact-message',
            'We would like to discuss a remote Senior Laravel position with you.',
        );
        await page.waitForTimeout(3200);
        await page.locator('[data-test="contact-submit"]').click();
        await expect(page.getByText('Thanks for your message')).toBeVisible();

        await page.goto('/admin/messages');
        const first = page.locator('[data-test="messages-list"] a').first();
        await expect(first).toContainText('Senior Laravel role');
        await first.click();
        await expect(
            page.getByRole('link', { name: 'Reply by email' }),
        ).toHaveAttribute('href', /^mailto:jane@example.com/);
    });
});
