<?php

namespace App\Http\Controllers;

use App\Services\RemoteUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    public function __construct(
        private RemoteUploadService $uploadService
    ) {
    }

    /**
     * Upload single file
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
            'uploadType' => 'sometimes|string|max:255',
        ]);

        try {
            $file = $request->file('file');
            $uploadType = $request->input('uploadType', 'temp');

            $result = $this->uploadService->uploadSingleFile($file, $uploadType);

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('File upload failed', [
                'error' => $e->getMessage(),
                'uploadType' => $request->input('uploadType', 'temp'),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Upload multiple files
     */
    public function uploadFiles(Request $request): JsonResponse
    {
        $request->validate([
            'files.*' => 'required|file|max:10240',
            'uploadType' => 'sometimes|string|max:255',
        ]);

        try {
            $files = $request->file('files');
            $uploadType = $request->input('uploadType', 'temp');

            if (empty($files)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No files provided',
                ], 400);
            }

            $result = $this->uploadService->uploadFiles($files, $uploadType);

            if (!$result['success']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['error'],
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => count($result['files']) . ' file(s) uploaded successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Multiple file upload failed', [
                'error' => $e->getMessage(),
                'uploadType' => $request->input('uploadType', 'temp'),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
