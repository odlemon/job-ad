<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\RemoteUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployerCompanyProfileController extends Controller
{
    public function __construct(
        private CompanyService $companyService,
        private RemoteUploadService $uploadService
    ) {
    }

    /**
     * Display the company profile page.
     */
    public function show()
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        // If no employer or company_id, create a default company or show setup form
        if (!$employer) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Please set up your employer profile first.');
        }

        // If no company_id, try to get or create a company from employer data
        if (!$employer->company_id) {
            // Try to find company by employer's company_name
            $company = null;
            if ($employer->company_name) {
                $company = \App\Models\Company::where('name', $employer->company_name)->first();
            }
            
            // If still no company, create one from employer data
            if (!$company) {
                $companyData = [
                    'name' => $employer->company_name ?? 'My Company',
                    'description' => $employer->company_description ?? null,
                    'industry' => $employer->industry ?? null,
                    'size' => $employer->company_size ?? null,
                    'website' => $employer->website ?? null,
                    'logo' => $employer->company_logo ?? null,
                    'location' => $employer->address ?? null,
                ];
                $company = $this->companyService->create($companyData);
                
                // Link company to employer
                $employer->company_id = $company->id;
                $employer->save();
            } else {
                // Link existing company to employer
                $employer->company_id = $company->id;
                $employer->save();
            }
        } else {
            $company = $this->companyService->getById($employer->company_id);
        }
        
        if (!$company) {
            return redirect()->route('employer.dashboard')
                ->with('error', 'Company not found.');
        }

        // If request wants JSON (for AJAX calls), return JSON
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'company' => [
                    'id' => $company->id,
                    'name' => $company->name,
                    'description' => $company->description,
                    'website' => $company->website,
                    'industry' => $company->industry,
                    'size' => $company->size,
                    'location' => $company->location,
                    'logo' => $company->logo,
                    'cover_image' => $company->cover_image,
                    'gallery_images' => $company->gallery_images,
                    'founded_year' => $company->founded_year,
                    'linkedin' => $company->linkedin,
                    'twitter' => $company->twitter,
                    'culture_benefits' => $company->culture_benefits,
                    'verified_at' => $company->verified_at,
                ],
                'employer' => [
                    'id' => $employer->id,
                    'company_id' => $employer->company_id,
                ],
                'mediaBaseUrl' => $this->uploadService->getMediaBaseUrl(),
            ]);
        }

        return view('employer.company-profile', [
            'company' => $company,
            'employer' => $employer,
            'mediaBaseUrl' => $this->uploadService->getMediaBaseUrl(),
        ]);
    }

    /**
     * Update the company profile.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $company = $this->companyService->getById($employer->company_id);
        
        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // If only uploading files (logo or cover_image), don't require name
        $isFileOnlyUpload = ($request->hasFile('logo') || $request->hasFile('cover_image')) 
            && !$request->has('name') && !$request->has('description') && !$request->has('website')
            && !$request->has('industry') && !$request->has('size') && !$request->has('location');
        
        try {
            $validated = $request->validate([
                'name' => $isFileOnlyUpload ? 'nullable' : 'required|string|max:255',
                'description' => 'nullable|string',
                'website' => 'nullable|url|max:255',
                'industry' => 'nullable|string|max:255',
                'size' => 'nullable|string|max:255',
                'location' => 'nullable|string|max:255',
                'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
                'linkedin' => 'nullable|url|max:255',
                'twitter' => 'nullable|url|max:255',
                'culture_benefits' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        // Handle logo upload - use remote upload service (same as gallery)
        if ($request->hasFile('logo')) {
            $uploadResult = $this->uploadService->uploadFiles(
                [$request->file('logo')],
                'company-logos'
            );
            
            if (!$uploadResult['success']) {
                return response()->json([
                    'message' => $uploadResult['error'] ?? 'Failed to upload logo',
                ], 400);
            }
            
            // Extract URL from upload result (same as gallery)
            if (!empty($uploadResult['files']) && isset($uploadResult['files'][0]['url'])) {
                $validated['logo'] = $uploadResult['files'][0]['url'];
            } else {
                // Fallback: construct URL from filename if URL not provided
                $mediaBaseUrl = $this->uploadService->getMediaBaseUrl();
                $filename = $uploadResult['files'][0]['filename'] ?? 'unknown';
                $validated['logo'] = rtrim($mediaBaseUrl, '/') . '/' . ltrim($filename, '/');
            }
        } else {
            unset($validated['logo']);
        }

        // Handle cover image upload - use remote upload service (same as gallery)
        if ($request->hasFile('cover_image')) {
            $uploadResult = $this->uploadService->uploadFiles(
                [$request->file('cover_image')],
                'company-gallery' // Use company-gallery type (same as gallery images)
            );
            
            if (!$uploadResult['success']) {
                return response()->json([
                    'message' => $uploadResult['error'] ?? 'Failed to upload cover image',
                ], 400);
            }
            
            // Extract URL from upload result (same as gallery)
            if (!empty($uploadResult['files']) && isset($uploadResult['files'][0]['url'])) {
                $validated['cover_image'] = $uploadResult['files'][0]['url'];
            } else {
                // Fallback: construct URL from filename if URL not provided
                $mediaBaseUrl = $this->uploadService->getMediaBaseUrl();
                $filename = $uploadResult['files'][0]['filename'] ?? 'unknown';
                $validated['cover_image'] = rtrim($mediaBaseUrl, '/') . '/' . ltrim($filename, '/');
            }
        } else {
            unset($validated['cover_image']);
        }

        // Only update fields that are actually provided (for file-only uploads, only update the file field)
        $updateData = [];
        if (isset($validated['logo'])) {
            $updateData['logo'] = $validated['logo'];
        }
        if (isset($validated['cover_image'])) {
            $updateData['cover_image'] = $validated['cover_image'];
        }
        // For non-file fields, include them if they're present
        if (isset($validated['name'])) $updateData['name'] = $validated['name'];
        if (isset($validated['description'])) $updateData['description'] = $validated['description'];
        if (isset($validated['website'])) $updateData['website'] = $validated['website'];
        if (isset($validated['industry'])) $updateData['industry'] = $validated['industry'];
        if (isset($validated['size'])) $updateData['size'] = $validated['size'];
        if (isset($validated['location'])) $updateData['location'] = $validated['location'];
        if (isset($validated['founded_year'])) $updateData['founded_year'] = $validated['founded_year'];
        if (isset($validated['linkedin'])) $updateData['linkedin'] = $validated['linkedin'];
        if (isset($validated['twitter'])) $updateData['twitter'] = $validated['twitter'];
        if (isset($validated['culture_benefits'])) $updateData['culture_benefits'] = $validated['culture_benefits'];

        $company = $this->companyService->update($company, $updateData);

        return response()->json([
            'message' => 'Company profile updated successfully',
            'company' => $company,
        ], 200);
    }

    /**
     * Upload gallery images.
     */
    public function uploadGallery(Request $request)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        // Upload files to remote server using RemoteUploadService
        $uploadResult = $this->uploadService->uploadFiles(
            $request->file('images'),
            'company-gallery'
        );

        if (!$uploadResult['success']) {
            return response()->json([
                'message' => $uploadResult['error'] ?? 'Failed to upload images',
            ], 400);
        }

        // Extract file URLs from upload result
        // The upload service returns the full URL in file['url'] - use it directly, simple!
        $uploadedImages = [];
        foreach ($uploadResult['files'] as $file) {
            // Log what URL the upload service returns so we can see what's happening
            \Log::info('Upload service returned URL', [
                'file_url' => $file['url'] ?? 'not set',
                'filename' => $file['filename'] ?? 'not set',
            ]);
            
            // Use the URL directly from the upload service - it should be correct
            if (isset($file['url']) && !empty($file['url'])) {
                $uploadedImages[] = $file['url'];
            } else {
                // Fallback: construct URL from filename if URL not provided
                $mediaBaseUrl = $this->uploadService->getMediaBaseUrl();
                $filename = $file['filename'] ?? 'unknown';
                $uploadedImages[] = rtrim($mediaBaseUrl, '/') . '/' . ltrim($filename, '/');
            }
        }

        // Get current gallery images
        $company = $this->companyService->getById($employer->company_id);
        $gallery = $company->gallery_images ?? [];
        
        // Ensure it's an array (model casts it, but handle both cases)
        if (is_string($gallery)) {
            $gallery = json_decode($gallery, true) ?? [];
        }
        if (!is_array($gallery)) {
            $gallery = [];
        }
        
        // Merge new images (full URLs) with existing gallery
        $gallery = array_merge($gallery, $uploadedImages);

        $updatedCompany = $this->companyService->update($company, [
            'gallery_images' => json_encode($gallery),
        ]);

        // Refresh company to get updated data
        $updatedCompany = $this->companyService->getById($employer->company_id);
        
        // Get gallery images - it's already cast as array by the model
        $finalGallery = $updatedCompany->gallery_images ?? [];
        
        // Ensure it's an array (in case it's still a JSON string)
        if (is_string($finalGallery)) {
            $finalGallery = json_decode($finalGallery, true) ?? [];
        }
        
        // Ensure it's an array
        if (!is_array($finalGallery)) {
            $finalGallery = [];
        }

        // Extract media base URL from the upload service response
        // The upload service returns full URLs in file['url'], so extract the base URL from it
        $mediaBaseUrl = $this->uploadService->getMediaBaseUrl();
        if (!empty($uploadedImages) && str_starts_with($uploadedImages[0], 'http')) {
            // Extract base URL from the full URL (everything before /uploads/)
            // Example: http://31.220.82.129:3050/uploads/profile-photos/file.jpg
            // Should extract: http://31.220.82.129:3050/uploads
            $urlParts = explode('/uploads/', $uploadedImages[0]);
            if (count($urlParts) > 1) {
                $mediaBaseUrl = $urlParts[0] . '/uploads';
            }
        }

        return response()->json([
            'message' => 'Images uploaded successfully',
            'images' => $uploadedImages,
            'gallery_images' => $finalGallery,
            'mediaBaseUrl' => $mediaBaseUrl,
        ], 200);
    }

    /**
     * Delete gallery image.
     */
    public function deleteGalleryImage(Request $request, int $index)
    {
        $user = Auth::user();
        $employer = $user->employer;
        
        if (!$employer || !$employer->company_id) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $company = $this->companyService->getById($employer->company_id);
        $gallery = json_decode($company->gallery_images ?? '[]', true);

        if (isset($gallery[$index])) {
            // Note: File deletion on remote server would need to be handled by the upload service
            // For now, we just remove it from the database
            // TODO: Implement remote file deletion if needed
            
            // Remove from array
            unset($gallery[$index]);
            $gallery = array_values($gallery); // Re-index array

            $this->companyService->update($company, [
                'gallery_images' => json_encode($gallery),
            ]);
            
            // Refresh company to get updated data
            $updatedCompany = $this->companyService->getById($employer->company_id);
            $finalGallery = json_decode($updatedCompany->gallery_images ?? '[]', true);
        } else {
            $finalGallery = $gallery;
        }

        return response()->json([
            'message' => 'Image deleted successfully',
            'gallery_images' => $finalGallery,
            'mediaBaseUrl' => $this->uploadService->getMediaBaseUrl(),
        ], 200);
    }
}
