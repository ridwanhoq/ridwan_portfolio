import { test as setup } from '@playwright/test';
import { adminState, login } from './helpers';

// Sign in once and reuse the session, so admin tests do not trip the login throttle.
setup('authenticate as admin', async ({ page }) => {
    await login(page);
    await page.context().storageState({ path: adminState });
});
