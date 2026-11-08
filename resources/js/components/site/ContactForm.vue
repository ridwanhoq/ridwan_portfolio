<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle, Send } from '@lucide/vue';
import { toast } from 'vue-sonner';
import type { ContactConfig } from '@/types';

const props = defineProps<{ contact: ContactConfig }>();

const form = useForm({
    name: '',
    email: '',
    subject: '',
    message: '',
    website: '', // honeypot
    token: props.contact.token,
});

const fields = [
    {
        key: 'name',
        label: 'Name',
        type: 'text',
        autocomplete: 'name',
        max: 120,
    },
    {
        key: 'email',
        label: 'Email',
        type: 'email',
        autocomplete: 'email',
        max: 255,
    },
] as const;

function submit(): void {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => form.reset('name', 'email', 'subject', 'message'),
        onError: () =>
            toast.error('Please check the highlighted fields and try again.'),
        onNetworkError: () => {
            toast.error(
                'Your message could not be sent. Check your connection and try again.',
            );
        },
    });
}

const inputClass =
    'block w-full rounded-md border border-input bg-background px-3.5 py-2.5 text-[15px] text-foreground shadow-xs transition-colors placeholder:text-muted-foreground/70 focus:border-ring focus:ring-2 focus:ring-ring/25 focus:outline-none aria-[invalid=true]:border-destructive';
</script>

<template>
    <form
        class="grid gap-5"
        novalidate
        data-test="contact-form"
        @submit.prevent="submit"
    >
        <div class="grid gap-5 sm:grid-cols-2">
            <div v-for="field in fields" :key="field.key">
                <label
                    :for="`contact-${field.key}`"
                    class="mb-1.5 block text-sm font-medium"
                    >{{ field.label }}</label
                >
                <input
                    :id="`contact-${field.key}`"
                    v-model="form[field.key]"
                    :type="field.type"
                    :name="field.key"
                    :autocomplete="field.autocomplete"
                    :maxlength="field.max"
                    required
                    :class="inputClass"
                    :aria-invalid="form.errors[field.key] ? 'true' : undefined"
                    :aria-describedby="
                        form.errors[field.key]
                            ? `contact-${field.key}-error`
                            : undefined
                    "
                />
                <p
                    v-if="form.errors[field.key]"
                    :id="`contact-${field.key}-error`"
                    class="mt-1.5 text-sm text-destructive"
                >
                    {{ form.errors[field.key] }}
                </p>
            </div>
        </div>

        <div>
            <label
                for="contact-subject"
                class="mb-1.5 block text-sm font-medium"
                >Subject</label
            >
            <input
                id="contact-subject"
                v-model="form.subject"
                type="text"
                name="subject"
                maxlength="200"
                required
                :class="inputClass"
                :aria-invalid="form.errors.subject ? 'true' : undefined"
                :aria-describedby="
                    form.errors.subject ? 'contact-subject-error' : undefined
                "
            />
            <p
                v-if="form.errors.subject"
                id="contact-subject-error"
                class="mt-1.5 text-sm text-destructive"
            >
                {{ form.errors.subject }}
            </p>
        </div>

        <div>
            <label
                for="contact-message"
                class="mb-1.5 block text-sm font-medium"
                >Message</label
            >
            <textarea
                id="contact-message"
                v-model="form.message"
                name="message"
                rows="6"
                maxlength="5000"
                required
                :class="[inputClass, 'resize-y']"
                :aria-invalid="form.errors.message ? 'true' : undefined"
                :aria-describedby="
                    form.errors.message
                        ? 'contact-message-error'
                        : 'contact-message-hint'
                "
            />
            <p
                v-if="form.errors.message"
                id="contact-message-error"
                class="mt-1.5 text-sm text-destructive"
            >
                {{ form.errors.message }}
            </p>
            <p
                v-else
                id="contact-message-hint"
                class="mt-1.5 text-xs text-muted-foreground"
            >
                {{ form.message.length }} / 5000
            </p>
        </div>

        <!-- Honeypot: hidden from people and assistive tech, attractive to bots. -->
        <div
            class="absolute -left-[9999px] h-px w-px overflow-hidden"
            aria-hidden="true"
        >
            <label for="contact-website">Website</label>
            <input
                id="contact-website"
                v-model="form.website"
                type="text"
                name="website"
                tabindex="-1"
                autocomplete="off"
            />
        </div>

        <p
            v-if="form.errors.token || form.errors.website"
            class="text-sm text-destructive"
            role="alert"
        >
            {{ form.errors.token || form.errors.website }}
        </p>

        <div class="flex flex-wrap items-center gap-4">
            <button
                type="submit"
                class="inline-flex items-center gap-2 rounded-md bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground transition-opacity hover:opacity-90 disabled:opacity-60"
                :disabled="form.processing"
                data-test="contact-submit"
            >
                <LoaderCircle
                    v-if="form.processing"
                    class="size-4 animate-spin"
                    aria-hidden="true"
                />
                <Send v-else class="size-4" aria-hidden="true" />
                {{ form.processing ? 'Sending…' : 'Send message' }}
            </button>
            <p class="text-xs text-muted-foreground">
                Your details are only used to reply. See the
                <a href="/privacy" class="link-underline">privacy policy</a>.
            </p>
        </div>
    </form>
</template>
