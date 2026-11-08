export type Option = { value: string | number; label: string };

export type ColumnType =
    | 'text'
    | 'title'
    | 'toggle'
    | 'status'
    | 'date'
    | 'image'
    | 'count'
    | 'badge';

export type Column = {
    key: string;
    label: string;
    type?: ColumnType;
    sortable?: boolean;
    /** Secondary text shown under a "title" column. */
    sub?: string;
    class?: string;
};

type BaseField = {
    key: string;
    label: string;
    help?: string;
    placeholder?: string;
    required?: boolean;
    /** Grid width inside its group (out of 2). */
    span?: 1 | 2;
    /** Only show when another boolean field is on/off. */
    showIf?: { field: string; equals: unknown };
    maxLength?: number;
};

export type Field =
    | (BaseField & {
          type:
              | 'text'
              | 'email'
              | 'url'
              | 'number'
              | 'month'
              | 'datetime'
              | 'textarea'
              | 'markdown'
              | 'switch'
              | 'list'
              | 'slug'
              | 'readonly';
          rows?: number;
      })
    | (BaseField & {
          type: 'select';
          options: string | Option[];
          nullable?: boolean;
      })
    | (BaseField & { type: 'multiselect'; options: string })
    | (BaseField & { type: 'media' })
    | (BaseField & {
          type: 'repeater';
          fields: Field[];
          addLabel: string;
          max?: number;
      });

export type FieldGroup = {
    title: string;
    description?: string;
    fields: Field[];
};

export type Filter = { key: string; label: string; options: string | Option[] };

export type ResourceConfig = {
    title: string;
    singular: string;
    description?: string;
    columns: Column[];
    groups: FieldGroup[];
    filters?: Filter[];
    searchable?: boolean;
    /** Link to the public page for a row, when it has one. */
    publicUrl?: (row: Record<string, unknown>) => string | null;
};
