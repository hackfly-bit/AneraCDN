<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\File;
use App\Models\FileActivity;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class FileController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('auth:sanctum')->except(['download']);
    }

    /**
     * Display a listing of files with filtering and search
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = File::query();

        // If not admin, only show user's files
        if (! $user->canManageAllFiles()) {
            $query->where('user_id', $user->id);
        }

        $files = QueryBuilder::for($query)
            ->allowedFilters([
                'name',
                'mime_type',
                'folder',
                'is_public',
                AllowedFilter::exact('extension'),
                AllowedFilter::scope('images'),
                AllowedFilter::scope('videos'),
                AllowedFilter::scope('documents'),
            ])
            ->allowedSorts(['name', 'size', 'created_at', 'download_count'])
            ->defaultSort('-created_at')
            ->with('user:id,name,email')
            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $files,
        ]);
    }

    /**
     * Store a newly uploaded file
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|max:'.(config('app.max_file_size', 104857600) / 1024), // Convert to KB
            'folder' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if user can upload files
        if (! $request->user()->canUploadFiles()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to upload files',
            ], 403);
        }

        $typeError = $this->fileTypeError($request->file('file'));
        if ($typeError) {
            return response()->json([
                'success' => false,
                'message' => $typeError,
            ], 422);
        }

        try {
            $file = $this->fileService->uploadFile(
                $request->file('file'),
                $request->user()->id,
                $request->get('folder'),
                $request->get('is_public', true)
            );

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => $file->load('user:id,name,email'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'File upload failed: '.$e->getMessage() : 'File upload failed',
            ], 500);
        }
    }

    /**
     * Upload file from dashboard (supports multiple files)
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'files' => 'required|array',
            'files.*' => 'required|file|max:'.(config('app.max_file_size', 104857600) / 1024), // Convert to KB
            'folder' => 'nullable|string|max:255',
            'visibility' => 'required|in:public,private',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check if user can upload files
        if (! $request->user()->canUploadFiles()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to upload files',
            ], 403);
        }

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            $typeError = $this->fileTypeError($file);
            if ($typeError) {
                $errors[] = "File {$file->getClientOriginalName()}: {$typeError}";

                continue;
            }

            try {
                $uploadedFile = $this->fileService->uploadFile(
                    $file,
                    $request->user()->id,
                    $request->get('folder'),
                    $request->get('visibility') === 'public'
                );

                $uploadedFiles[] = $uploadedFile->load('user:id,name,email');
            } catch (\Exception $e) {
                $errors[] = "File {$file->getClientOriginalName()}: {$e->getMessage()}";
            }
        }

        if (empty($uploadedFiles) && ! empty($errors)) {
            return response()->json([
                'success' => false,
                'message' => 'All files failed to upload',
                'errors' => $errors,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedFiles).' file(s) uploaded successfully',
            'data' => $uploadedFiles,
            'errors' => $errors,
        ], 201);
    }

    /**
     * Display the specified file
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $file = File::with('user:id,name,email', 'activities.user:id,name')
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('slug', $id);
            })
            ->first();

        if (! $file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        // Check permissions
        if (! $user->canManageAllFiles() && $file->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this file',
            ], 403);
        }

        // Log view activity
        FileActivity::create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'action' => FileActivity::ACTION_VIEW,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $file,
        ]);
    }

    /**
     * Update the specified file metadata
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        $file = File::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->first();

        if (! $file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        // Check permissions
        if (! $user->canManageAllFiles() && $file->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this file',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'display_name' => 'nullable|string|max:255',
            'folder' => 'nullable|string|max:255',
            'is_public' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldData = $file->only(['display_name', 'folder', 'is_public']);

        $file->update($request->only(['display_name', 'folder', 'is_public']));

        // Log update activity
        FileActivity::create([
            'file_id' => $file->id,
            'user_id' => $user->id,
            'action' => FileActivity::ACTION_UPDATE,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'metadata' => [
                'old_data' => $oldData,
                'new_data' => $file->only(['display_name', 'folder', 'is_public']),
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File updated successfully',
            'data' => $file->fresh()->load('user:id,name,email'),
        ]);
    }

    /**
     * Remove the specified file
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $file = File::where(function ($query) use ($id) {
            $query->where('id', $id)->orWhere('slug', $id);
        })->first();

        if (! $file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        // Check permissions
        if (! $user->canDeleteFiles() && $file->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this file',
            ], 403);
        }

        try {
            $this->fileService->deleteFile($file, $user->id);

            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? 'File deletion failed: '.$e->getMessage() : 'File deletion failed',
            ], 500);
        }
    }

    /**
     * Download file
     */
    public function download(Request $request, $slug)
    {
        $file = File::where('slug', $slug)->first();

        if (! $file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found',
            ], 404);
        }

        // Check if file is public or user has permission
        if (! $file->is_public) {
            $user = $request->user();
            if (! $user || (! $user->canManageAllFiles() && $file->user_id !== $user->id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to download this file',
                ], 403);
            }
        }

        // Check if file exists
        if (! Storage::disk($file->disk)->exists($file->path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found on storage',
            ], 404);
        }

        // Increment download count
        $file->incrementDownloadCount();

        // Log download activity
        FileActivity::create([
            'file_id' => $file->id,
            'user_id' => $request->user()?->id,
            'action' => FileActivity::ACTION_DOWNLOAD,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return Storage::disk($file->disk)->download($file->path, $file->name);
    }

    /**
     * Get storage statistics
     */
    public function stats(Request $request)
    {
        if (! $request->user()->hasPermissionTo('view dashboard stats')) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view statistics',
            ], 403);
        }

        $stats = $this->fileService->getStorageStats();

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    private function allowedMimesRule(): string
    {
        return implode(',', array_map('trim', explode(',', config('app.allowed_file_types'))));
    }

    private function fileTypeError(?UploadedFile $file): ?string
    {
        if (! $file) {
            return 'No file uploaded';
        }

        $allowedTypes = array_map('trim', explode(',', config('app.allowed_file_types')));
        $extension = strtolower($file->getClientOriginalExtension());

        if (! in_array($extension, $allowedTypes)) {
            return 'File type not allowed. Allowed types: '.implode(', ', $allowedTypes);
        }

        $mimeValidator = Validator::make(
            ['file' => $file],
            ['file' => 'file|mimes:'.$this->allowedMimesRule()]
        );

        if ($mimeValidator->fails()) {
            return 'File content does not match its extension';
        }

        return null;
    }
}
