<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKeyHeader = $request->header('Authorization');
        
        if (!$apiKeyHeader || !str_starts_with($apiKeyHeader, 'Bearer ')) {
            return response()->json([
                'message' => 'API Key tidak valid atau tidak ditemukan'
            ], 401);
        }

        $apiKey = substr($apiKeyHeader, 7);
        
        $validKey = ApiKey::where('key', $apiKey)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->with('user')
            ->first();

        if (!$validKey) {
            return response()->json([
                'message' => 'API Key tidak valid atau sudah kedaluwarsa'
            ], 401);
        }

        // Update last used timestamp
        $validKey->update(['last_used_at' => now()]);

        // Set authenticated user for the request
        $request->merge(['api_user' => $validKey->user]);

        return $next($request);
    }
}
