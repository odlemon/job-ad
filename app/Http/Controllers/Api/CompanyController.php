<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function __construct(
        private CompanyService $service
    ) {
    }

    /**
     * Display a listing of companies.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $companies = $this->service->getPaginated($perPage);

        return response()->json($companies);
    }

    /**
     * Store a newly created company.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:companies,slug',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $company = $this->service->create($validated);

        return response()->json($company, 201);
    }

    /**
     * Display the specified company.
     */
    public function show(int $id): JsonResponse
    {
        $company = $this->service->getById($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json($company);
    }

    /**
     * Update the specified company.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $company = $this->service->getById($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255|unique:companies,slug,' . $id,
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'logo' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $company = $this->service->update($company, $validated);

        return response()->json($company);
    }

    /**
     * Remove the specified company.
     */
    public function destroy(int $id): JsonResponse
    {
        $company = $this->service->getById($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $this->service->delete($company);

        return response()->json(['message' => 'Company deleted successfully'], 200);
    }
}
