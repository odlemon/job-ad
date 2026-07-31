<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\TenderAd;
use App\Models\JobCategory;
use App\Services\RemoteUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class EmployerTenderController extends Controller
{
    public function __construct(
        private RemoteUploadService $uploadService
    ) {
    }

    /**
     * List tenders created by the authenticated employer.
     */
    public function index(Request $request)
    {
        $tenders = TenderAd::with('category')
            ->where('created_by', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        $categories = JobCategory::where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();

        return view('employer.tenders.index', [
            'tenders' => $tenders,
            'categories' => $categories,
        ]);
    }

    /**
     * Upload a single document for a tender (used during create/edit). Returns URL and metadata.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document' => 'required|file|max:10240', // 10MB
            'name' => 'nullable|string|max:255',
        ]);

        $file = $request->file('document');
        $uploadResult = $this->uploadService->uploadFiles([$file], 'tender-documents');

        if (!$uploadResult['success']) {
            return response()->json([
                'message' => $uploadResult['error'] ?? 'Failed to upload document',
            ], 400);
        }

        $uploaded = $uploadResult['files'][0] ?? null;
        if (!$uploaded) {
            return response()->json(['message' => 'Upload failed'], 400);
        }

        $url = $uploaded['url'] ?? null;
        if (!$url) {
            $mediaBaseUrl = $this->uploadService->getMediaBaseUrl();
            $filename = $uploaded['filename'] ?? 'unknown';
            $url = rtrim($mediaBaseUrl, '/') . '/' . ltrim($filename, '/');
        }

        $name = $request->input('name') ?: $file->getClientOriginalName();
        $type = strtoupper(pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION)) ?: 'File';
        if ($type === 'PDF') {
            // keep as PDF
        } elseif (in_array(strtolower($type), ['doc', 'docx'])) {
            $type = 'DOC';
        } else {
            $type = 'File';
        }

        return response()->json([
            'url' => $url,
            'name' => $name,
            'type' => $type,
            'size' => $file->getSize() ? $this->formatFileSize($file->getSize()) : null,
        ]);
    }

    private function formatFileSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Store a new tender (from create modal).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:64',
            'tender_type' => 'nullable|string|max:32',
            'category_id' => 'nullable|exists:job_categories,id',
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'entity_name' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'procuring_entity' => 'nullable|string|max:255',
            'country_region' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'published_date' => 'nullable|date',
            'clarification_deadline' => 'nullable|date',
            'submission_deadline' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'submission_method' => 'nullable|string|max:255',
            'requirements' => 'nullable',
            'required_documents' => 'nullable',
            'eligibility_criteria' => 'nullable',
            'attachments' => 'nullable',
            'status' => 'nullable|string|in:draft,pending_approval,active',
        ]);

        // Decode JSON strings for array fields (sent from FormData)
        $requirements = $request->input('requirements');
        if (is_string($requirements)) {
            $requirements = json_decode($requirements, true);
        }
        $required_documents = $request->input('required_documents');
        if (is_string($required_documents)) {
            $required_documents = json_decode($required_documents, true);
        }
        $eligibility_criteria = $request->input('eligibility_criteria');
        if (is_string($eligibility_criteria)) {
            $eligibility_criteria = json_decode($eligibility_criteria, true);
        }
        $attachments = $request->input('attachments');
        if (is_string($attachments)) {
            $attachments = json_decode($attachments, true);
        }
        $attachments = is_array($attachments) ? $attachments : null;

        $slug = Str::slug(Str::limit($validated['title'], 50));
        $baseSlug = $slug;
        $i = 0;
        while (TenderAd::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . (++$i);
        }

        $tender = new TenderAd();
        $tender->title = $validated['title'];
        $tender->slug = $slug;
        $tender->created_by = Auth::id();
        $tender->status = $validated['status'] ?? 'active';
        $tender->reference_number = $validated['reference_number'] ?? null;
        $tender->tender_type = $validated['tender_type'] ?? null;
        $tender->category_id = $validated['category_id'] ?? null;
        $tender->description = $validated['description'] ?? null;
        $tender->summary = $validated['summary'] ?? null;
        $tender->scope_of_work = $validated['scope_of_work'] ?? null;
        $tender->entity_name = $validated['entity_name'] ?? null;
        $tender->sector = $validated['sector'] ?? null;
        $tender->procuring_entity = $validated['procuring_entity'] ?? null;
        $tender->country_region = $validated['country_region'] ?? null;
        $tender->location = $validated['location'] ?? null;
        $tender->start_date = isset($validated['start_date']) ? $validated['start_date'] : null;
        $tender->end_date = isset($validated['end_date']) ? $validated['end_date'] : null;
        $tender->published_date = isset($validated['published_date']) ? $validated['published_date'] : null;
        $tender->clarification_deadline = isset($validated['clarification_deadline']) ? $validated['clarification_deadline'] : null;
        $tender->submission_deadline = isset($validated['submission_deadline']) ? $validated['submission_deadline'] : null;
        $tender->amount = $validated['amount'] ?? null;
        $tender->budget_min = $validated['budget_min'] ?? null;
        $tender->budget_max = $validated['budget_max'] ?? null;
        $tender->currency = $validated['currency'] ?? 'SCR';
        $tender->submission_method = $validated['submission_method'] ?? null;
        $tender->requirements = is_array($requirements) ? $requirements : null;
        $tender->required_documents = is_array($required_documents) ? $required_documents : null;
        $tender->eligibility_criteria = is_array($eligibility_criteria) ? $eligibility_criteria : null;
        $tender->attachments = $attachments;
        $tender->save();

        $tender->load('category');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Tender created successfully.',
                'tender' => [
                    'id' => $tender->id,
                    'title' => $tender->title,
                    'slug' => $tender->slug,
                    'reference_number' => $tender->reference_number,
                    'tender_type' => $tender->tender_type,
                    'status' => $tender->status,
                    'location' => $tender->location,
                    'entity_name' => $tender->entity_name,
                    'submission_deadline' => $tender->submission_deadline?->format('M d, Y'),
                    'created_at' => $tender->created_at->format('M d, Y'),
                    'category' => $tender->category ? ['id' => $tender->category->id, 'name' => $tender->category->name] : null,
                ],
            ], 201);
        }

        return redirect()->route('employer.tenders.index')->with('success', 'Tender created successfully.');
    }

    /**
     * Get a single tender (for view modal / edit form). Must belong to current user.
     */
    public function show(int $id)
    {
        $tender = TenderAd::with('category')
            ->where('created_by', Auth::id())
            ->findOrFail($id);

        $data = [
            'id' => $tender->id,
            'title' => $tender->title,
            'slug' => $tender->slug,
            'reference_number' => $tender->reference_number,
            'tender_type' => $tender->tender_type,
            'category_id' => $tender->category_id,
            'description' => $tender->description,
            'summary' => $tender->summary,
            'scope_of_work' => $tender->scope_of_work,
            'entity_name' => $tender->entity_name,
            'sector' => $tender->sector,
            'procuring_entity' => $tender->procuring_entity,
            'country_region' => $tender->country_region,
            'location' => $tender->location,
            'start_date' => $tender->start_date?->format('Y-m-d'),
            'end_date' => $tender->end_date?->format('Y-m-d'),
            'published_date' => $tender->published_date?->format('Y-m-d'),
            'clarification_deadline' => $tender->clarification_deadline?->format('Y-m-d'),
            'submission_deadline' => $tender->submission_deadline?->format('Y-m-d'),
            'amount' => $tender->amount,
            'budget_min' => $tender->budget_min,
            'budget_max' => $tender->budget_max,
            'currency' => $tender->currency,
            'submission_method' => $tender->submission_method,
            'requirements' => $tender->requirements,
            'required_documents' => $tender->required_documents,
            'eligibility_criteria' => $tender->eligibility_criteria,
            'attachments' => $tender->attachments,
            'status' => $tender->status,
            'category' => $tender->category ? ['id' => $tender->category->id, 'name' => $tender->category->name] : null,
        ];

        return response()->json(['tender' => $data]);
    }

    /**
     * Update a tender. Same validation as store.
     */
    public function update(Request $request, int $id)
    {
        $tender = TenderAd::where('created_by', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'reference_number' => 'nullable|string|max:64',
            'tender_type' => 'nullable|string|max:32',
            'category_id' => 'nullable|exists:job_categories,id',
            'description' => 'nullable|string',
            'summary' => 'nullable|string',
            'scope_of_work' => 'nullable|string',
            'entity_name' => 'nullable|string|max:255',
            'sector' => 'nullable|string|max:255',
            'procuring_entity' => 'nullable|string|max:255',
            'country_region' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'published_date' => 'nullable|date',
            'clarification_deadline' => 'nullable|date',
            'submission_deadline' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|max:3',
            'submission_method' => 'nullable|string|max:255',
            'requirements' => 'nullable',
            'required_documents' => 'nullable',
            'eligibility_criteria' => 'nullable',
            'attachments' => 'nullable',
            'status' => 'nullable|string|in:draft,pending_approval,active',
        ]);

        $requirements = $request->input('requirements');
        if (is_string($requirements)) {
            $requirements = json_decode($requirements, true);
        }
        $required_documents = $request->input('required_documents');
        if (is_string($required_documents)) {
            $required_documents = json_decode($required_documents, true);
        }
        $eligibility_criteria = $request->input('eligibility_criteria');
        if (is_string($eligibility_criteria)) {
            $eligibility_criteria = json_decode($eligibility_criteria, true);
        }
        $attachments = $request->input('attachments');
        if (is_string($attachments)) {
            $attachments = json_decode($attachments, true);
        }
        $attachments = is_array($attachments) ? $attachments : null;

        $tender->title = $validated['title'];
        $tender->status = $validated['status'] ?? $tender->status;
        $tender->reference_number = $validated['reference_number'] ?? null;
        $tender->tender_type = $validated['tender_type'] ?? null;
        $tender->category_id = $validated['category_id'] ?? null;
        $tender->description = $validated['description'] ?? null;
        $tender->summary = $validated['summary'] ?? null;
        $tender->scope_of_work = $validated['scope_of_work'] ?? null;
        $tender->entity_name = $validated['entity_name'] ?? null;
        $tender->sector = $validated['sector'] ?? null;
        $tender->procuring_entity = $validated['procuring_entity'] ?? null;
        $tender->country_region = $validated['country_region'] ?? null;
        $tender->location = $validated['location'] ?? null;
        $tender->start_date = $validated['start_date'] ?? null;
        $tender->end_date = $validated['end_date'] ?? null;
        $tender->published_date = $validated['published_date'] ?? null;
        $tender->clarification_deadline = $validated['clarification_deadline'] ?? null;
        $tender->submission_deadline = $validated['submission_deadline'] ?? null;
        $tender->amount = $validated['amount'] ?? null;
        $tender->budget_min = $validated['budget_min'] ?? null;
        $tender->budget_max = $validated['budget_max'] ?? null;
        $tender->currency = $validated['currency'] ?? 'SCR';
        $tender->submission_method = $validated['submission_method'] ?? null;
        $tender->requirements = is_array($requirements) ? $requirements : null;
        $tender->required_documents = is_array($required_documents) ? $required_documents : null;
        $tender->eligibility_criteria = is_array($eligibility_criteria) ? $eligibility_criteria : null;
        $tender->attachments = $attachments;
        $tender->save();

        $tender->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Tender updated successfully.',
            'tender' => [
                'id' => $tender->id,
                'title' => $tender->title,
                'slug' => $tender->slug,
                'reference_number' => $tender->reference_number,
                'tender_type' => $tender->tender_type,
                'status' => $tender->status,
                'location' => $tender->location,
                'entity_name' => $tender->entity_name,
                'submission_deadline' => $tender->submission_deadline?->format('M d, Y'),
                'category' => $tender->category ? ['id' => $tender->category->id, 'name' => $tender->category->name] : null,
            ],
        ]);
    }

    /**
     * Publish draft tender so it appears on the public tenders page (no approval process).
     */
    public function submitForApproval(int $id)
    {
        $tender = TenderAd::where('created_by', Auth::id())->findOrFail($id);
        if ($tender->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Only draft tenders can be published.'], 422);
        }
        $tender->status = 'active';
        $tender->save();
        $tender->load('category');
        return response()->json([
            'success' => true,
            'message' => 'Tender is now live on the tenders page.',
            'tender' => [
                'id' => $tender->id,
                'title' => $tender->title,
                'reference_number' => $tender->reference_number,
                'tender_type' => $tender->tender_type,
                'status' => $tender->status,
                'location' => $tender->location,
                'entity_name' => $tender->entity_name,
                'submission_deadline' => $tender->submission_deadline?->format('M d, Y'),
            ],
        ]);
    }

    /**
     * Delete a tender.
     */
    public function destroy(int $id)
    {
        $tender = TenderAd::where('created_by', Auth::id())->findOrFail($id);
        $tender->delete();
        return response()->json(['success' => true, 'message' => 'Tender deleted.']);
    }
}
