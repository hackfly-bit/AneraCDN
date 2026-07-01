<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    /**
     * Display the specified file
     */
    public function show(Request $request, string $slug)
    {
        $file = $this->findAccessibleFile($request, $slug);

        if (! Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage');
        }

        FileActivity::create([
            'file_id' => $file->id,
            'user_id' => $request->user()?->id,
            'action' => FileActivity::ACTION_VIEW,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('file.show', compact('file'));
    }

    /**
     * Download the specified file
     */
    public function download(Request $request, string $slug)
    {
        $file = $this->findAccessibleFile($request, $slug);

        if (! Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage');
        }

        $file->incrementDownloadCount();

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
     * Serve file inline for preview (img, video, etc.).
     */
    public function view(Request $request, string $slug): StreamedResponse
    {
        $file = $this->findAccessibleFile($request, $slug);

        if (! Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage');
        }

        return Storage::disk($file->disk)->response($file->path, $file->name, [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline; filename="'.$file->name.'"',
        ]);
    }

    /**
     * Serve the thumbnail for an image file.
     */
    public function thumbnail(Request $request, string $slug): StreamedResponse
    {
        $file = $this->findAccessibleFile($request, $slug);

        if (! $file->thumbnail_path || ! Storage::disk($file->disk)->exists($file->thumbnail_path)) {
            abort(404, 'Thumbnail not found');
        }

        return Storage::disk($file->disk)->response($file->thumbnail_path);
    }

    /**
     * Serve the WebP variant of an image file.
     */
    public function webp(Request $request, string $slug): StreamedResponse
    {
        $file = $this->findAccessibleFile($request, $slug);

        if (! $file->webp_path || ! Storage::disk($file->disk)->exists($file->webp_path)) {
            abort(404, 'WebP version not found');
        }

        return Storage::disk($file->disk)->response($file->webp_path, pathinfo($file->name, PATHINFO_FILENAME).'.webp', [
            'Content-Type' => 'image/webp',
            'Content-Disposition' => 'inline; filename="'.pathinfo($file->name, PATHINFO_FILENAME).'.webp"',
        ]);
    }

    protected function findAccessibleFile(Request $request, string $slug): File
    {
        $file = File::with('user:id,name,email')->where('slug', $slug)->first();

        if (! $file) {
            abort(404, 'File not found');
        }

        if (! $file->is_public) {
            $user = $request->user();
            if (! $user || (! $user->canManageAllFiles() && $file->user_id !== $user->id)) {
                abort(403, 'You do not have permission to access this file');
            }
        }

        return $file;
    }
}
