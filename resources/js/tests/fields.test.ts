import { mount } from '@vue/test-utils';
import { describe, expect, it } from 'vitest';
import ListField from '@/components/admin/fields/ListField.vue';
import MultiSelectField from '@/components/admin/fields/MultiSelectField.vue';
import ToggleSwitch from '@/components/admin/ToggleSwitch.vue';

describe('admin form fields', () => {
    it('ToggleSwitch exposes switch semantics', async () => {
        const wrapper = mount(ToggleSwitch, {
            props: { modelValue: false, label: 'Published' },
        });
        const button = wrapper.get('button');

        expect(button.attributes('role')).toBe('switch');
        expect(button.attributes('aria-checked')).toBe('false');
        expect(button.attributes('aria-label')).toBe('Published');

        await button.trigger('click');
        expect(wrapper.emitted('update:modelValue')?.[0]).toEqual([true]);
    });

    it('ListField adds, edits, reorders and removes items', async () => {
        const wrapper = mount(ListField, {
            props: { id: 'list', label: 'Items', modelValue: ['a', 'b'] },
        });

        await wrapper
            .findAll('button')
            .find((b) => b.text().includes('Add item'))!
            .trigger('click');
        expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([
            ['a', 'b', ''],
        ]);

        await wrapper.findAll('input')[0].setValue('A');
        expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([
            ['A', 'b'],
        ]);

        await wrapper
            .findAll('button[aria-label="Move down"]')[0]
            .trigger('click');
        expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([
            ['b', 'a'],
        ]);

        await wrapper
            .findAll('button[aria-label="Remove item"]')[1]
            .trigger('click');
        expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([['a']]);
    });

    it('MultiSelectField toggles options', async () => {
        const wrapper = mount(MultiSelectField, {
            props: {
                id: 'tech',
                label: 'Tech',
                modelValue: [1],
                options: [
                    { value: 1, label: 'PHP' },
                    { value: 2, label: 'Vue' },
                ],
            },
        });

        const [php, vue] = wrapper.findAll('button');
        expect(php.attributes('aria-pressed')).toBe('true');

        await vue.trigger('click');
        expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([[1, 2]]);

        await php.trigger('click');
        expect(wrapper.emitted('update:modelValue')?.at(-1)).toEqual([[]]);
    });
});
