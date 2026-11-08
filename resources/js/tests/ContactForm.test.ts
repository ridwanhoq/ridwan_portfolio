import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';
import { reactive } from 'vue';

const post = vi.fn();
const toastError = vi.fn();
let form: Record<string, unknown>;

vi.mock('@inertiajs/vue3', () => ({
    useForm: (data: Record<string, unknown>) => {
        form = reactive({
            ...data,
            errors: {},
            processing: false,
            post,
            reset: vi.fn(),
        });

        return form;
    },
}));
vi.mock('vue-sonner', () => ({
    toast: { error: (m: string) => toastError(m), success: vi.fn() },
}));

const { default: ContactForm } =
    await import('@/components/site/ContactForm.vue');

describe('ContactForm', () => {
    beforeEach(() => {
        post.mockReset();
        toastError.mockReset();
    });

    it('renders labelled fields and a hidden honeypot', () => {
        const wrapper = mount(ContactForm, {
            props: { contact: { intro: null, token: 'tok' } },
        });

        for (const id of ['name', 'email', 'subject', 'message']) {
            expect(wrapper.find(`label[for="contact-${id}"]`).exists()).toBe(
                true,
            );
        }

        const honeypot = wrapper.get('#contact-website');
        expect(honeypot.attributes('tabindex')).toBe('-1');
        expect(honeypot.element.closest('[aria-hidden="true"]')).not.toBeNull();
    });

    it('posts the form with the spam token', async () => {
        const wrapper = mount(ContactForm, {
            props: { contact: { intro: null, token: 'signed-token' } },
        });

        await wrapper.get('#contact-name').setValue('Jane');
        await wrapper.get('#contact-email').setValue('jane@example.com');
        await wrapper.get('form').trigger('submit');

        expect(post).toHaveBeenCalledWith(
            '/contact',
            expect.objectContaining({ preserveScroll: true }),
        );
        expect(form.token).toBe('signed-token');
        expect(form.name).toBe('Jane');
    });

    it('shows server validation errors accessibly and raises an error toast', async () => {
        post.mockImplementation(
            (_url: string, options: { onError: () => void }) => {
                (form.errors as Record<string, string>).email =
                    'The email field must be a valid email address.';
                options.onError();
            },
        );

        const wrapper = mount(ContactForm, {
            props: { contact: { intro: null, token: 't' } },
        });
        await wrapper.get('form').trigger('submit');
        await flushPromises();

        const email = wrapper.get('#contact-email');
        expect(email.attributes('aria-invalid')).toBe('true');
        expect(email.attributes('aria-describedby')).toBe(
            'contact-email-error',
        );
        expect(wrapper.get('#contact-email-error').text()).toContain(
            'valid email',
        );
        expect(toastError).toHaveBeenCalledOnce();
    });

    it('reports network failures with a toast instead of an alert', async () => {
        const alert = vi.spyOn(window, 'alert').mockImplementation(() => {});
        post.mockImplementation(
            (_url: string, options: { onNetworkError: () => void }) =>
                options.onNetworkError(),
        );

        const wrapper = mount(ContactForm, {
            props: { contact: { intro: null, token: 't' } },
        });
        await wrapper.get('form').trigger('submit');

        expect(toastError).toHaveBeenCalledWith(
            expect.stringContaining('could not be sent'),
        );
        expect(alert).not.toHaveBeenCalled();
    });
});
