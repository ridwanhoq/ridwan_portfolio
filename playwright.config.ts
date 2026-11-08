import { defineConfig, devices } from '@playwright/test';

const port = 8200;

/**
 * Browser tests run against a throwaway SQLite database that is migrated
 * and seeded on start, so they never touch development data.
 * PHP's built-in server is used directly because `artisan serve` does not
 * pass these environment variables through to its workers.
 * Build assets first: `npm run build`.
 */
export default defineConfig({
    testDir: 'tests/Browser',
    fullyParallel: false,
    workers: 1,
    retries: process.env.CI ? 1 : 0,
    reporter: [['list']],
    use: {
        baseURL: `http://127.0.0.1:${port}`,
        trace: 'retain-on-failure',
    },
    projects: [
        { name: 'setup', testMatch: /auth\.setup\.ts/ },
        {
            name: 'desktop',
            use: { ...devices['Desktop Chrome'] },
            grepInvert: /@mobile/,
            dependencies: ['setup'],
        },
        { name: 'mobile', use: { ...devices['Pixel 7'] }, grep: /@mobile/ },
    ],
    webServer: {
        command: `touch database/e2e.sqlite && php artisan cache:clear && php artisan migrate:fresh --seed --force && cd public && php -S 127.0.0.1:${port} ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php`,
        url: `http://127.0.0.1:${port}/up`,
        reuseExistingServer: false,
        timeout: 120_000,
        env: {
            APP_ENV: 'local',
            PHP_CLI_SERVER_WORKERS: '4',
            APP_URL: `http://127.0.0.1:${port}`,
            DB_CONNECTION: 'sqlite',
            DB_DATABASE: `${process.cwd()}/database/e2e.sqlite`,
            CACHE_STORE: 'file',
            SESSION_DRIVER: 'file',
            MAIL_MAILER: 'array',
            ADMIN_EMAIL: 'admin@example.com',
            ADMIN_PASSWORD: 'password',
        },
    },
});
