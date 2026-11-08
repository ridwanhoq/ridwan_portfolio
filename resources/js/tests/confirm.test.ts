import { describe, expect, it } from 'vitest';
import { confirm, confirmState, settleConfirm } from '@/composables/useConfirm';

describe('confirm()', () => {
    it('resolves true when accepted', async () => {
        const result = confirm({ title: 'Delete?', destructive: true });
        expect(confirmState.open).toBe(true);
        expect(confirmState.destructive).toBe(true);

        settleConfirm(true);

        await expect(result).resolves.toBe(true);
        expect(confirmState.open).toBe(false);
    });

    it('resolves false when cancelled or superseded', async () => {
        const first = confirm({ title: 'First' });
        const second = confirm({ title: 'Second' });

        await expect(first).resolves.toBe(false);
        expect(confirmState.title).toBe('Second');

        settleConfirm(false);
        await expect(second).resolves.toBe(false);
    });
});
