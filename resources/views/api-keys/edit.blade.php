@extends('layouts.dashboard')

@section('title', 'Edit API Key')

@section('toolbar')
<a href="{{ route('api-keys.index') }}" class="btn btn-secondary">
    <i class="fas fa-arrow-left me-2"></i>Kembali
</a>
@endsection

@section('content')
<div class="row">
    <div class="col-md-8 col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Edit API Key</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('api-keys.update', $apiKey) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama API Key</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', $apiKey->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="expires_at" class="form-label">Tanggal Kadaluarsa (Opsional)</label>
                        <input type="date" 
                               name="expires_at" 
                               id="expires_at" 
                               value="{{ old('expires_at', $apiKey->expires_at?->format('Y-m-d')) }}"
                               class="form-control @error('expires_at') is-invalid @enderror">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Kosongkan jika tidak ingin ada batas waktu kadaluarsa.</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1"
                                   {{ $apiKey->is_active ? 'checked' : '' }}
                                   class="form-check-input"
                                   id="is_active">
                            <label class="form-check-label" for="is_active">
                                Aktifkan API Key
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">Informasi API Key</h6>
                                <p class="card-text mb-2">
                                    <strong>Key:</strong> 
                                    <code class="bg-white px-2 py-1 rounded">{{ substr($apiKey->key, 0, 8) }}...{{ substr($apiKey->key, -4) }}</code>
                                </p>
                                <p class="card-text mb-2">
                                    <strong>Dibuat:</strong> {{ $apiKey->created_at->format('d M Y H:i') }}
                                </p>
                                <p class="card-text mb-0">
                                    <strong>Terakhir digunakan:</strong> {{ $apiKey->last_used_at?->diffForHumans() ?? 'Belum pernah' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('api-keys.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Perbarui API Key
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection