<?php

namespace App\Http\Requests\Admin;

class SettingsRequest extends AdminRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'site_title' => ['required', 'string', 'max:90'],
            'footer_note' => ['nullable', 'string', 'max:255'],
            'google_site_verification' => ['nullable', 'string', 'max:120', 'regex:/^[\w\-]+$/'],
            'contact_enabled' => ['boolean'],
            'contact_intro' => ['nullable', 'string', 'max:500'],
            'contact_success_message' => ['required', 'string', 'max:255'],
            'contact_notify_enabled' => ['boolean'],
            'contact_notify_email' => ['nullable', 'required_if_accepted:contact_notify_enabled', 'email:rfc', 'max:255'],
            'blog_enabled' => ['boolean'],
            'projects_show_filters' => ['boolean'],
            'privacy_policy' => ['nullable', 'string', 'max:30000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'google_site_verification.regex' => 'Paste only the content value of the verification meta tag.',
            'contact_notify_email.required_if_accepted' => 'Add an email address to receive notifications.',
        ];
    }
}
