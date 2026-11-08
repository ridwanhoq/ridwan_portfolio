<?php

use App\Models\User;

$adminPages = [
    '/admin', '/admin/profile', '/admin/settings', '/admin/resume', '/admin/media', '/admin/messages',
    '/admin/experiences', '/admin/experiences/create', '/admin/projects', '/admin/projects/create',
    '/admin/project-categories', '/admin/technologies', '/admin/skills', '/admin/skill-categories',
    '/admin/principles', '/admin/education', '/admin/certifications', '/admin/social-links',
    '/admin/navigation', '/admin/posts', '/admin/posts/create', '/admin/seo', '/admin/sections',
];

beforeEach(fn () => seedPortfolio());

it('redirects guests to the login page', function (string $url) {
    $this->get($url)->assertRedirect('/login');
})->with($adminPages);

it('forbids signed-in users who are not administrators', function (string $url) {
    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
})->with($adminPages);

it('lets administrators open every admin page', function (string $url) {
    $this->actingAs(admin())->get($url)->assertOk();
})->with($adminPages);

it('blocks non-admins from writing content', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/admin/technologies', ['name' => 'Hack'])->assertForbidden();
    $this->actingAs($user)->delete('/admin/projects/1')->assertForbidden();
});

it('does not allow public registration', function () {
    $this->get('/register')->assertNotFound();
});

it('does not allow mass assignment of the admin flag', function () {
    $user = new User(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secret', 'is_admin' => true]);

    expect($user->is_admin)->toBeNull();
});

it('throttles repeated failed logins', function () {
    $admin = admin();

    foreach (range(1, 5) as $i) {
        $this->post('/login', ['email' => $admin->email, 'password' => 'wrong']);
    }

    $this->post('/login', ['email' => $admin->email, 'password' => 'wrong'])->assertTooManyRequests();
});

it('sends administrators to the dashboard after login', function () {
    $admin = admin();

    $this->post('/login', ['email' => $admin->email, 'password' => 'password'])->assertRedirect('/admin');
});
