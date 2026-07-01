@extends('layouts.dashboard')

@section('title', 'Manajemen API Key')

@section('toolbar')
<a href="{{ route('api-keys.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Buat API Key</a>
@endsection

@section('content')
@if (session('new_api_key'))
<div class="mb-4 rounded-lg border border-sky-200 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/50 p-4">
    <h3 class="font-medium text-sky-900 dark:text-sky-100 mb-2">API Key baru berhasil dibuat:</h3>
    <div class="flex flex-wrap items-center gap-2">
        <code id="newApiKey" class="px-3 py-2 rounded-lg bg-white dark:bg-slate-800 text-sm break-all">{{ session('new_api_key') }}</code>
        <button type="button" onclick="copyNewKey()" class="px-3 py-1.5 rounded-lg border border-sky-300 dark:border-sky-700 text-sm hover:bg-sky-100 dark:hover:bg-sky-900">Salin</button>
    </div>
    <p class="mt-3 text-sm text-sky-800 dark:text-sky-200">Simpan key ini dengan aman. Key tidak akan ditampilkan lagi setelah halaman ini.</p>
</div>
@endif

<div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 dark:bg-slate-800/50 text-left text-slate-600 dark:text-slate-400">
                <tr>
                    <th class="px-4 py-3 font-medium">Nama</th>
                    <th class="px-4 py-3 font-medium">Key</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3 font-medium">Kadaluarsa</th>
                    <th class="px-4 py-3 font-medium">Terakhir Digunakan</th>
                    <th class="px-4 py-3 font-medium">Dibuat</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse ($apiKeys as $apiKey)
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $apiKey->name }}</div>
                        <div class="text-xs text-slate-500">ID: {{ $apiKey->id }}</div>
                    </td>
                    <td class="px-4 py-3"><code class="px-2 py-1 rounded bg-slate-100 dark:bg-slate-800 text-xs">{{ substr($apiKey->key, 0, 8) }}...{{ substr($apiKey->key, -4) }}</code></td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 rounded text-xs font-medium {{ $apiKey->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400' }}">
                            {{ $apiKey->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">
                        @if ($apiKey->expires_at){{ $apiKey->expires_at->format('d M Y H:i') }}@else—@endif
                    </td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $apiKey->last_used_at?->diffForHumans() ?? 'Belum pernah' }}</td>
                    <td class="px-4 py-3 text-slate-600 dark:text-slate-400">{{ $apiKey->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <div class="flex justify-end gap-1 flex-wrap">
                            <a href="{{ route('api-keys.edit', $apiKey) }}" class="px-2 py-1 rounded border border-slate-300 dark:border-slate-600 text-xs hover:bg-slate-100 dark:hover:bg-slate-800">Edit</a>
                            <form method="POST" action="{{ route('api-keys.regenerate', $apiKey) }}" onsubmit="return confirm('Regenerasi key akan mengganti key sebelumnya. Lanjutkan?')" class="inline">@csrf<button type="submit" class="px-2 py-1 rounded border border-sky-300 dark:border-sky-700 text-xs hover:bg-sky-50 dark:hover:bg-sky-950">Regenerasi</button></form>
                            <form method="POST" action="{{ route('api-keys.destroy', $apiKey) }}" onsubmit="return confirm('Hapus API Key ini?')" class="inline">@csrf @method('DELETE')<button type="submit" class="px-2 py-1 rounded border border-red-300 dark:border-red-800 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950">Hapus</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-12 text-center text-slate-500">Belum ada API Key.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($apiKeys->hasPages())<div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">{{ $apiKeys->links() }}</div>@endif
</div>
@endsection

@push('scripts')
<script>
function copyNewKey() {
    const el = document.getElementById('newApiKey');
    if (!el) return;
    navigator.clipboard.writeText(el.textContent.trim()).then(() => alert('API Key disalin ke clipboard.'));
}
</script>
@endpush
