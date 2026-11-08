import type { InjectionKey } from 'vue';
import type { Option } from '@/admin/types';
import type { MediaItem } from '@/types';

/** Media records already known to the form, keyed by id (for previews). */
export const mediaCacheKey: InjectionKey<Record<number, MediaItem>> =
    Symbol('mediaCache');

/** Option lists sent by the controller (e.g. categories, technologies). */
export const optionsKey: InjectionKey<Record<string, Option[]>> =
    Symbol('options');

export const inputClass =
    'h-9 w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive dark:bg-input/30';

export const textareaClass =
    'w-full min-w-0 rounded-md border border-input bg-transparent px-3 py-2 text-sm leading-relaxed shadow-xs transition-[color,box-shadow] outline-none placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 aria-invalid:border-destructive dark:bg-input/30';
