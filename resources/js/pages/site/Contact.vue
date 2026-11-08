<script setup lang="ts">
import ContactDetails from '@/components/site/ContactDetails.vue';
import ContactForm from '@/components/site/ContactForm.vue';
import PageHeader from '@/components/site/PageHeader.vue';
import SeoHead from '@/components/site/SeoHead.vue';
import type { ContactConfig, Profile, Seo } from '@/types';

defineProps<{ seo: Seo; profile: Profile; contact: ContactConfig }>();
</script>

<template>
    <SeoHead :seo="seo" />

    <PageHeader
        eyebrow="Contact"
        title="Let’s talk about your next project"
        :intro="contact.intro"
        :breadcrumbs="[{ label: 'Home', href: '/' }, { label: 'Contact' }]"
    />

    <section class="py-16 sm:py-20" aria-label="Contact options">
        <div class="container-site grid gap-12 lg:grid-cols-12 lg:gap-10">
            <div class="lg:col-span-4">
                <h2 class="eyebrow mb-6">Contact details</h2>
                <ContactDetails :profile="profile" />
            </div>
            <div class="lg:col-span-8">
                <div class="rounded-lg border border-border bg-card p-6 sm:p-8">
                    <h2 class="mb-6 text-xl font-semibold tracking-tight">
                        Send a message
                    </h2>
                    <ContactForm
                        v-if="$page.props.site?.contact_enabled"
                        :contact="contact"
                    />
                    <p v-else class="text-muted-foreground">
                        The contact form is currently unavailable. Please use
                        the details listed here.
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
