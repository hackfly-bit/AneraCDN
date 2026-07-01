<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\FileActivity;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('auth');
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }

    /**
     * Dashboard overview
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Get user's file statistics
        $userStats = [
            'total_files' => File::where('user_id', $user->id)->count(),
            'total_size' => File::where('user_id', $user->id)->sum('size'),
            'public_files' => File::where('user_id', $user->id)->where('is_public', true)->count(),
            'private_files' => File::where('user_id', $user->id)->where('is_public', false)->count(),
        ];

        // Recent files
        $recentFiles = File::where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        // Recent activities
        $recentActivities = FileActivity::with('file', 'user')
            ->whereHas('file', function ($query) use ($user) {
                if (! $user->canManageAllFiles()) {
                    $query->where('user_id', $user->id);
                }
            })
            ->latest()
            ->limit(20)
            ->get();

        // Storage stats (for admins)
        $storageStats = null;
        if ($user->hasPermissionTo('view dashboard stats')) {
            $storageStats = $this->fileService->getStorageStats();
        }

        return view('dashboard.index', compact(
            'userStats',
            'recentFiles',
            'recentActivities',
            'storageStats'
        ));
    }

    /**
     * Files management page
     */
    public function files(Request $request)
    {
        $user = $request->user();

        $query = File::with('user');

        // If not admin, only show user's files
        if (! $user->canManageAllFiles()) {
            $query->where('user_id', $user->id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('type')) {
            switch ($request->type) {
                case 'images':
                    $query->where('mime_type', 'like', 'image/%');
                    break;
                case 'videos':
                    $query->where('mime_type', 'like', 'video/%');
                    break;
                case 'documents':
                    $query->whereIn('mime_type', [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'text/plain',
                    ]);
                    break;
            }
        }

        if ($request->filled('folder')) {
            $query->where('folder', $request->folder);
        }

        if ($request->filled('visibility')) {
            $query->where('is_public', $request->visibility === 'public');
        }

        $files = $query->latest()->paginate(20);

        // Get available folders
        $folders = File::whereNotNull('folder')
            ->distinct()
            ->pluck('folder')
            ->filter()
            ->sort();

        return view('dashboard.files', compact('files', 'folders'));
    }

    /**
     * File upload page
     */
    public function upload()
    {
        $allowedTypes = explode(',', config('app.allowed_file_types', 'jpg,jpeg,png,gif,webp,mp4,avi,mov,pdf,doc,docx,txt,zip'));
        $maxFileSize = config('app.max_file_size', 104857600); // 100MB default

        return view('dashboard.upload', compact('allowedTypes', 'maxFileSize'));
    }

    /**
     * Statistics page
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        if (! $user->hasPermissionTo('view dashboard stats')) {
            abort(403);
        }

        $stats = $this->fileService->getStorageStats();

        // Add active users count
        $stats['active_users'] = DB::table('users')->count();

        // File type distribution
        $fileTypeStats = DB::table('files')
            ->select(
                DB::raw('CASE 
                    WHEN mime_type LIKE "image/%" THEN "Images"
                    WHEN mime_type LIKE "video/%" THEN "Videos"
                    WHEN mime_type IN ("application/pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "text/plain") THEN "Documents"
                    ELSE "Others"
                END as type'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(size) as total_size')
            )
            ->groupBy('type')
            ->get();

        // Upload trends (last 30 days)
        $uploadTrends = DB::table('files')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as uploads'),
                DB::raw('SUM(size) as total_size')
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare data for charts and tables
        $stats['file_types'] = collect($fileTypeStats)->pluck('count', 'type')->toArray();
        $stats['upload_trends'] = collect($uploadTrends)->pluck('uploads', 'date')->toArray();

        // Top files (most downloaded)
        $stats['top_files'] = File::select('name as original_name', 'download_count as downloads', 'size')
            ->orderByDesc('download_count')
            ->limit(10)
            ->get()
            ->map(function ($file) {
                $file->human_size = $this->formatBytes($file->size);

                return $file;
            });

        // User storage usage
        $stats['user_storage'] = DB::table('files')
            ->join('users', 'files.user_id', '=', 'users.id')
            ->select(
                'users.name',
                DB::raw('COUNT(*) as files_count'),
                DB::raw('SUM(files.size) as total_size')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total_size')
            ->limit(10)
            ->get()
            ->map(function ($user) {
                $user->total_size_human = $this->formatBytes($user->total_size);

                return $user;
            });

        return view('dashboard.stats', compact(
            'stats',
            'fileTypeStats',
            'uploadTrends'
        ));
    }

    /**
     * Activities page
     */
    public function activities(Request $request)
    {
        $user = $request->user();

        $query = FileActivity::with('file', 'user');

        // If not admin, only show activities for user's files
        if (! $user->canManageAllFiles()) {
            $query->whereHas('file', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // Apply filters
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $activities = $query->latest()->paginate(50);

        // Get available actions for filter
        $actions = FileActivity::distinct()->pluck('action');

        // Get users for filter (admins only)
        $users = collect();
        if ($user->canManageAllFiles()) {
            $users = \App\Models\User::select('id', 'name', 'email')->get();
        }

        return view('dashboard.activities', compact('activities', 'actions', 'users'));
    }
}
