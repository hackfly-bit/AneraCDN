@extends('layouts.dashboard')

@section('title', 'File Preview')

@section('toolbar')
<div class="btn-group" role="group">
    <a href="{{ route('file.download', $file->slug) }}" class="btn btn-success">
        <i class="bi bi-download me-2"></i>
        Download
    </a>
    <button type="button" class="btn btn-outline-primary" onclick="copyToClipboard('{{ $file->cdn_url }}')">
        <i class="bi bi-link-45deg me-2"></i>
        Copy URL
    </button>
    @if($file->webp_path)
    <button type="button" class="btn btn-outline-info" onclick="copyToClipboard('{{ $file->webp_url }}')">
        <i class="bi bi-image me-2"></i>
        Copy WebP URL
    </button>
    @endif
    <a href="{{ route('dashboard.files') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Files
    </a>
</div>
@endsection

@section('content')
<div class="row">
    <!-- File Preview -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $file->display_name ?: $file->name }}</h5>
            </div>
            <div class="card-body text-center">
                @if($file->isImage())
                    <img src="{{ $file->url }}" alt="{{ $file->name }}" class="img-fluid rounded shadow" style="max-height: 600px;">
                    
                    @if($file->webp_path)
                    <div class="mt-3">
                        <small class="text-muted">WebP Version Available:</small><br>
                        <img src="{{ $file->webp_url }}" alt="{{ $file->name }} (WebP)" class="img-thumbnail" style="max-height: 150px;">
                    </div>
                    @endif
                @elseif($file->isVideo())
                    <video controls class="w-100" style="max-height: 600px;">
                        <source src="{{ $file->url }}" type="{{ $file->mime_type }}">
                        Your browser does not support the video tag.
                    </video>
                @else
                    <div class="py-5">
                        <i class="bi bi-file-earmark fs-1 text-muted mb-3"></i>
                        <h4>{{ $file->name }}</h4>
                        <p class="text-muted">Preview not available for this file type</p>
                        <a href="{{ route('file.download', $file->slug) }}" class="btn btn-primary">
                            <i class="bi bi-download me-2"></i>
                            Download to View
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- File Information -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">File Information</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Original Name:</strong></td>
                        <td>{{ $file->name }}</td>
                    </tr>
                    @if($file->display_name && $file->display_name !== $file->name)
                    <tr>
                        <td><strong>Display Name:</strong></td>
                        <td>{{ $file->display_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Size:</strong></td>
                        <td>{{ $file->human_size }}</td>
                    </tr>
                    <tr>
                        <td><strong>Type:</strong></td>
                        <td>
                            <span class="badge bg-secondary">{{ strtoupper($file->extension) }}</span>
                            <br><small class="text-muted">{{ $file->mime_type }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Visibility:</strong></td>
                        <td>
                            @if($file->is_public)
                                <span class="badge bg-success">Public</span>
                            @else
                                <span class="badge bg-warning">Private</span>
                            @endif
                        </td>
                    </tr>
                    @if($file->folder)
                    <tr>
                        <td><strong>Folder:</strong></td>
                        <td><span class="badge bg-info">{{ $file->folder }}</span></td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Uploaded:</strong></td>
                        <td>
                            {{ $file->created_at->format('M d, Y H:i') }}
                            <br><small class="text-muted">{{ $file->created_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Downloads:</strong></td>
                        <td>{{ number_format($file->download_count) }}</td>
                    </tr>
                    @if($file->last_accessed_at)
                    <tr>
                        <td><strong>Last Accessed:</strong></td>
                        <td>
                            {{ $file->last_accessed_at->format('M d, Y H:i') }}
                            <br><small class="text-muted">{{ $file->last_accessed_at->diffForHumans() }}</small>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Uploaded by:</strong></td>
                        <td>{{ $file->user->name }}</td>
                    </tr>
                </table>
                
                @if($file->metadata && count($file->metadata) > 0)
                <hr>
                <h6>Metadata</h6>
                <table class="table table-sm">
                    @foreach($file->metadata as $key => $value)
                    <tr>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                    </tr>
                    @endforeach
                </table>
                @endif
            </div>
        </div>
        
        <!-- File URLs -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">File URLs</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label"><strong>Direct URL:</strong></label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $file->cdn_url }}" readonly id="directUrl">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $file->cdn_url }}')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label"><strong>Download URL:</strong></label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ route('file.download', $file->slug) }}" readonly id="downloadUrl">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ route('file.download', $file->slug) }}')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                
                @if($file->thumbnail_path)
                <div class="mb-3">
                    <label class="form-label"><strong>Thumbnail URL:</strong></label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $file->thumbnail_url }}" readonly id="thumbnailUrl">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $file->thumbnail_url }}')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                @endif
                
                @if($file->webp_path)
                <div class="mb-3">
                    <label class="form-label"><strong>WebP URL:</strong></label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $file->webp_url }}" readonly id="webpUrl">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $file->webp_url }}')">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Actions -->
        @if(auth()->user()->canManageAllFiles() || $file->user_id === auth()->id())
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Actions</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-warning" onclick="editFile({{ $file->id }})">
                        <i class="bi bi-pencil me-2"></i>
                        Edit File
                    </button>
                    @if(auth()->user()->canDeleteFiles() || $file->user_id === auth()->id())
                    <button type="button" class="btn btn-outline-danger" onclick="deleteFile({{ $file->id }})">
                        <i class="bi bi-trash me-2"></i>
                        Delete File
                    </button>
                    @endif
                    @if($file->isImage() && !$file->is_optimized)
                    <button type="button" class="btn btn-outline-info" onclick="optimizeFile({{ $file->id }})">
                        <i class="bi bi-gear me-2"></i>
                        Optimize File
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Success Alert Template -->
<div id="successAlert" class="alert alert-success alert-dismissible fade" role="alert" style="display: none;">
    <span id="successMessage"></span>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showSuccess('URL copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showSuccess('URL copied to clipboard!');
    });
}

function showSuccess(message) {
    const alert = document.getElementById('successAlert');
    const messageSpan = document.getElementById('successMessage');
    messageSpan.textContent = message;
    alert.style.display = 'block';
    alert.classList.add('show');
    
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.style.display = 'none';
        }, 150);
    }, 3000);
}

function editFile(fileId) {
    // Redirect to files page with edit modal
    window.location.href = '{{ route("dashboard.files") }}?edit=' + fileId;
}

function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        fetch(`/api/files/${fileId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('File deleted successfully!');
                setTimeout(() => {
                    window.location.href = '{{ route("dashboard.files") }}';
                }, 1500);
            } else {
                alert('Failed to delete file: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

function optimizeFile(fileId) {
    if (confirm('Optimize this file? This will generate thumbnails and WebP versions.')) {
        // This would require additional API endpoint for optimization
        alert('Optimization feature coming soon!');
    }
}
</script>
@endpush