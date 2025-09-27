@extends('layouts.dashboard')

@section('title', 'Buat API Key Baru')

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
                <h5 class="card-title mb-0">Buat API Key Baru</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('api-keys.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama API Key</label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name') }}"
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
                               value="{{ old('expires_at') }}"
                               class="form-control @error('expires_at') is-invalid @enderror">
                        @error('expires_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Kosongkan jika tidak ingin ada batas waktu kadaluarsa.</div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('api-keys.index') }}" class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Buat API Key
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection