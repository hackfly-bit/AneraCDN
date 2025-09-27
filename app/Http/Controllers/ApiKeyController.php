<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $this->authorize('view api keys');
        
        $apiKeys = auth()->user()->apiKeys()
            ->latest()
            ->paginate(10);
            
        return view('api-keys.index', compact('apiKeys'));
    }

    public function create()
    {
        $this->authorize('create api keys');
        
        return view('api-keys.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create api keys');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $apiKey = auth()->user()->apiKeys()->create([
            'name' => $validated['name'],
            'key' => Str::random(64),
            'expires_at' => $validated['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key berhasil dibuat!')
            ->with('new_key', $apiKey->key);
    }

    public function show(ApiKey $apiKey)
    {
        $this->authorize('view api keys');
        $this->authorize('update', $apiKey);

        return view('api-keys.show', compact('apiKey'));
    }

    public function edit(ApiKey $apiKey)
    {
        $this->authorize('update api keys');
        $this->authorize('update', $apiKey);

        return view('api-keys.edit', compact('apiKey'));
    }

    public function update(Request $request, ApiKey $apiKey)
    {
        $this->authorize('update api keys');
        $this->authorize('update', $apiKey);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'boolean',
        ]);

        $apiKey->update($validated);

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key berhasil diperbarui!');
    }

    public function destroy(ApiKey $apiKey)
    {
        $this->authorize('delete api keys');
        $this->authorize('update', $apiKey);

        $apiKey->delete();

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key berhasil dihapus!');
    }

    public function regenerate(ApiKey $apiKey)
    {
        $this->authorize('update api keys');
        $this->authorize('update', $apiKey);

        $apiKey->update([
            'key' => Str::random(64),
            'last_used_at' => null,
        ]);

        return redirect()->route('api-keys.index')
            ->with('success', 'API Key berhasil diregenerasi!')
            ->with('new_key', $apiKey->key);
    }
}
