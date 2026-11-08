import { expect } from '@playwright/test';
import type { Page } from '@playwright/test';

/** Saved admin session, created by auth.setup.ts. */
export const adminState = 'test-results/.auth/admin.json';

/** Collects console errors and uncaught exceptions for a page. */
export function trackErrors(page: Page): string[] {
    const errors: string[] = [];
    page.on('console', (m) => m.type() === 'error' && errors.push(m.text()));
    page.on('pageerror', (e) => errors.push(e.message));

    return errors;
}

export async function login(page: Page): Promise<void> {
    await page.goto('/login');
    await page.fill('#email', 'admin@example.com');
    await page.fill('#password', 'password');
    await page.click('[data-test="login-button"]');
    await expect(page).toHaveURL(/\/admin$/);
}
