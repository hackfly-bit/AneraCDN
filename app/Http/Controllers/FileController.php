<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Display the specified file
     */
    public function show(Request $request, string $slug)
    {
        $file = File::with('user:id,name,email')
            ->where('slug', $slug)
            ->first();

        if (!$file) {
            abort(404, 'File not found');
        }

        // Check if file is public or user has permission
        if (!$file->is_public) {
            $user = $request->user();
            if (!$user || (!$user->canManageAllFiles() && $file->user_id !== $user->id)) {
                abort(403, 'You do not have permission to view this file');
            }
        }

        // Check if file exists on storage
        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage');
        }

        // Log view activity
        FileActivity::create([
            'file_id' => $file->id,
            'user_id' => $request->user()?->id,
            'action' => FileActivity::ACTION_VIEW,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return view('file.show', compact('file'));
    }

    /**
     * Download the specified file
     */
    public function download(Request $request, string $slug)
    {
        $file = File::where('slug', $slug)->first();

        if (!$file) {
            abort(404, 'File not found');
        }

        // Check if file is public or user has permission
        if (!$file->is_public) {
            $user = $request->user();
            if (!$user || (!$user->canManageAllFiles() && $file->user_id !== $user->id)) {
                abort(403, 'You do not have permission to download this file');
            }
        }

        // Check if file exists on storage
        if (!Storage::disk($file->disk)->exists($file->path)) {
            abort(404, 'File not found on storage');
        }

        // Increment download count
        $file->incrementDownloadCount();

        // Log download activity
        FileActivity::create([
            'file_id' => $file->id,
            'user_id' => $request->user()?->id,
            'action' => FileActivity::ACTION_DOWNLOAD,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return Storage::disk($file->disk)->download($file->path, $file->name);
    }
}
