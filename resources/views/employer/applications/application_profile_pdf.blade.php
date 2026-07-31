<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicant Profile - {{ $application->first_name }} {{ $application->last_name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1f2937; line-height: 1.4; }
        h1 { font-size: 18px; margin: 0 0 4px 0; color: #111827; }
        h2 { font-size: 13px; margin: 14px 0 6px 0; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; }
        .subtitle { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
        .section { margin-bottom: 14px; }
        .row { margin-bottom: 6px; }
        .label { color: #6b7280; }
        table.section-table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.section-table th, table.section-table td { border: 1px solid #e5e7eb; padding: 8px 10px; text-align: left; }
        table.section-table th { background: #f3f4f6; font-weight: bold; }
        .block { margin-bottom: 10px; padding: 10px; background: #f9fafb; border-left: 3px solid #3b82f6; }
        .skill-tag { display: inline-block; background: #d1fae5; color: #065f46; padding: 3px 8px; margin: 2px 4px 2px 0; border-radius: 4px; font-size: 10px; }
        .footer { margin-top: 24px; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>{{ $application->first_name }} {{ $application->last_name }}</h1>
    <p class="subtitle">
        {{ $application->jobAdvertisement->title ?? 'Applicant' }}
        @if($application->jobAdvertisement && $application->jobAdvertisement->company)
            | {{ $application->jobAdvertisement->company->name }}
        @endif
    </p>

    <h2>Contact &amp; Personal</h2>
    <div class="section">
        <div class="row"><span class="label">Email:</span> {{ $application->email }}</div>
        <div class="row"><span class="label">Phone:</span> {{ $application->phone ?? '—' }}</div>
        @if($jobSeeker = $application->jobSeeker)
            <div class="row"><span class="label">Location:</span> {{ $jobSeeker->location ?? '—' }}</div>
            <div class="row"><span class="label">Gender:</span> {{ $jobSeeker->gender ? ucfirst($jobSeeker->gender) : '—' }}</div>
            <div class="row"><span class="label">Date of birth:</span> {{ $jobSeeker->date_of_birth ? \Carbon\Carbon::parse($jobSeeker->date_of_birth)->format('M j, Y') : '—' }}</div>
            <div class="row"><span class="label">Nationality:</span> {{ $jobSeeker->nationality ?? '—' }}</div>
            <div class="row"><span class="label">Highest education:</span> {{ $jobSeeker->highest_education ?? '—' }}</div>
            <div class="row"><span class="label">Driving license:</span> {{ $jobSeeker->driving_license ? 'Yes' : 'No' }}</div>
        @endif
    </div>

    @if($application->cover_letter)
    <h2>Cover Letter</h2>
    <div class="section" style="white-space: pre-wrap;">{{ $application->cover_letter }}</div>
    @endif

    @if($jobSeeker && $jobSeeker->bio)
    <h2>About</h2>
    <div class="section" style="white-space: pre-wrap;">{{ $jobSeeker->bio }}</div>
    @endif

    @if($jobSeeker && $jobSeeker->experiences && $jobSeeker->experiences->count() > 0)
    <h2>Work Experience</h2>
    <div class="section">
        @foreach($jobSeeker->experiences as $exp)
        <div class="block">
            <strong>{{ $exp->job_title }}</strong> @ {{ $exp->company_name }}<br>
            <span style="font-size: 10px; color: #6b7280;">
                {{ $exp->start_date ? \Carbon\Carbon::parse($exp->start_date)->format('M Y') : '' }}
                —
                {{ $exp->is_current ? 'Present' : ($exp->end_date ? \Carbon\Carbon::parse($exp->end_date)->format('M Y') : '') }}
                @if($exp->location) | {{ $exp->location }} @endif
            </span>
            @if($exp->description)<br><span style="font-size: 10px;">{{ $exp->description }}</span>@endif
        </div>
        @endforeach
    </div>
    @endif

    @if($jobSeeker && $jobSeeker->educations && $jobSeeker->educations->count() > 0)
    <h2>Education</h2>
    <div class="section">
        @foreach($jobSeeker->educations as $edu)
        <div class="block" style="border-left-color: #10b981;">
            <strong>{{ $edu->degree ?? 'Degree' }}</strong><br>
            {{ $edu->institution ?? $edu->institution_name ?? '' }}<br>
            <span style="font-size: 10px; color: #6b7280;">
                {{ $edu->start_date ? \Carbon\Carbon::parse($edu->start_date)->format('M Y') : '' }}
                —
                {{ $edu->end_date ? \Carbon\Carbon::parse($edu->end_date)->format('M Y') : ($edu->is_current ?? false ? 'Present' : '') }}
            </span>
            @if($edu->field_of_study ?? $edu->description)<br><span style="font-size: 10px;">{{ $edu->field_of_study ?? $edu->description }}</span>@endif
        </div>
        @endforeach
    </div>
    @endif

    @if($jobSeeker && $jobSeeker->skills && $jobSeeker->skills->count() > 0)
    <h2>Skills</h2>
    <div class="section">
        @foreach($jobSeeker->skills as $skill)
        <span class="skill-tag">{{ $skill->skill_name ?? $skill->name ?? 'Skill' }}@if($skill->proficiency_level ?? $skill->years_experience) ({{ trim(($skill->proficiency_level ?? '') . ($skill->years_experience ? ' ' . $skill->years_experience . 'y' : '')) }})@endif</span>
        @endforeach
    </div>
    @endif

    @if($jobSeeker && $jobSeeker->languages && $jobSeeker->languages->count() > 0)
    <h2>Languages</h2>
    <div class="section">
        @foreach($jobSeeker->languages as $lang)
        <div class="row">{{ $lang->language ?? $lang->language_name ?? '' }} — {{ $lang->proficiency_level ?? '' }}</div>
        @endforeach
    </div>
    @endif

    @if($jobSeeker && $jobSeeker->certifications && $jobSeeker->certifications->count() > 0)
    <h2>Certifications</h2>
    <div class="section">
        @foreach($jobSeeker->certifications as $cert)
        <div class="row"><strong>{{ $cert->certification_name }}</strong> — {{ $cert->issuing_organization ?? '' }} ({{ $cert->issue_date ? \Carbon\Carbon::parse($cert->issue_date)->format('M Y') : '' }})</div>
        @endforeach
    </div>
    @endif

    @if($jobSeeker && $jobSeeker->references && $jobSeeker->references->count() > 0)
    <h2>References</h2>
    <div class="section">
        @foreach($jobSeeker->references as $ref)
        <div class="block">
            <strong>{{ $ref->reference_name }}</strong> — {{ $ref->title }} @ {{ $ref->company }}<br>
            <span style="font-size: 10px;">{{ $ref->email }} @if($ref->phone) | {{ $ref->phone }} @endif</span>
        </div>
        @endforeach
    </div>
    @endif

    <div class="footer">
        Applicant profile generated on {{ now()->format('F j, Y \a\t g:i A') }}. Application ID: {{ $application->id }}.
    </div>
</body>
</html>
