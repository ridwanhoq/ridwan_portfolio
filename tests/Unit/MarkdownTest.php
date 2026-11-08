<?php

use App\Services\Markdown;

it('renders markdown to HTML', function () {
    expect(Markdown::toHtml("## Title\n\n- one\n- two"))->toContain('<h2>Title</h2>')->toContain('<li>one</li>');
});

it('strips raw HTML and unsafe links', function () {
    $html = Markdown::toHtml("<script>alert(1)</script>\n\n<img src=x onerror=alert(1)>\n\n[click](javascript:alert(1))");

    expect($html)->not->toContain('<script')->not->toContain('onerror')->not->toContain('javascript:');
});

it('returns null for empty input', function () {
    expect(Markdown::toHtml(null))->toBeNull()->and(Markdown::toHtml("  \n"))->toBeNull();
});

it('produces plain text excerpts', function () {
    expect(Markdown::toText('**Bold** and [a link](https://example.com)', 100))->toBe('Bold and a link');
});
