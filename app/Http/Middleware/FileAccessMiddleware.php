<?php

namespace App\Http\Middleware;

use App\Models\File;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FileAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        // Get file from route parameter
        $fileId = $request->route('file') ?? $request->route('id') ?? $request->route('slug');
        
        if (!$fileId) {
            return response()->json([
                'success' => false,
                'message' => 'File not specified'
            ], 400);
        }
        
        // Find file by ID or slug
        $file = File::where('id', $fileId)
            ->orWhere('slug', $fileId)
            ->first();
        
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }
        
        // Check if file is public
        if ($file->is_public) {
            // Public files are accessible to everyone
            $request->merge(['file' => $file]);
            return $next($request);
        }
        
        // Private files require authentication
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required for private files'
            ], 401);
        }
        
        // Check if user owns the file or has admin privileges
        if ($file->user_id === $user->id || $user->canManageAllFiles()) {
            $request->merge(['file' => $file]);
            return $next($request);
        }
        
        // Check specific permission if provided
        if ($permission && $user->hasPermissionTo($permission)) {
            $request->merge(['file' => $file]);
            return $next($request);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'You do not have permission to access this file'
        ], 403);
    }
}
