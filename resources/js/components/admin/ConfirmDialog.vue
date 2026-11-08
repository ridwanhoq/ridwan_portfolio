<script setup lang="ts">
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { confirmState, settleConfirm } from '@/composables/useConfirm';

function onOpenChange(open: boolean): void {
    if (!open) {
        settleConfirm(false);
    }
}
</script>

<template>
    <Dialog :open="confirmState.open" @update:open="onOpenChange">
        <DialogContent class="sm:max-w-md" data-test="confirm-dialog">
            <DialogHeader>
                <DialogTitle>{{ confirmState.title }}</DialogTitle>
                <DialogDescription v-if="confirmState.description">{{
                    confirmState.description
                }}</DialogDescription>
            </DialogHeader>
            <DialogFooter class="gap-2">
                <Button variant="outline" @click="settleConfirm(false)">{{
                    confirmState.cancelLabel
                }}</Button>
                <Button
                    :variant="
                        confirmState.destructive ? 'destructive' : 'default'
                    "
                    data-test="confirm-accept"
                    @click="settleConfirm(true)"
                >
                    {{ confirmState.confirmLabel }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
