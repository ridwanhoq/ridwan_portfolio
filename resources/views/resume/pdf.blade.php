<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $profile['full_name'] }} — Résumé</title>
    <style>
        @page { margin: 16mm 16mm 14mm; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.6pt; line-height: 1.45; color: #111827; }
        h1 { font-size: 20pt; margin: 0 0 2pt; letter-spacing: -0.2pt; }
        h2 { font-size: 10.5pt; text-transform: uppercase; letter-spacing: 1pt; color: #1d4ed8; border-bottom: 0.8pt solid #cbd5e1; padding-bottom: 3pt; margin: 14pt 0 6pt; }
        h3 { font-size: 10pt; margin: 0; }
        p { margin: 0 0 5pt; }
        ul { margin: 3pt 0 0; padding-left: 12pt; }
        li { margin-bottom: 2pt; }
        a { color: #1d4ed8; text-decoration: none; }
        .designation { font-size: 11pt; color: #334155; margin-bottom: 4pt; }
        .contact { color: #475569; font-size: 8.8pt; }
        /* Each section heading sits inside its first entry so it never ends up alone at a page bottom. */
        .entry { margin-bottom: 9pt; page-break-inside: avoid; }
        .meta { color: #475569; font-size: 8.8pt; }
        .row { width: 100%; }
        .row td { vertical-align: top; padding: 0; }
        .right { text-align: right; white-space: nowrap; }
        .skills td { padding: 1.5pt 0; vertical-align: top; }
        .skills td.label { width: 28%; font-weight: bold; }
        .stack { color: #475569; font-size: 8.6pt; margin-top: 2pt; }
    </style>
</head>
<body>
    <h1>{{ $profile['full_name'] }}</h1>
    <div class="designation">{{ $profile['designation'] }}</div>
    <div class="contact">
        {{ collect([
            $profile['location'],
            $profile['work_preference'],
            $profile['email'],
            $profile['phone'],
        ])->filter()->implode('  |  ') }}
    </div>
    @php($links = collect($site['socials'])->where('platform', '!=', 'email'))
    @if ($links->isNotEmpty())
        <div class="contact">
            {{ $links->map(fn ($l) => $l['label'].': '.preg_replace('#^https?://(www\.)?#', '', $l['url']))->implode('  |  ') }}
        </div>
    @endif

    @if ($profile['summary'])
        <h2>Professional Summary</h2>
        <p>{{ $profile['summary'] }}</p>
    @endif

    @if (count($skills))
        <h2>Core Skills</h2>
        <table class="skills" cellspacing="0">
            @foreach ($skills as $category)
                <tr>
                    <td class="label">{{ $category['name'] }}</td>
                    <td>{{ implode(', ', $category['skills']) }}</td>
                </tr>
            @endforeach
        </table>
    @endif

    @if (count($experiences))
        @foreach ($experiences as $job)
            <div class="entry">
                @if ($loop->first)<h2>Professional Experience</h2>@endif
                <table class="row" cellspacing="0">
                    <tr>
                        <td><h3>{{ $job['title'] }} | {{ $job['company'] }}</h3></td>
                        <td class="right meta">{{ $job['period'] }}</td>
                    </tr>
                </table>
                <div class="meta">{{ collect([$job['focus'], $job['location'], $job['employment_type']])->filter()->implode(' | ') }}</div>
                @php($points = array_merge($job['responsibilities'], $job['contributions']))
                @if (count($points))
                    <ul>
                        @foreach ($points as $point)
                            <li>{{ $point }}</li>
                        @endforeach
                    </ul>
                @endif
                @if (count($job['technologies']))
                    <div class="stack">Technologies: {{ implode(', ', $job['technologies']) }}</div>
                @endif
            </div>
        @endforeach
    @endif

    @if (count($projects))
        @foreach ($projects as $project)
            <div class="entry">
                @if ($loop->first)<h2>Selected Projects</h2>@endif
                <h3>{{ $project['title'] }}@if ($project['client']) | {{ $project['client'] }}@endif</h3>
                <p>{{ $project['summary'] }}</p>
                @if (count($project['technologies']))
                    <div class="stack">Technologies: {{ implode(', ', $project['technologies']) }}</div>
                @endif
            </div>
        @endforeach
    @endif

    @if (count($education))
        @foreach ($education as $item)
            <div class="entry">
                @if ($loop->first)<h2>Education</h2>@endif
                <table class="row" cellspacing="0">
                    <tr>
                        <td><h3>{{ $item['degree'] }}@if ($item['subject']) in {{ $item['subject'] }}@endif</h3></td>
                        <td class="right meta">{{ $item['graduation_year'] }}</td>
                    </tr>
                </table>
                <div class="meta">{{ collect([$item['institution'], $item['grade']])->filter()->implode(' | ') }}</div>
            </div>
        @endforeach
    @endif

    @if (count($certifications))
        @foreach ($certifications as $cert)
            <div class="entry">
                @if ($loop->first)<h2>Certifications &amp; Training</h2>@endif
                <h3>{{ $cert['name'] }}</h3>
                <div class="meta">{{ collect([$cert['issuer'], $cert['issued'], $cert['description']])->filter()->implode(' | ') }}</div>
            </div>
        @endforeach
    @endif

    @if (count($profile['languages']))
        <h2>Languages</h2>
        <p>{{ implode(', ', $profile['languages']) }}</p>
    @endif
</body>
</html>
