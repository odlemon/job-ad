<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\TenderAd;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Public API for tenders (active tender ads only).
 */
class TenderController extends Controller
{
    /**
     * Web: render the tenders listing page with real data.
     */
    public function indexWeb(Request $request)
    {
        $tenders = TenderAd::with('category')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
            })
            ->orderByDesc('created_at')
            ->get();

        $sectors = $tenders->pluck('sector')->filter()->unique()->sort()->values();
        $types = $tenders->pluck('tender_type')->filter()->unique()->sort()->values();
        $locations = $tenders->pluck('location')->filter()->unique()->sort()->values();
        $entities = $tenders->pluck('entity_name')->filter()->unique()->sort()->values();
        $deadlines = $tenders->pluck('submission_deadline')->filter()->map(fn ($d) => $d->format('M d, Y'))->unique()->sort()->values();

        return view('tenders.index', [
            'tenders' => $tenders,
            'sectors' => $sectors,
            'types' => $types,
            'locations' => $locations,
            'entities' => $entities,
            'deadlines' => $deadlines,
        ]);
    }

    /**
     * Web: render a single tender detail page with real data.
     */
    public function showWeb(string $idOrSlug)
    {
        $tender = TenderAd::with('category')
            ->where('status', 'active')
            ->where(function ($q) use ($idOrSlug) {
                $q->where('id', (int) $idOrSlug)->orWhere('slug', $idOrSlug);
            })
            ->first();

        if (!$tender) {
            abort(404);
        }

        $tender->increment('views_count');

        return view('tenders.show', ['tender' => $tender]);
    }

    /**
     * List active tenders for the public tenders page.
     */
    public function index(Request $request): JsonResponse
    {
        $query = TenderAd::with('category')
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
            })
            ->orderByDesc('created_at');

        if ($request->filled('search') || $request->filled('q')) {
            $s = $request->get('search', $request->get('q'));
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', '%' . $s . '%')
                    ->orWhere('entity_name', 'like', '%' . $s . '%')
                    ->orWhere('reference_number', 'like', '%' . $s . '%')
                    ->orWhere('description', 'like', '%' . $s . '%');
            });
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->get('category_id'));
        }

        $perPage = max(1, min(50, (int) $request->get('per_page', 15)));
        $paginator = $query->paginate($perPage);

        $items = $paginator->getCollection()->map(function (TenderAd $t) {
            return $this->mapTenderListItem($t);
        });

        return response()->json([
            'data' => $items,
            'tenders' => $items, // backward compatible
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
            ],
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    /**
     * Show a single active tender by id or slug.
     */
    public function show(Request $request, string $idOrSlug): JsonResponse
    {
        $tender = TenderAd::with('category')
            ->where('status', 'active')
            ->where(function ($q) use ($idOrSlug) {
                $q->where('id', (int) $idOrSlug)->orWhere('slug', $idOrSlug);
            })
            ->first();

        if (!$tender) {
            return response()->json(['message' => 'Tender not found'], 404);
        }

        // Optionally increment views
        $tender->increment('views_count');

        $t = $tender;

        return response()->json([
            'data' => array_merge($this->mapTenderDetail($t), [
                'overview' => [
                    'description' => $t->description,
                    'summary' => $t->summary,
                    'scope_of_work' => $t->scope_of_work,
                    'requirements' => $t->requirements ?? [],
                ],
                'tender_information' => [
                    'budget_range' => $this->budgetLabel($t),
                    'budget_min' => $t->budget_min ? (float) $t->budget_min : null,
                    'budget_max' => $t->budget_max ? (float) $t->budget_max : null,
                    'currency' => $t->currency,
                    'sector' => $t->sector,
                    'procuring_entity' => $t->procuring_entity,
                    'entity_name' => $t->entity_name,
                    'country_region' => $t->country_region,
                    'location' => $t->location,
                ],
                'submission_details' => [
                    'submission_method' => $t->submission_method,
                    'required_documents' => $t->required_documents ?? [],
                    'eligibility_criteria' => $t->eligibility_criteria ?? [],
                ],
                'important_dates' => [
                    'published_date' => $t->published_date?->toDateString(),
                    'clarification_deadline' => $t->clarification_deadline?->toDateString(),
                    'submission_deadline' => $t->submission_deadline?->toDateString(),
                    'start_date' => $t->start_date?->toDateString(),
                    'end_date' => $t->end_date?->toDateString(),
                ],
                'views_count' => (int) $t->views_count,
                'created_at' => $t->created_at?->toIso8601String(),
                'updated_at' => $t->updated_at?->toIso8601String(),
            ]),
        ]);
    }

    private function budgetLabel(TenderAd $t): ?string
    {
        if ($t->budget_min && $t->budget_max) {
            return '$'.number_format((float) $t->budget_min).' - $'.number_format((float) $t->budget_max);
        }

        return $t->amount ? '$'.number_format((float) $t->amount) : null;
    }

    private function mapTenderListItem(TenderAd $t): array
    {
        return [
            'id' => $t->id,
            'title' => $t->title,
            'slug' => $t->slug,
            'summary' => $t->summary,
            'description' => $t->description,
            'reference' => $t->reference_number,
            'reference_number' => $t->reference_number,
            'sector' => $t->sector,
            'procuring_entity' => $t->procuring_entity ?? $t->entity_name,
            'country' => $t->country_region,
            'location' => $t->location,
            'budget' => $this->budgetLabel($t),
            'deadline' => $t->submission_deadline?->toDateString(),
            'deadline_long' => $t->submission_deadline?->format('F j, Y'),
            'tags' => array_values(array_filter([
                $t->tender_type ? ['label' => $t->tender_type, 'type' => 'type'] : null,
                $t->sector ? ['label' => $t->sector, 'type' => 'sector'] : null,
                $t->location ? ['label' => $t->location, 'type' => 'location'] : null,
            ])),
            'created_at' => $t->created_at?->toIso8601String(),
        ];
    }

    private function mapTenderDetail(TenderAd $t): array
    {
        $attachments = collect($t->attachments ?? [])->values()->map(function ($a, $i) {
            if (is_string($a)) {
                return ['id' => (string) ($i + 1), 'name' => basename($a), 'size' => null, 'url' => $a];
            }
            $arr = (array) $a;

            return [
                'id' => (string) ($arr['id'] ?? ($i + 1)),
                'name' => $arr['name'] ?? $arr['filename'] ?? 'attachment',
                'size' => $arr['size'] ?? null,
                'url' => $arr['url'] ?? $arr['path'] ?? null,
            ];
        })->all();

        return [
            'id' => $t->id,
            'title' => $t->title,
            'slug' => $t->slug,
            'summary' => $t->summary,
            'scope' => $t->scope_of_work ?? $t->description,
            'requirements' => $t->requirements ?? [],
            'reference' => $t->reference_number,
            'sector' => $t->sector,
            'procuring_entity' => $t->procuring_entity ?? $t->entity_name,
            'country' => $t->country_region,
            'location' => $t->location,
            'budget' => $this->budgetLabel($t),
            'deadline' => $t->submission_deadline?->toDateString(),
            'deadline_long' => $t->submission_deadline?->format('F j, Y'),
            'tags' => array_values(array_filter([
                $t->tender_type ? ['label' => $t->tender_type, 'type' => 'type'] : null,
                $t->sector ? ['label' => $t->sector, 'type' => 'sector'] : null,
            ])),
            'submission' => [
                'method' => $t->submission_method,
                'required_documents' => $t->required_documents ?? [],
                'eligibility' => $t->eligibility_criteria ?? [],
            ],
            'attachments' => $attachments,
            'dates' => array_values(array_filter([
                $t->published_date ? ['label' => 'Published', 'value' => $t->published_date->toDateString()] : null,
                $t->clarification_deadline ? ['label' => 'Clarification deadline', 'value' => $t->clarification_deadline->toDateString()] : null,
                $t->submission_deadline ? ['label' => 'Submission deadline', 'value' => $t->submission_deadline->toDateString()] : null,
            ])),
        ];
    }
}
