<script setup lang="ts">
import { Head, Link, router, setLayoutProps, useForm } from '@inertiajs/vue3';
import {
    ArrowLeft,
    ExternalLink,
    LoaderCircle,
    Save,
    Trash2,
} from '@lucide/vue';
import { computed, provide, reactive } from 'vue';
import { toast } from 'vue-sonner';
import { resources } from '@/admin/resources';
import type { Field, Option } from '@/admin/types';
import AdminPageHeader from '@/components/admin/AdminPageHeader.vue';
import { mediaCacheKey, optionsKey } from '@/components/admin/fields/context';
import FormField from '@/components/admin/fields/FormField.vue';
import { Button } from '@/components/ui/button';
import { confirm } from '@/composables/useConfirm';
import type { MediaItem } from '@/types';

const props = defineProps<{
    resource: string;
    item: Record<string, unknown>;
    itemId: number | null;
    options: Record<string, Option[]>;
    abilities: { create: boolean; delete: boolean };
    action?: { url: string; method: 'put' | 'post' };
    extraActions?: { label: string; url: string; method: 'post' }[];
}>();

const config = computed(() => resources[props.resource]);
const base = `/admin/${props.resource}`;
const isSingleton = Boolean(props.action);
const isEdit = props.itemId !== null || isSingleton;
const title = isSingleton
    ? config.value.title
    : isEdit
      ? `Edit ${config.value.singular}`
      : `New ${config.value.singular}`;

setLayoutProps({
    breadcrumbs: isSingleton
        ? [{ title: config.value.title, href: props.action!.url }]
        : [
              { title: config.value.title, href: base },
              {
                  title: isEdit ? 'Edit' : 'New',
                  href: isEdit
                      ? `${base}/${props.itemId}/edit`
                      : `${base}/create`,
              },
          ],
});

// Media previews and option lists are shared with nested field components.
provide(
    mediaCacheKey,
    reactive({
        ...(props.item.media as Record<number, MediaItem> | undefined),
    }),
);
provide(optionsKey, props.options);

const fieldKeys = config.value.groups.flatMap((g) =>
    g.fields.map((f) => f.key),
);
// Field values are dynamic per module, so the form is typed loosely.
type FormValues = Record<string, any>; // eslint-disable-line @typescript-eslint/no-explicit-any
const form = useForm<FormValues>(
    Object.fromEntries(fieldKeys.map((key) => [key, props.item[key] ?? null])),
);
const values = form as unknown as Record<string, unknown>;

function visible(field: Field): boolean {
    return (
        !field.showIf ||
        Boolean(values[field.showIf.field]) === field.showIf.equals
    );
}

function submit(): void {
    const options = {
        preserveScroll: true,
        onError: () => toast.error('Please fix the highlighted fields.'),
    };

    if (props.action) {
        form[props.action.method](props.action.url, options);
    } else if (props.itemId) {
        form.put(`${base}/${props.itemId}`, options);
    } else {
        form.post(base, options);
    }
}

async function destroy(): Promise<void> {
    const ok = await confirm({
        title: `Delete this ${config.value.singular}?`,
        description: 'This cannot be undone.',
        confirmLabel: 'Delete',
        destructive: true,
    });

    if (ok && props.itemId) {
        router.delete(`${base}/${props.itemId}`);
    }
}

const errors = computed(() => form.errors as Record<string, string>);
const errorCount = computed(() => Object.keys(errors.value).length);
const publicUrl = computed(
    () => (props.item.public_url as string | null | undefined) ?? null,
);
</script>

<template>
    <Head :title="`${title} · Admin`" />

    <form
        class="flex flex-col gap-6 p-4 md:p-6"
        novalidate
        data-test="resource-form"
        @submit.prevent="submit"
    >
        <AdminPageHeader :title="title" :description="config.description">
            <template #actions>
                <Button v-if="publicUrl" variant="outline" as-child>
                    <a :href="publicUrl" target="_blank" rel="noopener"
                        ><ExternalLink /> View</a
                    >
                </Button>
                <Button
                    v-for="extra in extraActions ?? []"
                    :key="extra.url"
                    type="button"
                    variant="outline"
                    :disabled="form.isDirty"
                    :title="
                        form.isDirty ? 'Save your changes first' : undefined
                    "
                    @click="
                        router.post(extra.url, {}, { preserveScroll: true })
                    "
                >
                    {{ extra.label }}
                </Button>
                <Button v-if="!isSingleton" variant="ghost" as-child>
                    <Link :href="base"><ArrowLeft /> Back</Link>
                </Button>
            </template>
        </AdminPageHeader>

        <div
            v-if="errorCount"
            class="rounded-md border border-destructive/30 bg-destructive/5 px-4 py-3 text-sm text-destructive"
            role="alert"
        >
            {{
                errorCount === 1
                    ? 'One field needs attention.'
                    : `${errorCount} fields need attention.`
            }}
        </div>

        <section
            v-for="group in config.groups"
            :key="group.title"
            class="rounded-lg border border-border bg-card"
            :aria-labelledby="`group-${group.title}`"
        >
            <header class="border-b border-border px-5 py-4">
                <h2 :id="`group-${group.title}`" class="font-semibold">
                    {{ group.title }}
                </h2>
                <p
                    v-if="group.description"
                    class="mt-0.5 text-sm text-muted-foreground"
                >
                    {{ group.description }}
                </p>
            </header>
            <div class="grid gap-5 p-5 md:grid-cols-2">
                <template v-for="field in group.fields" :key="field.key">
                    <div
                        v-if="visible(field)"
                        :class="field.span === 1 ? '' : 'md:col-span-2'"
                    >
                        <FormField
                            :field="field"
                            :model-value="values[field.key]"
                            :path="field.key"
                            :errors="errors"
                            @update:model-value="values[field.key] = $event"
                        />
                    </div>
                </template>
            </div>
        </section>

        <div
            class="sticky bottom-0 -mx-4 flex items-center justify-between gap-3 border-t border-border bg-background/95 px-4 py-3 backdrop-blur md:-mx-6 md:px-6"
        >
            <div>
                <Button
                    v-if="itemId && abilities.delete && !isSingleton"
                    type="button"
                    variant="ghost"
                    class="text-destructive hover:bg-destructive/10 hover:text-destructive"
                    @click="destroy"
                >
                    <Trash2 /> Delete
                </Button>
            </div>
            <div class="flex items-center gap-3">
                <span
                    v-if="form.isDirty"
                    class="hidden text-xs text-muted-foreground sm:inline"
                    >Unsaved changes</span
                >
                <Button
                    type="submit"
                    :disabled="form.processing"
                    data-test="resource-save"
                >
                    <LoaderCircle v-if="form.processing" class="animate-spin" />
                    <Save v-else />
                    {{ isEdit ? 'Save changes' : `Create ${config.singular}` }}
                </Button>
            </div>
        </div>
    </form>
</template>
