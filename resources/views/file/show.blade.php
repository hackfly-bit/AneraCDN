@extends('layouts.dashboard')

@section('title', 'View File - ' . $file->display_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">{{ $file->display_name }}</h1>
                    <p class="text-muted mb-0">{{ $file->name }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('files.download', $file->slug) }}" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i>Download
                    </a>
                    @auth
                        @if(auth()->user()->canManageAllFiles() || $file->user_id === auth()->id())
                            <a href="{{ route('dashboard.files') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i>Back to Files
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- File Info Card -->
            <div class="row">
                <div class="col-lg-8">
                    <!-- File Preview -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            @if($file->isImage())
                                <img src="{{ Storage::disk($file->disk)->url($file->path) }}" 
                                     alt="{{ $file->display_name }}" 
                                     class="img-fluid rounded"
                                     style="max-height: 500px;">
                            @elseif($file->isVideo())
                                <video controls class="w-100" style="max-height: 500px;">
                                    <source src="{{ Storage::disk($file->disk)->url($file->path) }}" type="{{ $file->mime_type }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <div class="text-center py-5">
                                    <i class="bi bi-file-earmark display-1 text-muted"></i>
                                    <h4 class="mt-3">{{ $file->display_name }}</h4>
                                    <p class="text-muted">{{ strtoupper($file->extension) }} File</p>
                                    <a href="{{ route('files.download', $file->slug) }}" class="btn btn-primary">
                                        <i class="bi bi-download me-1"></i>Download to View
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- File Details -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">File Details</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-4">Size:</dt>
                                <dd class="col-sm-8">{{ number_format($file->size / 1024 / 1024, 2) }} MB</dd>
                                
                                <dt class="col-sm-4">Type:</dt>
                                <dd class="col-sm-8">{{ $file->mime_type }}</dd>
                                
                                <dt class="col-sm-4">Extension:</dt>
                                <dd class="col-sm-8">{{ strtoupper($file->extension) }}</dd>
                                
                                @if($file->folder)
                                <dt class="col-sm-4">Folder:</dt>
                                <dd class="col-sm-8">{{ $file->folder }}</dd>
                                @endif
                                
                                <dt class="col-sm-4">Visibility:</dt>
                                <dd class="col-sm-8">
                                    @if($file->is_public)
                                        <span class="badge bg-success">Public</span>
                                    @else
                                        <span class="badge bg-warning">Private</span>
                                    @endif
                                </dd>
                                
                                <dt class="col-sm-4">Downloads:</dt>
                                <dd class="col-sm-8">{{ number_format($file->download_count) }}</dd>
                                
                                <dt class="col-sm-4">Uploaded:</dt>
                                <dd class="col-sm-8">{{ $file->created_at->format('M d, Y H:i') }}</dd>
                                
                                @if($file->user)
                                <dt class="col-sm-4">Uploaded by:</dt>
                                <dd class="col-sm-8">{{ $file->user->name }}</dd>
                                @endif
                                
                                @if($file->last_accessed_at)
                                <dt class="col-sm-4">Last accessed:</dt>
                                <dd class="col-sm-8">{{ $file->last_accessed_at->format('M d, Y H:i') }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection