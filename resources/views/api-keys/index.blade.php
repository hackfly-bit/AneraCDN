@extends('layouts.dashboard')

@section('title', 'Manajemen API Key')

@section('toolbar')
<a href="{{ route('api-keys.create') }}" class="btn btn-primary">
    <i class="fas fa-plus me-2"></i>Buat API Key
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if (session('new_api_key'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h6 class="alert-heading">API Key baru berhasil dibuat:</h6>
            <div class="d-flex align-items-center gap-2 mt-2">
                <code id="newApiKey" class="bg-light p-2 rounded">{{ session('new_api_key') }}</code>
                <button onclick="copyNewKey()" class="btn btn-sm btn-outline-primary">Salin</button>
            </div>
            <hr>
            <p class="mb-0 small">⚠️ Simpan key ini dengan aman. Key tidak akan ditampilkan lagi setelah halaman ini.</p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Key</th>
                                <th>Status</th>
                                <th>Kadaluarsa</th>
                                <th>Terakhir Digunakan</th>
                                <th>Dibuat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($apiKeys as $apiKey)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $apiKey->name }}</div>
                                    <small class="text-muted">ID: {{ $apiKey->id }}</small>
                                </td>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ substr($apiKey->key, 0, 8) }}...{{ substr($apiKey->key, -4) }}</code>
                                </td>
                                <td>
                                    @if ($apiKey->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                    @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($apiKey->expires_at)
                                    <div>{{ $apiKey->expires_at->format('d M Y H:i') }}</div>
                                    <small class="text-muted">({{ $apiKey->expires_at->diffForHumans() }})</small>
                                    @else
                                    <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($apiKey->last_used_at)
                                    <div>{{ $apiKey->last_used_at->format('d M Y H:i') }}</div>
                                    <small class="text-muted">({{ $apiKey->last_used_at->diffForHumans() }})</small>
                                    @else
                                    <span class="text-muted">Belum pernah</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $apiKey->created_at->format('d M Y H:i') }}</div>
                                    <small class="text-muted">({{ $apiKey->created_at->diffForHumans() }})</small>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('api-keys.edit', $apiKey) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <form method="POST" action="{{ route('api-keys.regenerate', $apiKey) }}" onsubmit="return confirm('Regenerasi key akan mengganti key sebelumnya. Lanjutkan?')" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-info">Regenerasi</button>
                                        </form>
                                        <form method="POST" action="{{ route('api-keys.destroy', $apiKey) }}" onsubmit="return confirm('Hapus API Key ini? Tindakan tidak dapat dibatalkan.')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    Belum ada API Key. Mulai dengan membuat API Key baru.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($apiKeys->hasPages())
                <div class="mt-3">
                    {{ $apiKeys->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyNewKey() {
    const el = document.getElementById('newApiKey');
    if (!el) { return; }
    const key = el.textContent.trim();
    navigator.clipboard.writeText(key).then(function() {
        alert('API Key disalin ke clipboard.');
    }).catch(function(err) {
        alert('Gagal menyalin API Key: ' + err);
    });
}
</script>
@endsection
