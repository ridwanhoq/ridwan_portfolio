<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import {
    Award,
    Briefcase,
    Code2,
    ExternalLink,
    FileText,
    FolderKanban,
    GraduationCap,
    Images,
    Inbox,
    LayoutDashboard,
    LayoutList,
    Layers,
    Link2,
    Menu as MenuIcon,
    Newspaper,
    Search,
    Settings,
    Tags,
    UserRound,
    Wrench,
} from '@lucide/vue';
import type { LucideIcon } from '@lucide/vue';
import { computed } from 'vue';
import AppLogo from '@/components/AppLogo.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuBadge,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';

const page = usePage();
const unread = computed(() => page.props.admin?.unread_messages ?? 0);
const { isCurrentUrl, isCurrentOrParentUrl } = useCurrentUrl();

type AdminNavItem = {
    title: string;
    href: string;
    icon: LucideIcon;
    exact?: boolean;
    badge?: boolean;
};

const groups: { label: string; items: AdminNavItem[] }[] = [
    {
        label: 'Overview',
        items: [
            {
                title: 'Dashboard',
                href: '/admin',
                icon: LayoutDashboard,
                exact: true,
            },
            {
                title: 'Messages',
                href: '/admin/messages',
                icon: Inbox,
                badge: true,
            },
        ],
    },
    {
        label: 'Content',
        items: [
            { title: 'Profile', href: '/admin/profile', icon: UserRound },
            {
                title: 'Experience',
                href: '/admin/experiences',
                icon: Briefcase,
            },
            { title: 'Projects', href: '/admin/projects', icon: FolderKanban },
            {
                title: 'Project categories',
                href: '/admin/project-categories',
                icon: Tags,
            },
            { title: 'Technologies', href: '/admin/technologies', icon: Code2 },
            { title: 'Skills', href: '/admin/skills', icon: Wrench },
            {
                title: 'Skill categories',
                href: '/admin/skill-categories',
                icon: Layers,
            },
            {
                title: 'Engineering approach',
                href: '/admin/principles',
                icon: LayoutList,
            },
            {
                title: 'Education',
                href: '/admin/education',
                icon: GraduationCap,
            },
            {
                title: 'Certifications',
                href: '/admin/certifications',
                icon: Award,
            },
            { title: 'Blog posts', href: '/admin/posts', icon: Newspaper },
        ],
    },
    {
        label: 'Website',
        items: [
            {
                title: 'Homepage sections',
                href: '/admin/sections',
                icon: LayoutList,
            },
            { title: 'Navigation', href: '/admin/navigation', icon: MenuIcon },
            { title: 'Social links', href: '/admin/social-links', icon: Link2 },
            { title: 'Résumé', href: '/admin/resume', icon: FileText },
            { title: 'Media library', href: '/admin/media', icon: Images },
            { title: 'SEO', href: '/admin/seo', icon: Search },
            { title: 'Settings', href: '/admin/settings', icon: Settings },
        ],
    },
];

function active(item: AdminNavItem): boolean {
    return item.exact
        ? isCurrentUrl(item.href)
        : isCurrentOrParentUrl(item.href);
}
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link href="/admin"><AppLogo /></Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <SidebarGroup
                v-for="group in groups"
                :key="group.label"
                class="px-2 py-0"
            >
                <SidebarGroupLabel>{{ group.label }}</SidebarGroupLabel>
                <SidebarMenu>
                    <SidebarMenuItem
                        v-for="item in group.items"
                        :key="item.href"
                    >
                        <SidebarMenuButton
                            as-child
                            :is-active="active(item)"
                            :tooltip="item.title"
                        >
                            <Link :href="item.href">
                                <component :is="item.icon" />
                                <span>{{ item.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                        <SidebarMenuBadge
                            v-if="item.badge && unread > 0"
                            class="bg-primary text-primary-foreground"
                            :aria-label="`${unread} unread`"
                            >{{ unread }}</SidebarMenuBadge
                        >
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>
        </SidebarContent>

        <SidebarFooter>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton as-child tooltip="View website">
                        <a href="/" target="_blank" rel="noopener"
                            ><ExternalLink /><span>View website</span></a
                        >
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
