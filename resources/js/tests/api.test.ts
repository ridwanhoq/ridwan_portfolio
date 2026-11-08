import { afterEach, describe, expect, it, vi } from 'vitest';
import { api, ApiError } from '@/lib/api';

function respond(status: number, body: unknown) {
    return vi.fn().mockResolvedValue({
        ok: status < 400,
        status,
        json: () => Promise.resolve(body),
    });
}

describe('api client', () => {
    afterEach(() => vi.unstubAllGlobals());

    it('sends the XSRF token from the cookie', async () => {
        document.cookie = 'XSRF-TOKEN=abc%3D123';
        const fetch = respond(200, { ok: true });
        vi.stubGlobal('fetch', fetch);

        await api.post('/admin/markdown/preview', { markdown: 'x' });

        const headers = fetch.mock.calls[0][1].headers as Record<
            string,
            string
        >;
        expect(headers['X-XSRF-TOKEN']).toBe('abc=123');
        expect(headers['Content-Type']).toBe('application/json');
    });

    it('surfaces the first validation error', async () => {
        vi.stubGlobal(
            'fetch',
            respond(422, {
                message: 'Invalid',
                errors: { file: ['The file must be an image.'] },
            }),
        );

        await expect(
            api.post('/admin/media', new FormData()),
        ).rejects.toMatchObject({
            message: 'The file must be an image.',
            status: 422,
        });
    });

    it('turns network failures into a readable error', async () => {
        vi.stubGlobal(
            'fetch',
            vi.fn().mockRejectedValue(new TypeError('Failed to fetch')),
        );

        const error = (await api
            .get('/admin/media/browse')
            .catch((e: unknown) => e)) as ApiError;
        expect(error).toBeInstanceOf(ApiError);
        expect(error.status).toBe(0);
        expect(error.message).toContain('Network error');
    });

    it('handles non-JSON error responses', async () => {
        vi.stubGlobal(
            'fetch',
            vi.fn().mockResolvedValue({
                ok: false,
                status: 500,
                json: () => Promise.reject(new Error('html')),
            }),
        );

        await expect(api.get('/x')).rejects.toMatchObject({
            message: 'Request failed (500).',
            status: 500,
        });
    });
});
