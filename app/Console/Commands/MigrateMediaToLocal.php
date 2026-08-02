<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

class MigrateMediaToLocal extends Command
{
    protected $signature = 'media:migrate-local
                            {--dry-run : Show what would change without writing}
                            {--skip-download : Do not attempt to fetch from the old host}
                            {--seed-demos : Create demo media files for empty profiles}';

    protected $description = 'Rewrite media URLs to the local media server and ensure files exist on disk';

    /** @var list<array{0:string,1:string,2?:bool}> */
    private array $stringColumns = [
        ['companies', 'logo'],
        ['companies', 'cover_image'],
        ['employers', 'company_logo'],
        ['employers', 'business_certificate_path'],
        ['job_seekers', 'profile_photo'],
        ['job_seekers', 'cv_file_path'],
        ['job_seeker_documents', 'file_path'],
        ['job_seeker_certifications', 'certificate_file_path'],
        ['job_applications', 'resume_path'],
    ];

    /** @var list<array{0:string,1:string}> */
    private array $jsonColumns = [
        ['companies', 'gallery_images'],
        ['tender_ads', 'attachments'],
    ];

    private string $oldHost = '31.220.82.129';

    private string $newHost;

    private string $mediaBase;

    private string $uploadRoot;

    private bool $dryRun = false;

    private bool $skipDownload = false;

    private int $rewritten = 0;

    private int $filesCreated = 0;

    private int $filesDownloaded = 0;

    private int $filesKept = 0;

    public function handle(): int
    {
        $this->dryRun = (bool) $this->option('dry-run');
        $this->skipDownload = (bool) $this->option('skip-download');
        $this->mediaBase = rtrim((string) env('MEDIA_BASE_URL', 'http://127.0.0.1/uploads'), '/');
        $this->newHost = parse_url($this->mediaBase, PHP_URL_HOST) ?: '127.0.0.1';
        $this->uploadRoot = (string) env('MEDIA_UPLOAD_DIR', base_path('uploads'));

        if (! is_dir($this->uploadRoot) && ! $this->dryRun) {
            mkdir($this->uploadRoot, 0755, true);
        }

        $this->info("Media base: {$this->mediaBase}");
        $this->info("Upload root: {$this->uploadRoot}");
        if ($this->dryRun) {
            $this->warn('Dry run — no DB/disk writes.');
        }

        foreach ($this->stringColumns as [$table, $column]) {
            $this->migrateStringColumn($table, $column);
        }

        foreach ($this->jsonColumns as [$table, $column]) {
            $this->migrateJsonColumn($table, $column);
        }

        if ($this->option('seed-demos')) {
            $this->seedDemoMedia();
        }

        $this->newLine();
        $this->info("Rewritten values: {$this->rewritten}");
        $this->info("Files downloaded: {$this->filesDownloaded}");
        $this->info("Placeholder files created: {$this->filesCreated}");
        $this->info("Existing files kept: {$this->filesKept}");

        return self::SUCCESS;
    }

    private function primaryKeyFor(string $table): string
    {
        return match ($table) {
            'job_seekers' => 'seeker_id',
            'employers' => 'employer_id',
            default => 'id',
        };
    }

    private function migrateStringColumn(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $pk = $this->primaryKeyFor($table);
        if (! Schema::hasColumn($table, $pk)) {
            $this->warn("Skip {$table}.{$column}: missing PK {$pk}");
            return;
        }

        $rows = DB::table($table)
            ->whereNotNull($column)
            ->where($column, '!=', '')
            ->select([$pk, $column])
            ->get();

        foreach ($rows as $row) {
            $id = $row->{$pk};
            $original = (string) $row->{$column};
            $updated = $this->rewriteValue($original);
            if ($updated !== $original) {
                $this->line("{$table}.{$column}#{$id}: rewrite host/path");
                $this->rewritten++;
                if (! $this->dryRun) {
                    DB::table($table)->where($pk, $id)->update([$column => $updated]);
                }
            }
            $this->ensureFileForValue($updated !== $original ? $updated : $original);
        }
    }

    private function migrateJsonColumn(string $table, string $column): void
    {
        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
            return;
        }

        $pk = Schema::hasColumn($table, 'id') ? 'id' : null;
        if (! $pk) {
            return;
        }

        $rows = DB::table($table)
            ->whereNotNull($column)
            ->select([$pk, $column])
            ->get();

        foreach ($rows as $row) {
            $raw = $row->{$column};
            $decoded = is_string($raw) ? json_decode($raw, true) : $raw;
            if (! is_array($decoded)) {
                continue;
            }

            $changed = false;
            $walked = $this->walkJson($decoded, $changed);
            if ($changed) {
                $this->line("{$table}.{$column}#{$row->{$pk}}: rewrite json media refs");
                $this->rewritten++;
                if (! $this->dryRun) {
                    DB::table($table)->where($pk, $row->{$pk})->update([
                        $column => json_encode($walked),
                    ]);
                }
            }
        }
    }

    private function walkJson(mixed $node, bool &$changed): mixed
    {
        if (is_string($node)) {
            $updated = $this->rewriteValue($node);
            if ($updated !== $node) {
                $changed = true;
            }
            $this->ensureFileForValue($updated);
            return $updated;
        }

        if (! is_array($node)) {
            return $node;
        }

        $out = [];
        foreach ($node as $key => $value) {
            $out[$key] = $this->walkJson($value, $changed);
        }

        return $out;
    }

    private function rewriteValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return $value;
        }

        // Absolute old-host URLs -> new media base.
        if (str_contains($value, $this->oldHost)) {
            $path = $value;
            if (preg_match('#https?://[^/]+(/uploads/.+)$#i', $value, $m)) {
                return $this->mediaBase . substr($m[1], strlen('/uploads'));
            }
            return str_replace(
                ["http://{$this->oldHost}", "https://{$this->oldHost}"],
                ["http://{$this->newHost}", "https://{$this->newHost}"],
                $value
            );
        }

        // Relative /uploads/... -> full media URL.
        if (str_starts_with($value, '/uploads/')) {
            return $this->mediaBase . substr($value, strlen('/uploads'));
        }

        // Relative uploads/... without leading slash.
        if (str_starts_with($value, 'uploads/')) {
            return $this->mediaBase . '/' . substr($value, strlen('uploads/'));
        }

        // Already a path under a known upload type (cv/foo.pdf).
        if (preg_match('#^(cv|resumes|documents|profile-photos|company-logos|company-gallery|company-covers|certifications|tender-documents|application-documents|job-documents|temp)/#', $value)) {
            if (! str_starts_with($value, 'http')) {
                return $this->mediaBase . '/' . ltrim($value, '/');
            }
        }

        return $value;
    }

    private function ensureFileForValue(string $value): void
    {
        $relative = $this->extractRelativePath($value);
        if ($relative === null) {
            // Seeded tender-style /documents/... paths under public/
            if (str_starts_with($value, '/documents/') || str_starts_with($value, 'documents/')) {
                $this->ensurePublicDocument($value);
            }
            return;
        }

        $absolute = $this->uploadRoot . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
        $dir = dirname($absolute);
        if (! is_dir($dir) && ! $this->dryRun) {
            mkdir($dir, 0755, true);
        }

        if (is_file($absolute) && filesize($absolute) > 0) {
            $this->filesKept++;
            return;
        }

        if (! $this->skipDownload) {
            $candidates = [
                "http://{$this->oldHost}/uploads/{$relative}",
                "https://{$this->oldHost}/uploads/{$relative}",
            ];
            if (str_starts_with($value, 'http')) {
                array_unshift($candidates, $value);
            }
            foreach ($candidates as $url) {
                if ($this->tryDownload($url, $absolute)) {
                    $this->filesDownloaded++;
                    $this->line("  downloaded {$relative}");
                    return;
                }
            }
        }

        if ($this->dryRun) {
            $this->line("  would create placeholder {$relative}");
            $this->filesCreated++;
            return;
        }

        $this->writePlaceholder($absolute, $relative);
        $this->filesCreated++;
        $this->line("  placeholder {$relative}");
    }

    private function extractRelativePath(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('#/uploads/(.+)$#i', $value, $m)) {
            return ltrim($m[1], '/');
        }

        if (preg_match('#^(cv|resumes|documents|profile-photos|company-logos|company-gallery|company-covers|certifications|tender-documents|application-documents|job-documents|temp)/.+#', $value)) {
            return ltrim($value, '/');
        }

        return null;
    }

    private function tryDownload(string $url, string $absolute): bool
    {
        try {
            $response = Http::timeout(8)->withOptions(['allow_redirects' => true])->get($url);
            if (! $response->successful()) {
                return false;
            }
            $body = $response->body();
            if ($body === '' || strlen($body) < 8) {
                return false;
            }
            if ($this->dryRun) {
                return true;
            }
            $dir = dirname($absolute);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            file_put_contents($absolute, $body);

            return is_file($absolute) && filesize($absolute) > 0;
        } catch (\Throwable) {
            return false;
        }
    }

    private function writePlaceholder(string $absolute, string $relative): void
    {
        $ext = strtolower(pathinfo($absolute, PATHINFO_EXTENSION));
        $dir = dirname($absolute);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
            $this->writePlaceholderImage($absolute, $ext, $relative);
            return;
        }

        if ($ext === 'pdf' || $ext === '') {
            file_put_contents($absolute, $this->minimalPdf("JobHub media placeholder\n{$relative}"));
            return;
        }

        if (in_array($ext, ['doc', 'docx', 'txt'], true)) {
            file_put_contents(
                $absolute,
                "JobHub restored media placeholder for {$relative}\nOriginal file was unavailable from the previous media host.\n"
            );
            return;
        }

        file_put_contents($absolute, "placeholder:{$relative}\n");
    }

    private function writePlaceholderImage(string $absolute, string $ext, string $label): void
    {
        if (! function_exists('imagecreatetruecolor')) {
            // Tiny valid JPEG bytes fallback.
            $jpeg = base64_decode('/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxISEhUSEhIVFhUVFRUVFRUVFRUWFxUXFhUYHSggGBolGxUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OGxAQGy0lHyUtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAAEAAQMBIgACEQEDEQH/xAAbAAACAwEBAQAAAAAAAAAAAAADBAECBQYAB//EABQBAQAAAAAAAAAAAAAAAAAAAAD/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIQAxAAAAGhQ//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAQUCf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQMBAT8Bf//EABQRAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQIBAT8Bf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEABj8Cf//EABQQAQAAAAAAAAAAAAAAAAAAAAD/2gAIAQEAAT8hf//Z');
            file_put_contents($absolute, $jpeg ?: "IMG:{$label}");
            return;
        }

        $w = 640;
        $h = 400;
        $img = imagecreatetruecolor($w, $h);
        $bg = imagecolorallocate($img, 15, 76, 129);
        $fg = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w, $h, $bg);
        $text = 'JobHub';
        imagestring($img, 5, 20, 20, $text, $fg);
        imagestring($img, 3, 20, 50, substr($label, 0, 70), $fg);

        $targetExt = $ext === 'jpg' ? 'jpeg' : $ext;
        if ($targetExt === 'png') {
            imagepng($img, $absolute);
        } elseif ($targetExt === 'gif') {
            imagegif($img, $absolute);
        } elseif ($targetExt === 'webp' && function_exists('imagewebp')) {
            imagewebp($img, $absolute);
        } else {
            imagejpeg($img, $absolute, 85);
        }
        imagedestroy($img);
    }

    private function minimalPdf(string $text): string
    {
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $stream = "BT /F1 12 Tf 50 750 Td ({$escaped}) Tj ET";
        $len = strlen($stream);

        return "%PDF-1.4\n"
            . "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n"
            . "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n"
            . "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources<< /Font<< /F1 5 0 R >> >> >>endobj\n"
            . "4 0 obj<< /Length {$len} >>stream\n{$stream}\nendstream endobj\n"
            . "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n"
            . "xref\n0 6\n0000000000 65535 f \n"
            . "trailer<< /Size 6 /Root 1 0 R >>\nstartxref\n0\n%%EOF\n";
    }

    private function ensurePublicDocument(string $value): void
    {
        $rel = ltrim($value, '/');
        $absolute = public_path($rel);
        if (is_file($absolute) && filesize($absolute) > 0) {
            $this->filesKept++;
            return;
        }
        if ($this->dryRun) {
            $this->line("  would create public {$rel}");
            $this->filesCreated++;
            return;
        }
        $dir = dirname($absolute);
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $this->writePlaceholder($absolute, $rel);
        $this->filesCreated++;
        $this->line("  public placeholder {$rel}");
    }

    private function seedDemoMedia(): void
    {
        $this->info('Seeding demo media for empty profiles...');

        if (Schema::hasTable('job_seekers') && Schema::hasColumn('job_seekers', 'profile_photo')) {
            $seekers = DB::table('job_seekers')
                ->where(function ($q) {
                    $q->whereNull('profile_photo')->orWhere('profile_photo', '');
                })
                ->limit(25)
                ->get(['seeker_id']);

            foreach ($seekers as $seeker) {
                $rel = 'profile-photos/demo-seeker-' . $seeker->seeker_id . '.jpg';
                $url = $this->mediaBase . '/' . $rel;
                $this->ensureFileForValue($url);
                if (! $this->dryRun) {
                    DB::table('job_seekers')->where('seeker_id', $seeker->seeker_id)->update(['profile_photo' => $url]);
                }
                $this->rewritten++;
            }
        }

        if (Schema::hasTable('companies') && Schema::hasColumn('companies', 'logo')) {
            $companies = DB::table('companies')
                ->where(function ($q) {
                    $q->whereNull('logo')->orWhere('logo', '');
                })
                ->limit(25)
                ->get(['id', 'name']);

            foreach ($companies as $company) {
                $rel = 'company-logos/demo-company-' . $company->id . '.jpg';
                $url = $this->mediaBase . '/' . $rel;
                $this->ensureFileForValue($url);
                if (! $this->dryRun) {
                    DB::table('companies')->where('id', $company->id)->update(['logo' => $url]);
                }
                $this->rewritten++;
            }
        }
    }
}
