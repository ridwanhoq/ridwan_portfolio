<script setup lang="ts">
import { Head, router, useForm } from '@inertiajs/vue3';
import { Eye, FileText, LoaderCircle, Trash2, Upload } from '@lucide/vue';
import { ref } from 'vue';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { Button } from '@/components/ui/button';
import { confirm } from '@/composables/useConfirm';

defineOptions({
    layout: { breadcrumbs: [{ title: 'Résumé', href: '/admin/resume' }] },
});

const props = defineProps<{
    source: 'upload' | 'generated';
    upload: { name: string; size: number; uploaded_at: string | null } | null;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const form = useForm<{ file: File | null }>({ file: null });

function choose(event: Event): void {
    form.file = (event.target as HTMLInputElement).files?.[0] ?? null;

    if (form.file) {
        form.post('/admin/resume', {
            forceFormData: true,
            preserveScroll: true,
            onFinish: () => {
                form.reset();
                if (fileInput.value) fileInput.value.value = '';
            },
        });
    }
}

function setSource(source: 'upload' | 'generated'): void {
    if (source !== props.source) {
        router.put(
            '/admin/resume/source',
            { source },
            { preserveScroll: true },
        );
    }
}

async function removeUpload(): Promise<void> {
    const ok = await confirm({
        title: 'Remove the uploaded résumé?',
        description:
            'Visitors will download the PDF generated from your portfolio content instead.',
        confirmLabel: 'Remove',
        destructive: true,
    });

    if (ok) router.delete('/admin/resume', { preserveScroll: true });
}

function formatDate(value: string | null): string {
    return value
        ? new Intl.DateTimeFormat(undefined, {
              dateStyle: 'medium',
              timeStyle: 'short',
          }).format(new Date(value))
        : '';
}
</script>

<template>
    <Head title="Résumé · Admin" />

    <div class="flex flex-col gap-6 p-4 md:p-6">
        <AdminPageHeader
            title="Résumé"
            description="Choose what visitors download from the “Download résumé” buttons."
        />

        <div class="grid gap-4 lg:grid-cols-2">
            <section
                class="rounded-lg border bg-card p-5"
                :class="
                    source === 'generated'
                        ? 'border-primary ring-1 ring-primary'
                        : 'border-border'
                "
                aria-labelledby="generated-heading"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="generated-heading" class="font-semibold">
                            Generated PDF
                        </h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            An ATS-friendly, single-column PDF built
                            automatically from your profile, experience, skills,
                            projects and education. Always up to date.
                        </p>
                    </div>
                    <FileText
                        class="size-5 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                </div>
                <div class="mt-5 flex flex-wrap gap-2">
                    <Button variant="outline" as-child
                        ><a
                            href="/admin/resume/preview"
                            target="_blank"
                            rel="noopener"
                            ><Eye /> Preview</a
                        ></Button
                    >
                    <Button
                        v-if="source !== 'generated'"
                        @click="setSource('generated')"
                        >Use generated PDF</Button
                    >
                    <span
                        v-else
                        class="inline-flex items-center rounded-md bg-primary/10 px-3 text-sm font-medium text-primary"
                        >In use</span
                    >
                </div>
            </section>

            <section
                class="rounded-lg border bg-card p-5"
                :class="
                    source === 'upload'
                        ? 'border-primary ring-1 ring-primary'
                        : 'border-border'
                "
                aria-labelledby="upload-heading"
            >
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="upload-heading" class="font-semibold">
                            Uploaded PDF
                        </h2>
                        <p class="mt-1 text-sm text-muted-foreground">
                            Upload your own designed résumé (PDF, up to 5 MB).
                            Stored privately and served only through the
                            download link.
                        </p>
                    </div>
                    <Upload
                        class="size-5 shrink-0 text-muted-foreground"
                        aria-hidden="true"
                    />
                </div>

                <div
                    v-if="upload"
                    class="mt-4 rounded-md border border-border bg-muted/30 px-4 py-3 text-sm"
                    data-test="resume-upload-info"
                >
                    <p class="font-medium">{{ upload.name }}</p>
                    <p class="text-muted-foreground">
                        {{ Math.round(upload.size / 1024) }} KB · uploaded
                        {{ formatDate(upload.uploaded_at) }}
                    </p>
                </div>

                <p
                    v-if="form.errors.file"
                    class="mt-3 text-sm text-destructive"
                    role="alert"
                >
                    {{ form.errors.file }}
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    <input
                        ref="fileInput"
                        type="file"
                        accept="application/pdf"
                        class="sr-only"
                        data-test="resume-file"
                        @change="choose"
                    />
                    <Button
                        variant="outline"
                        :disabled="form.processing"
                        @click="fileInput?.click()"
                    >
                        <LoaderCircle
                            v-if="form.processing"
                            class="animate-spin"
                        /><Upload v-else />
                        {{ upload ? 'Replace PDF' : 'Upload PDF' }}
                    </Button>
                    <template v-if="upload">
                        <Button
                            v-if="source !== 'upload'"
                            @click="setSource('upload')"
                            >Use uploaded PDF</Button
                        >
                        <span
                            v-else
                            class="inline-flex items-center rounded-md bg-primary/10 px-3 text-sm font-medium text-primary"
                            >In use</span
                        >
                        <Button
                            variant="ghost"
                            class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                            @click="removeUpload"
                            ><Trash2 /> Remove</Button
                        >
                    </template>
                </div>
            </section>
        </div>

        <p class="text-sm text-muted-foreground">
            Public download link:
            <a
                href="/resume/download"
                class="font-mono text-foreground underline underline-offset-4"
                >/resume/download</a
            >
        </p>
    </div>
</template>
