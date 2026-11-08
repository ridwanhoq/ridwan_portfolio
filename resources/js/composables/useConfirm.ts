import { reactive } from 'vue';

export type ConfirmOptions = {
    title: string;
    description?: string;
    confirmLabel?: string;
    cancelLabel?: string;
    destructive?: boolean;
};

type ConfirmState = ConfirmOptions & {
    open: boolean;
    resolve: ((value: boolean) => void) | null;
};

/**
 * Promise-based confirmation dialog, rendered once by <ConfirmDialog />.
 * Replaces window.confirm().
 */
export const confirmState = reactive<ConfirmState>({
    open: false,
    title: '',
    resolve: null,
});

export function confirm(options: ConfirmOptions): Promise<boolean> {
    confirmState.resolve?.(false);

    Object.assign(confirmState, {
        description: undefined,
        confirmLabel: 'Confirm',
        cancelLabel: 'Cancel',
        destructive: false,
        ...options,
        open: true,
    });

    return new Promise((resolve) => {
        confirmState.resolve = resolve;
    });
}

export function settleConfirm(value: boolean): void {
    confirmState.resolve?.(value);
    confirmState.resolve = null;
    confirmState.open = false;
}

export function useConfirm() {
    return { confirm };
}
