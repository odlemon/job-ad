<?php

namespace App\Http\Controllers\JobSeeker;

use App\Http\Controllers\Controller;
use App\Models\CareerToolDocument;
use App\Models\Course;
use App\Models\JobAdvertisement;
use App\Models\JobSeeker;
use App\Models\JobSeekerDocument;
use App\Services\JobSeeker\JobSeekerService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CareerToolsController extends Controller
{
    public function __construct(
        private JobSeekerService $jobSeekerService
    ) {
    }

    public function index(Request $request): View
    {
        return view('job-seeker.career-tools', [
            'initialTool' => $request->get('tool', ''),
        ]);
    }

    public function bootstrap(): JsonResponse
    {
        $seeker = $this->seeker();
        $profile = $this->profilePayload($seeker);
        $documents = $this->documentsPayload($seeker);
        $courses = collect();
        try {
            $query = Course::query();
            if (\Illuminate\Support\Facades\Schema::hasColumn('courses', 'is_active')) {
                $query->where('is_active', true);
            }
            $courses = $query->latest()->limit(12)->get()->map(function ($c) {
                return [
                    'id' => $c->id,
                    'title' => $c->title ?? 'Course',
                    'description' => $c->description ?? $c->summary ?? '',
                    'provider' => $c->provider ?? $c->instructor ?? 'JobHub Learning',
                    'duration' => $c->duration ?? null,
                    'level' => $c->level ?? null,
                    'url' => $c->url ?? $c->link ?? null,
                ];
            })->values();
        } catch (\Throwable) {
            $courses = collect();
        }

        return response()->json([
            'profile' => $profile,
            'documents' => $documents,
            'courses' => $courses,
            'roles' => array_keys($this->salaryBands()),
            'assessment_topics' => [
                ['id' => 'javascript', 'label' => 'JavaScript Fundamentals'],
                ['id' => 'php', 'label' => 'PHP & Laravel Basics'],
                ['id' => 'soft-skills', 'label' => 'Workplace Soft Skills'],
            ],
        ]);
    }

    public function generateResume(Request $request): JsonResponse
    {
        $seeker = $this->seeker();
        $validated = $request->validate([
            'template' => 'nullable|in:modern,classic,compact',
            'headline' => 'nullable|string|max:120',
            'summary' => 'nullable|string|max:2000',
            'save' => 'nullable|boolean',
        ]);

        $template = $validated['template'] ?? 'modern';
        $profile = $this->profilePayload($seeker);
        $headline = $validated['headline'] ?: ($profile['headline'] ?: 'Professional');
        $summary = $validated['summary'] ?: ($profile['bio'] ?: 'Motivated professional seeking opportunities to grow and contribute.');

        $html = $this->renderResumeHtml($profile, $template, $headline, $summary);
        $name = 'Resume - '.$profile['full_name'].' ('.ucfirst($template).').pdf';

        $doc = null;
        if ($request->boolean('save', true)) {
            $doc = $this->storeDocument($seeker, 'resume', $name, $html, [
                'template' => $template,
                'headline' => $headline,
            ]);
        }

        return response()->json([
            'html' => $html,
            'name' => $name,
            'document' => $doc,
        ]);
    }

    public function generateCoverLetter(Request $request): JsonResponse
    {
        $seeker = $this->seeker();
        $validated = $request->validate([
            'job_title' => 'required|string|max:160',
            'company' => 'required|string|max:160',
            'tone' => 'nullable|in:professional,enthusiastic,concise',
            'highlights' => 'nullable|string|max:1000',
            'save' => 'nullable|boolean',
        ]);

        $profile = $this->profilePayload($seeker);
        $tone = $validated['tone'] ?? 'professional';
        $body = $this->buildCoverLetterBody($profile, $validated['job_title'], $validated['company'], $tone, $validated['highlights'] ?? '');
        $html = $this->renderCoverLetterHtml($profile, $validated['job_title'], $validated['company'], $body);
        $name = 'Cover Letter - '.$validated['company'].'.pdf';

        $doc = null;
        if ($request->boolean('save', true)) {
            $doc = $this->storeDocument($seeker, 'cover_letter', $name, $html, [
                'job_title' => $validated['job_title'],
                'company' => $validated['company'],
                'tone' => $tone,
            ]);
        }

        return response()->json([
            'html' => $html,
            'body' => $body,
            'name' => $name,
            'document' => $doc,
        ]);
    }

    public function interviewPrep(Request $request): JsonResponse
    {
        $role = strtolower((string) $request->get('role', 'general'));
        $banks = $this->interviewBanks();
        $key = array_key_exists($role, $banks) ? $role : 'general';

        return response()->json([
            'role' => $key,
            'questions' => $banks[$key],
            'tips' => [
                'Use the STAR method (Situation, Task, Action, Result) for behavioral answers.',
                'Research the company and prepare 2–3 thoughtful questions to ask.',
                'Keep answers under 2 minutes unless asked for more detail.',
                'Practice out loud — confidence comes from repetition.',
            ],
        ]);
    }

    public function calculateSalary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'role' => 'required|string|max:80',
            'experience' => 'required|in:entry,mid,senior,lead',
            'location' => 'nullable|string|max:80',
            'education' => 'nullable|string|max:80',
        ]);

        $bands = $this->salaryBands();
        $roleKey = $validated['role'];
        if (! isset($bands[$roleKey])) {
            $roleKey = 'Software Developer';
        }

        $base = $bands[$roleKey];
        $mult = match ($validated['experience']) {
            'entry' => 0.75,
            'mid' => 1.0,
            'senior' => 1.35,
            'lead' => 1.6,
            default => 1.0,
        };

        $loc = strtolower((string) ($validated['location'] ?? ''));
        $locMult = 1.0;
        if (str_contains($loc, 'mahé') || str_contains($loc, 'mahe') || str_contains($loc, 'victoria')) {
            $locMult = 1.08;
        } elseif (str_contains($loc, 'praslin') || str_contains($loc, 'la digue')) {
            $locMult = 0.95;
        }

        $edu = strtolower((string) ($validated['education'] ?? ''));
        $eduBoost = 0;
        if (str_contains($edu, 'master') || str_contains($edu, 'mba')) {
            $eduBoost = 1200;
        } elseif (str_contains($edu, 'bachelor')) {
            $eduBoost = 600;
        }

        $low = (int) round(($base['low'] * $mult * $locMult) + $eduBoost);
        $mid = (int) round(($base['mid'] * $mult * $locMult) + $eduBoost);
        $high = (int) round(($base['high'] * $mult * $locMult) + $eduBoost);

        $seeker = $this->seeker();
        $expectedMin = (int) ($seeker->expected_salary_min ?? 0);
        $expectedMax = (int) ($seeker->expected_salary_max ?? 0);

        $marketJobs = JobAdvertisement::query()
            ->where('status', 'published')
            ->where(function ($q) use ($roleKey) {
                $q->where('title', 'like', '%'.explode(' ', $roleKey)[0].'%');
            })
            ->whereNotNull('salary_min')
            ->limit(20)
            ->get(['salary_min', 'salary_max', 'title']);

        $liveAvg = null;
        if ($marketJobs->isNotEmpty()) {
            $vals = $marketJobs->map(function ($j) {
                if ($j->salary_min && $j->salary_max) {
                    return ((float) $j->salary_min + (float) $j->salary_max) / 2;
                }

                return (float) ($j->salary_min ?: $j->salary_max);
            })->filter()->values();
            if ($vals->isNotEmpty()) {
                $liveAvg = (int) round($vals->avg());
            }
        }

        $position = 'aligned';
        if ($expectedMax > 0) {
            if ($expectedMax < $low) {
                $position = 'below_market';
            } elseif ($expectedMin > $high) {
                $position = 'above_market';
            }
        }

        return response()->json([
            'currency' => 'SCR',
            'role' => $roleKey,
            'experience' => $validated['experience'],
            'range' => [
                'low' => $low,
                'mid' => $mid,
                'high' => $high,
            ],
            'period' => 'per month',
            'live_market_average' => $liveAvg,
            'your_expectation' => [
                'min' => $expectedMin ?: null,
                'max' => $expectedMax ?: null,
            ],
            'position' => $position,
            'advice' => match ($position) {
                'below_market' => 'Your expected range is below the estimated market. Consider negotiating upward with evidence of your impact.',
                'above_market' => 'Your expectation sits above typical ranges. Strengthen with certifications, portfolio proof, or niche skills.',
                default => 'Your expectations look well aligned with the Seychelles market for this role.',
            },
        ]);
    }

    public function assessmentQuestions(string $topic): JsonResponse
    {
        $banks = $this->assessmentBanks();
        if (! isset($banks[$topic])) {
            return response()->json(['message' => 'Unknown assessment topic'], 404);
        }

        $questions = collect($banks[$topic])->map(function ($q) {
            return [
                'id' => $q['id'],
                'prompt' => $q['prompt'],
                'choices' => $q['choices'],
            ];
        })->values();

        return response()->json([
            'topic' => $topic,
            'title' => collect([
                'javascript' => 'JavaScript Fundamentals',
                'php' => 'PHP & Laravel Basics',
                'soft-skills' => 'Workplace Soft Skills',
            ])->get($topic, $topic),
            'questions' => $questions,
            'pass_score' => 70,
        ]);
    }

    public function submitAssessment(Request $request, string $topic): JsonResponse
    {
        $banks = $this->assessmentBanks();
        if (! isset($banks[$topic])) {
            return response()->json(['message' => 'Unknown assessment topic'], 404);
        }

        $validated = $request->validate([
            'answers' => 'required|array',
        ]);

        $correct = 0;
        $total = count($banks[$topic]);
        $review = [];

        foreach ($banks[$topic] as $q) {
            $given = $validated['answers'][$q['id']] ?? null;
            $ok = $given !== null && (int) $given === (int) $q['answer'];
            if ($ok) {
                $correct++;
            }
            $review[] = [
                'id' => $q['id'],
                'prompt' => $q['prompt'],
                'correct' => $ok,
                'correct_choice' => $q['choices'][$q['answer']],
                'your_choice' => isset($q['choices'][$given]) ? $q['choices'][$given] : null,
                'explain' => $q['explain'],
            ];
        }

        $score = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
        $passed = $score >= 70;
        $seeker = $this->seeker();
        $title = collect([
            'javascript' => 'JavaScript Fundamentals',
            'php' => 'PHP & Laravel Basics',
            'soft-skills' => 'Workplace Soft Skills',
        ])->get($topic, $topic);

        $doc = null;
        if ($passed) {
            $html = $this->renderCertificateHtml($seeker, $title, $score);
            $name = 'Certificate - '.$title.'.pdf';
            $doc = $this->storeDocument($seeker, 'assessment', $name, $html, [
                'topic' => $topic,
                'score' => $score,
                'passed' => true,
            ]);
        }

        return response()->json([
            'score' => $score,
            'correct' => $correct,
            'total' => $total,
            'passed' => $passed,
            'review' => $review,
            'document' => $doc,
            'message' => $passed
                ? 'Congratulations! You passed and earned a certificate.'
                : 'Keep practicing — review the explanations and try again.',
        ]);
    }

    public function careerPaths(): JsonResponse
    {
        $seeker = $this->seeker();
        $skills = $seeker->skills()->pluck('skill_name')->map(fn ($s) => strtolower((string) $s))->all();
        $paths = $this->pathCatalog();

        $ranked = collect($paths)->map(function ($path) use ($skills) {
            $overlap = collect($path['skills'])->filter(function ($s) use ($skills) {
                foreach ($skills as $have) {
                    if (str_contains($have, strtolower($s)) || str_contains(strtolower($s), $have)) {
                        return true;
                    }
                }

                return false;
            })->count();

            $match = (int) round(($overlap / max(1, count($path['skills']))) * 100);

            return array_merge($path, [
                'match_percent' => $match,
                'matched_skills' => $overlap,
            ]);
        })->sortByDesc('match_percent')->values();

        $top = $ranked->first();
        $relatedJobs = [];
        if ($top) {
            $relatedJobs = JobAdvertisement::query()
                ->with('company:id,name')
                ->where('status', 'published')
                ->where(function ($q) use ($top) {
                    foreach ($top['keywords'] as $kw) {
                        $q->orWhere('title', 'like', '%'.$kw.'%');
                    }
                })
                ->latest()
                ->limit(6)
                ->get()
                ->map(fn ($j) => [
                    'id' => $j->id,
                    'title' => $j->title,
                    'company' => $j->company->name ?? 'Company',
                    'location' => $j->location,
                    'url' => url('/jobs/'.$j->id),
                ])
                ->all();
        }

        return response()->json([
            'paths' => $ranked,
            'related_jobs' => $relatedJobs,
            'your_skills' => $seeker->skills()->pluck('skill_name')->values(),
        ]);
    }

    public function documents(): JsonResponse
    {
        return response()->json([
            'data' => $this->documentsPayload($this->seeker()),
        ]);
    }

    public function downloadDocument(int $id)
    {
        $seeker = $this->seeker();
        $doc = CareerToolDocument::where('seeker_id', $seeker->seeker_id)->where('id', $id)->firstOrFail();

        $pdf = Pdf::loadHTML($doc->content)->setPaper('a4');

        return $pdf->download($doc->name);
    }

    public function destroyDocument(int $id): JsonResponse
    {
        $seeker = $this->seeker();
        $doc = CareerToolDocument::where('seeker_id', $seeker->seeker_id)->where('id', $id)->first();
        if (! $doc) {
            return response()->json(['message' => 'Document not found'], 404);
        }
        $doc->delete();

        return response()->json(['message' => 'Deleted']);
    }

    private function seeker(): JobSeeker
    {
        $seeker = $this->jobSeekerService->getByUserId(Auth::id());
        abort_unless($seeker, 404, 'Job seeker profile not found');

        return $seeker;
    }

    private function profilePayload(JobSeeker $seeker): array
    {
        $seeker->loadMissing(['skills', 'experiences', 'educations', 'certifications', 'user']);

        $fullName = trim(($seeker->first_name ?? '').' '.($seeker->last_name ?? ''));
        if ($fullName === '') {
            $fullName = $seeker->user->name ?? 'Job Seeker';
        }

        $headline = $seeker->experiences->first()?->job_title
            ?? $seeker->experiences->first()?->title
            ?? $seeker->experiences->first()?->position
            ?? null;

        return [
            'full_name' => $fullName,
            'email' => $seeker->user->email ?? '',
            'phone' => $seeker->phone ?? '',
            'location' => $seeker->location ?? '',
            'bio' => $seeker->bio ?? '',
            'headline' => $headline,
            'linkedin' => $seeker->linkedin_url ?? '',
            'skills' => $seeker->skills->map(fn ($s) => [
                'name' => $s->skill_name,
                'level' => $s->proficiency_level,
            ])->values(),
            'experiences' => $seeker->experiences->map(fn ($e) => [
                'title' => $e->job_title ?? $e->title ?? $e->position ?? 'Role',
                'company' => $e->company_name ?? $e->company ?? '',
                'start' => optional($e->start_date)->format('M Y') ?? (string) ($e->start_date ?? ''),
                'end' => $e->is_current ? 'Present' : (optional($e->end_date)->format('M Y') ?? (string) ($e->end_date ?? '')),
                'description' => $e->description ?? '',
            ])->values(),
            'educations' => $seeker->educations->map(fn ($e) => [
                'school' => $e->institution ?? $e->school_name ?? '',
                'degree' => $e->degree ?? '',
                'field' => $e->field_of_study ?? $e->field ?? '',
                'year' => $e->graduation_year ?? optional($e->end_date)->format('Y'),
            ])->values(),
            'certifications' => $seeker->certifications->map(fn ($c) => [
                'name' => $c->name ?? $c->certification_name ?? '',
                'issuer' => $c->issuing_organization ?? $c->issuer ?? '',
            ])->values(),
            'expected_salary_min' => $seeker->expected_salary_min,
            'expected_salary_max' => $seeker->expected_salary_max,
        ];
    }

    private function documentsPayload(JobSeeker $seeker): array
    {
        $generated = CareerToolDocument::where('seeker_id', $seeker->seeker_id)
            ->latest()
            ->limit(20)
            ->get()
            ->map(fn ($d) => [
                'id' => $d->id,
                'source' => 'career_tools',
                'type' => $d->type,
                'name' => $d->name,
                'date' => optional($d->created_at)->format('M j, Y'),
                'size' => $this->formatBytes((int) $d->size_bytes),
                'download_url' => url('/job-seeker/career-tools/documents/'.$d->id.'/download'),
                'can_delete' => true,
            ]);

        $uploaded = collect();
        try {
            $uploaded = JobSeekerDocument::where('seeker_id', $seeker->seeker_id)
                ->latest()
                ->limit(20)
                ->get()
                ->map(function ($d) {
                    $path = $d->file_path ?? '';
                    $url = $path;
                    if ($path && ! str_starts_with($path, 'http')) {
                        $url = asset('storage/'.ltrim(str_replace('/storage/', '', $path), '/'));
                    }

                    return [
                        'id' => 'up-'.$d->id,
                        'source' => 'upload',
                        'type' => 'upload',
                        'name' => $d->name ?? 'Document',
                        'date' => optional($d->created_at)->format('M j, Y'),
                        'size' => '—',
                        'download_url' => $url,
                        'can_delete' => false,
                    ];
                });
        } catch (\Throwable) {
            $uploaded = collect();
        }

        if ($seeker->cv_file_path) {
            $cvUrl = $seeker->cv_file_path;
            if (! str_starts_with($cvUrl, 'http')) {
                $cvUrl = asset('storage/'.ltrim(str_replace('/storage/', '', $cvUrl), '/'));
            }
            $uploaded->prepend([
                'id' => 'cv-primary',
                'source' => 'upload',
                'type' => 'resume',
                'name' => 'Primary CV',
                'date' => optional($seeker->cv_uploaded_at)->format('M j, Y') ?: '—',
                'size' => '—',
                'download_url' => $cvUrl,
                'can_delete' => false,
            ]);
        }

        return $generated->concat($uploaded)->values()->all();
    }

    private function storeDocument(JobSeeker $seeker, string $type, string $name, string $html, array $meta = []): array
    {
        $doc = CareerToolDocument::create([
            'seeker_id' => $seeker->seeker_id,
            'type' => $type,
            'name' => $name,
            'content' => $html,
            'meta' => $meta,
            'size_bytes' => strlen($html),
        ]);

        return [
            'id' => $doc->id,
            'source' => 'career_tools',
            'type' => $doc->type,
            'name' => $doc->name,
            'date' => optional($doc->created_at)->format('M j, Y'),
            'size' => $this->formatBytes((int) $doc->size_bytes),
            'download_url' => url('/job-seeker/career-tools/documents/'.$doc->id.'/download'),
            'can_delete' => true,
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }
        if ($bytes < 1048576) {
            return round($bytes / 1024).' KB';
        }

        return round($bytes / 1048576, 1).' MB';
    }

    private function renderResumeHtml(array $p, string $template, string $headline, string $summary): string
    {
        $skills = collect($p['skills'])->pluck('name')->filter()->implode(', ');
        $expHtml = collect($p['experiences'])->map(function ($e) {
            return '<div style="margin-bottom:12px;"><strong>'.e($e['title']).'</strong> — '.e($e['company']).
                '<div style="color:#6b7280;font-size:12px;">'.e($e['start']).' – '.e($e['end']).'</div>'.
                '<div style="margin-top:4px;">'.nl2br(e($e['description'])).'</div></div>';
        })->implode('');
        $eduHtml = collect($p['educations'])->map(function ($e) {
            return '<div style="margin-bottom:8px;"><strong>'.e($e['degree']).'</strong> '.e($e['field']).
                '<div style="color:#6b7280;font-size:12px;">'.e($e['school']).($e['year'] ? ' · '.$e['year'] : '').'</div></div>';
        })->implode('');
        $certs = collect($p['certifications'])->map(fn ($c) => e($c['name']).($c['issuer'] ? ' ('.e($c['issuer']).')' : ''))->filter()->implode(', ');

        $accent = $template === 'classic' ? '#111827' : ($template === 'compact' ? '#0f766e' : '#2563eb');
        $nameSize = $template === 'compact' ? '22px' : '28px';

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            body{font-family:DejaVu Sans,sans-serif;color:#111827;font-size:13px;line-height:1.45;margin:32px;}
            h1{margin:0;font-size:'.$nameSize.';color:'.$accent.';}
            .sub{color:#4b5563;margin:4px 0 16px;}
            .meta{font-size:12px;color:#6b7280;margin-bottom:18px;}
            h2{font-size:14px;text-transform:uppercase;letter-spacing:.04em;border-bottom:2px solid '.$accent.';padding-bottom:4px;margin:18px 0 10px;color:'.$accent.';}
        </style></head><body>
            <h1>'.e($p['full_name']).'</h1>
            <div class="sub">'.e($headline).'</div>
            <div class="meta">'.e($p['email']).' · '.e($p['phone']).' · '.e($p['location']).
            ($p['linkedin'] ? ' · '.e($p['linkedin']) : '').'</div>
            <h2>Summary</h2><p>'.nl2br(e($summary)).'</p>
            '.($expHtml ? '<h2>Experience</h2>'.$expHtml : '').'
            '.($eduHtml ? '<h2>Education</h2>'.$eduHtml : '').'
            '.($skills ? '<h2>Skills</h2><p>'.e($skills).'</p>' : '').'
            '.($certs ? '<h2>Certifications</h2><p>'.$certs.'</p>' : '').'
        </body></html>';
    }

    private function buildCoverLetterBody(array $p, string $jobTitle, string $company, string $tone, string $highlights): string
    {
        $name = $p['full_name'];
        $skill = collect($p['skills'])->pluck('name')->take(3)->implode(', ');
        $exp = collect($p['experiences'])->first();
        $expLine = $exp
            ? 'In my role as '.$exp['title'].($exp['company'] ? ' at '.$exp['company'] : '').', I developed practical experience delivering results.'
            : 'I bring a strong work ethic and a commitment to continuous learning.';

        $open = match ($tone) {
            'enthusiastic' => "I'm excited to apply for the {$jobTitle} position at {$company}. The opportunity to contribute to your team energizes me!",
            'concise' => "I am applying for the {$jobTitle} role at {$company}.",
            default => "I am writing to express my interest in the {$jobTitle} position at {$company}.",
        };

        $mid = "{$expLine}";
        if ($skill) {
            $mid .= " My strengths include {$skill}.";
        }
        if (trim($highlights) !== '') {
            $mid .= ' '.trim($highlights);
        } elseif ($p['bio']) {
            $mid .= ' '.preg_replace('/\s+/', ' ', strip_tags($p['bio']));
        }

        $close = match ($tone) {
            'enthusiastic' => "I'd love the chance to discuss how I can help {$company} succeed. Thank you for your time!",
            'concise' => "Thank you for considering my application. I look forward to your response.",
            default => "Thank you for considering my application. I would welcome the opportunity to discuss how I can contribute to {$company}.",
        };

        return "Dear Hiring Manager,\n\n{$open}\n\n{$mid}\n\n{$close}\n\nSincerely,\n{$name}";
    }

    private function renderCoverLetterHtml(array $p, string $jobTitle, string $company, string $body): string
    {
        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            body{font-family:DejaVu Sans,sans-serif;color:#111827;font-size:13px;line-height:1.55;margin:40px;}
            .meta{color:#6b7280;font-size:12px;margin-bottom:24px;}
            h1{font-size:18px;margin:0 0 4px;color:#2563eb;}
        </style></head><body>
            <h1>'.e($p['full_name']).'</h1>
            <div class="meta">'.e($p['email']).' · '.e($p['phone']).' · '.e($p['location']).'</div>
            <div class="meta">Re: '.e($jobTitle).' — '.e($company).'</div>
            <div>'.nl2br(e($body)).'</div>
        </body></html>';
    }

    private function renderCertificateHtml(JobSeeker $seeker, string $title, int $score): string
    {
        $name = trim(($seeker->first_name ?? '').' '.($seeker->last_name ?? '')) ?: ($seeker->user->name ?? 'Candidate');
        $date = now()->format('F j, Y');

        return '<!DOCTYPE html><html><head><meta charset="utf-8"><style>
            body{font-family:DejaVu Sans,sans-serif;text-align:center;color:#111827;margin:48px;}
            .frame{border:6px solid #2563eb;padding:40px;border-radius:12px;}
            h1{color:#2563eb;margin:0 0 8px;font-size:28px;}
            h2{margin:24px 0 8px;font-size:22px;}
            .score{font-size:18px;color:#0f766e;margin-top:16px;}
        </style></head><body><div class="frame">
            <h1>Certificate of Achievement</h1>
            <p>This certifies that</p>
            <h2>'.e($name).'</h2>
            <p>successfully completed the assessment</p>
            <h2 style="color:#2563eb;">'.e($title).'</h2>
            <div class="score">Score: '.$score.'%</div>
            <p style="margin-top:28px;color:#6b7280;">Issued by JobHub Career Tools · '.e($date).'</p>
        </div></body></html>';
    }

    private function salaryBands(): array
    {
        return [
            'Software Developer' => ['low' => 12000, 'mid' => 16000, 'high' => 22000],
            'Frontend Developer' => ['low' => 11000, 'mid' => 15000, 'high' => 20000],
            'Full Stack Developer' => ['low' => 13000, 'mid' => 18000, 'high' => 24000],
            'Data Analyst' => ['low' => 10000, 'mid' => 14000, 'high' => 19000],
            'Marketing Specialist' => ['low' => 9000, 'mid' => 13000, 'high' => 17000],
            'Accountant' => ['low' => 10000, 'mid' => 14500, 'high' => 19000],
            'Hospitality Supervisor' => ['low' => 8500, 'mid' => 12000, 'high' => 16000],
            'Project Manager' => ['low' => 15000, 'mid' => 20000, 'high' => 28000],
            'Customer Support' => ['low' => 7000, 'mid' => 9500, 'high' => 13000],
            'HR Officer' => ['low' => 9000, 'mid' => 12500, 'high' => 16500],
        ];
    }

    private function interviewBanks(): array
    {
        return [
            'general' => [
                ['q' => 'Tell me about yourself.', 'tip' => 'Keep it to ~90 seconds: present role, key achievements, why this opportunity.'],
                ['q' => 'What are your greatest strengths?', 'tip' => 'Pick 2 strengths with a short example for each.'],
                ['q' => 'Describe a challenge you overcame at work.', 'tip' => 'Use STAR — focus on your actions and measurable results.'],
                ['q' => 'Why do you want this role?', 'tip' => 'Connect company mission + role requirements to your goals.'],
                ['q' => 'Where do you see yourself in 3 years?', 'tip' => 'Show ambition aligned with growth inside the company.'],
            ],
            'software' => [
                ['q' => 'Walk me through a project you are proud of.', 'tip' => 'Cover problem, architecture choices, trade-offs, and impact.'],
                ['q' => 'How do you debug a production issue?', 'tip' => 'Mention reproduction, logs/metrics, hypothesis, fix, and prevention.'],
                ['q' => 'Explain REST vs GraphQL.', 'tip' => 'Be concise; mention use-cases for each.'],
                ['q' => 'How do you ensure code quality?', 'tip' => 'Tests, reviews, typing/linting, CI, and clear PR descriptions.'],
                ['q' => 'Describe a time you disagreed with a technical decision.', 'tip' => 'Show collaboration and data-driven persuasion.'],
            ],
            'hospitality' => [
                ['q' => 'How do you handle an upset guest?', 'tip' => 'Listen, empathize, solve quickly, follow up.'],
                ['q' => 'Describe a busy service period you managed.', 'tip' => 'Prioritization, teamwork, and composure under pressure.'],
                ['q' => 'What does excellent service mean to you?', 'tip' => 'Anticipate needs and personalize the experience.'],
                ['q' => 'How do you train or support junior staff?', 'tip' => 'Clear standards, shadowing, and constructive feedback.'],
                ['q' => 'Tell us about a time you received critical feedback.', 'tip' => 'Show growth mindset and changed behavior.'],
            ],
        ];
    }

    private function assessmentBanks(): array
    {
        return [
            'javascript' => [
                ['id' => 'js1', 'prompt' => 'Which keyword declares a block-scoped variable?', 'choices' => ['var', 'let', 'function', 'static'], 'answer' => 1, 'explain' => '`let` and `const` are block-scoped; `var` is function-scoped.'],
                ['id' => 'js2', 'prompt' => 'What does `Array.prototype.map` return?', 'choices' => ['A mutated original array', 'A new array', 'A boolean', 'Undefined'], 'answer' => 1, 'explain' => '`map` returns a new array with transformed values.'],
                ['id' => 'js3', 'prompt' => 'Which is true about promises?', 'choices' => ['They always block the thread', 'They represent a future value', 'They replace HTML', 'They are only for CSS'], 'answer' => 1, 'explain' => 'A Promise represents an eventual completion (or failure) of an async operation.'],
                ['id' => 'js4', 'prompt' => 'What is `===` in JavaScript?', 'choices' => ['Assignment', 'Loose equality', 'Strict equality', 'Bitwise AND'], 'answer' => 2, 'explain' => '`===` compares value and type without coercion.'],
                ['id' => 'js5', 'prompt' => 'Which method adds an item to the end of an array?', 'choices' => ['shift', 'unshift', 'push', 'pop'], 'answer' => 2, 'explain' => '`push` appends; `pop` removes from the end.'],
            ],
            'php' => [
                ['id' => 'php1', 'prompt' => 'Which symbol starts a PHP variable?', 'choices' => ['#', '$', '@', '&'], 'answer' => 1, 'explain' => 'PHP variables start with `$`.'],
                ['id' => 'php2', 'prompt' => 'In Laravel, which file defines web routes by default?', 'choices' => ['app/Http/Kernel.php', 'routes/web.php', 'config/app.php', 'public/index.php'], 'answer' => 1, 'explain' => 'Web routes live in `routes/web.php`.'],
                ['id' => 'php3', 'prompt' => 'What does Eloquent provide?', 'choices' => ['CSS framework', 'ORM for database models', 'Queue worker only', 'Mail templates'], 'answer' => 1, 'explain' => 'Eloquent is Laravel’s ActiveRecord-style ORM.'],
                ['id' => 'php4', 'prompt' => 'Which HTTP method is typically used to update a resource (Laravel form spoofing)?', 'choices' => ['CONNECT', 'TRACE', 'PUT/PATCH', 'HEAD only'], 'answer' => 2, 'explain' => 'Updates commonly use PUT or PATCH.'],
                ['id' => 'php5', 'prompt' => 'What is `composer` used for?', 'choices' => ['Frontend bundling only', 'PHP dependency management', 'Database GUI', 'DNS configuration'], 'answer' => 1, 'explain' => 'Composer manages PHP packages and autoloading.'],
            ],
            'soft-skills' => [
                ['id' => 'ss1', 'prompt' => 'Best first step when a teammate misses a deadline?', 'choices' => ['Escalate publicly', 'Ask privately what blocked them', 'Ignore it', 'Reassign without talking'], 'answer' => 1, 'explain' => 'Start with curiosity and support, then problem-solve.'],
                ['id' => 'ss2', 'prompt' => 'Active listening includes:', 'choices' => ['Planning your reply while they talk', 'Interrupting to correct details', 'Paraphrasing to confirm understanding', 'Checking your phone'], 'answer' => 2, 'explain' => 'Reflecting back shows understanding and reduces miscommunication.'],
                ['id' => 'ss3', 'prompt' => 'Constructive feedback should be:', 'choices' => ['Personal and vague', 'Specific, actionable, respectful', 'Delayed indefinitely', 'Only written in anger'], 'answer' => 1, 'explain' => 'Good feedback is timely, specific, and focused on behavior/outcomes.'],
                ['id' => 'ss4', 'prompt' => 'When priorities conflict, you should:', 'choices' => ['Do everything at once', 'Clarify impact and negotiate timelines', 'Hide the conflict', 'Always choose the newest request'], 'answer' => 1, 'explain' => 'Transparent prioritization protects quality and trust.'],
                ['id' => 'ss5', 'prompt' => 'A growth mindset means:', 'choices' => ['Talent is fixed', 'Skills can improve with effort and feedback', 'Avoid hard tasks', 'Never ask for help'], 'answer' => 1, 'explain' => 'Growth mindset treats abilities as developable.'],
            ],
        ];
    }

    private function pathCatalog(): array
    {
        return [
            [
                'id' => 'fullstack',
                'title' => 'Full-Stack Web Developer',
                'summary' => 'Build end-to-end web products with modern frontend and Laravel/Node backends.',
                'skills' => ['JavaScript', 'React', 'PHP', 'Laravel', 'SQL'],
                'keywords' => ['Developer', 'Full Stack', 'Software', 'Engineer'],
                'next_steps' => ['Ship a portfolio CRUD app', 'Add auth + API tests', 'Contribute to an open-source issue'],
            ],
            [
                'id' => 'data',
                'title' => 'Data Analyst',
                'summary' => 'Turn business data into insights with SQL, spreadsheets, and visualization.',
                'skills' => ['SQL', 'Excel', 'Python', 'Statistics', 'Communication'],
                'keywords' => ['Data', 'Analyst', 'Business Intelligence'],
                'next_steps' => ['Build a dashboard from public data', 'Practice SQL window functions', 'Learn a BI tool (Metabase/Power BI)'],
            ],
            [
                'id' => 'hospitality',
                'title' => 'Hospitality & Guest Experience Lead',
                'summary' => 'Lead teams that deliver memorable guest journeys across Seychelles tourism.',
                'skills' => ['Customer Service', 'Leadership', 'Communication', 'Operations'],
                'keywords' => ['Hospitality', 'Hotel', 'Guest', 'Supervisor', 'Manager'],
                'next_steps' => ['Document a service recovery playbook', 'Shadow a front-office lead', 'Earn a hospitality service certificate'],
            ],
            [
                'id' => 'marketing',
                'title' => 'Digital Marketing Specialist',
                'summary' => 'Grow brands through content, campaigns, and measurable acquisition channels.',
                'skills' => ['Marketing', 'Content', 'SEO', 'Analytics', 'Social Media'],
                'keywords' => ['Marketing', 'Digital', 'Content', 'SEO'],
                'next_steps' => ['Run a small paid/organic experiment', 'Build a content calendar', 'Learn GA4 basics'],
            ],
            [
                'id' => 'pm',
                'title' => 'Project / Product Coordinator',
                'summary' => 'Coordinate delivery across stakeholders with clear plans and communication.',
                'skills' => ['Project Management', 'Communication', 'Leadership', 'Organization'],
                'keywords' => ['Project', 'Product', 'Coordinator', 'Manager'],
                'next_steps' => ['Lead a mini project with milestones', 'Learn Agile basics', 'Create a risk/RAID log template'],
            ],
        ];
    }
}
