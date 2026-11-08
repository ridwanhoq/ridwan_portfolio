/**
 * Small fetch wrapper for the few JSON endpoints the admin uses outside
 * Inertia visits (media picker browse/upload). Sends Laravel's XSRF token.
 */
export class ApiError extends Error {
    constructor(
        message: string,
        public readonly status: number,
        public readonly errors: Record<string, string[]> = {},
    ) {
        super(message);
    }
}

function xsrfToken(): string | null {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);

    return match ? decodeURIComponent(match[1]) : null;
}

async function request<T>(
    method: string,
    url: string,
    body?: FormData | Record<string, unknown>,
): Promise<T> {
    const headers: Record<string, string> = {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };
    const token = xsrfToken();

    if (token) {
        headers['X-XSRF-TOKEN'] = token;
    }

    let payload: BodyInit | undefined;

    if (body instanceof FormData) {
        payload = body;
    } else if (body) {
        headers['Content-Type'] = 'application/json';
        payload = JSON.stringify(body);
    }

    let response: Response;

    try {
        response = await fetch(url, {
            method,
            headers,
            body: payload,
            credentials: 'same-origin',
        });
    } catch {
        throw new ApiError('Network error. Check your connection.', 0);
    }

    const data = (await response.json().catch(() => null)) as {
        message?: string;
        errors?: Record<string, string[]>;
    } | null;

    if (!response.ok) {
        const firstError = data?.errors
            ? Object.values(data.errors)[0]?.[0]
            : undefined;

        throw new ApiError(
            firstError ??
                data?.message ??
                `Request failed (${response.status}).`,
            response.status,
            data?.errors ?? {},
        );
    }

    return data as T;
}

export const api = {
    get: <T>(url: string) => request<T>('GET', url),
    post: <T>(url: string, body?: FormData | Record<string, unknown>) =>
        request<T>('POST', url, body),
};

export type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
    from: number | null;
    to: number | null;
    links: { url: string | null; label: string; active: boolean }[];
    next_page_url: string | null;
    prev_page_url: string | null;
};
