<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class RemoteUploadService
{
    private Client $client;
    private string $uploadServiceUrl;
    private string $mediaBaseUrl;
    
    private array $uploadTypeMap = [
        'cashbook-imports' => 'cashbook-imports',
        'financial-reports' => 'financial-reports',
        'application-documents' => 'application-documents',
        'term-sheets' => 'term-sheets',
        'term-sheet-signatures' => 'term-sheet-signatures',
        'cv' => 'cv',
        'resumes' => 'resumes',
        'profile-photos' => 'profile-photos',
        'company-logos' => 'company-logos',
        'company-gallery' => 'company-gallery',
        'company-covers' => 'company-gallery', // Use company-gallery directory for covers
        'job-documents' => 'job-documents',
        'certifications' => 'certifications',
        'tender-documents' => 'tender-documents',
        'temp' => 'temp',
    ];

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 60.0,
        ]);
        $this->uploadServiceUrl = env('UPLOAD_SERVICE_URL', 'http://31.220.82.129:3050/upload');
        $this->mediaBaseUrl = env('MEDIA_BASE_URL', 'http://31.220.82.129/uploads');
    }

    /**
     * Upload single file and return simplified result
     */
    public function uploadSingleFile(UploadedFile $file, string $uploadType = 'temp'): array
    {
        $result = $this->uploadFiles([$file], $uploadType);
        
        if (!$result['success'] || empty($result['files'])) {
            throw new \Exception($result['error'] ?? 'Failed to upload file');
        }

        $uploadedFile = $result['files'][0];
        $urlParts = explode('/uploads/', $uploadedFile['url']);
        $filePath = count($urlParts) > 1 ? $urlParts[1] : $uploadedFile['filename'];

        return [
            'filePath' => $filePath,
            'downloadURL' => $uploadedFile['url'],
            'filename' => $uploadedFile['filename'],
            'originalname' => $uploadedFile['originalname'],
            'size' => $uploadedFile['size'],
            'mimetype' => $uploadedFile['mimetype'],
        ];
    }

    /**
     * Upload multiple files
     */
    public function uploadFiles(array $files, string $uploadType = 'temp'): array
    {
        try {
            if (empty($files)) {
                return [
                    'success' => false,
                    'error' => 'No files to upload',
                ];
            }

            $serverType = $this->mapUploadType($uploadType);
            
            // Prepare multipart form data
            $multipart = [];
            
            foreach ($files as $file) {
                if (!($file instanceof UploadedFile)) {
                    continue;
                }

                $multipart[] = [
                    'name' => 'images',
                    'contents' => fopen($file->getRealPath(), 'r'),
                    'filename' => $file->getClientOriginalName(),
                    'headers' => [
                        'Content-Type' => $file->getMimeType(),
                    ],
                ];
            }
            
            if (empty($multipart)) {
                return [
                    'success' => false,
                    'error' => 'No valid files to upload',
                ];
            }
            
            $multipart[] = [
                'name' => 'type',
                'contents' => $serverType,
            ];

            $response = $this->client->post($this->uploadServiceUrl, [
                'multipart' => $multipart,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (isset($data['success']) && $data['success'] && isset($data['files'])) {
                return [
                    'success' => true,
                    'files' => $data['files'],
                ];
            }

            return [
                'success' => false,
                'error' => $data['message'] ?? 'Upload failed',
            ];
        } catch (GuzzleException $e) {
            Log::error('File upload error', [
                'message' => $e->getMessage(),
                'uploadType' => $uploadType,
                'code' => $e->getCode(),
            ]);

            $errorMessage = 'Failed to upload files';
            if ($e->getCode() === 0) {
                $errorMessage = 'Cannot connect to upload server';
            } elseif ($e->getCode() === 28) {
                $errorMessage = 'Upload timeout - server took too long to respond';
            } elseif ($e->hasResponse()) {
                $response = $e->getResponse();
                $errorMessage = 'Server error (' . $response->getStatusCode() . ')';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
            ];
        } catch (\Exception $e) {
            Log::error('File upload exception', [
                'message' => $e->getMessage(),
                'uploadType' => $uploadType,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Map upload type to server directory
     */
    private function mapUploadType(string $uploadType): string
    {
        $normalized = strtolower(preg_replace('/[^a-z0-9-]/', '-', $uploadType));
        return $this->uploadTypeMap[$normalized] ?? 'temp';
    }

    /**
     * Get media base URL
     */
    public function getMediaBaseUrl(): string
    {
        return $this->mediaBaseUrl;
    }

    /**
     * Download file from server
     */
    public function downloadFile(string $filePath): string
    {   
        $url = str_starts_with($filePath, 'http') 
            ? $filePath 
            : "{$this->mediaBaseUrl}/{$filePath}";

        $response = $this->client->get($url);
        return $response->getBody()->getContents();
    }
}
