@extends('layouts.dashboard')

@section('title', 'File Management')

@section('toolbar')
<div class="btn-group" role="group">
    <a href="{{ route('dashboard.upload') }}" class="btn btn-primary">
        <i class="bi bi-cloud-upload me-2"></i>
        Upload Files
    </a>
    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
        <i class="bi bi-funnel me-2"></i>
        Filters
    </button>
</div>
@endsection

@section('content')
<!-- Search and Filter Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard.files') }}" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" placeholder="Search files..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="type">
                    <option value="">All Types</option>
                    <option value="images" {{ request('type') === 'images' ? 'selected' : '' }}>Images</option>
                    <option value="videos" {{ request('type') === 'videos' ? 'selected' : '' }}>Videos</option>
                    <option value="documents" {{ request('type') === 'documents' ? 'selected' : '' }}>Documents</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="folder">
                    <option value="">All Folders</option>
                    @foreach($folders as $folder)
                        <option value="{{ $folder }}" {{ request('folder') === $folder ? 'selected' : '' }}>{{ $folder }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="visibility">
                    <option value="">All Visibility</option>
                    <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                    <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="btn-group w-100" role="group">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                    <a href="{{ route('dashboard.files') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Files Grid -->
@if($files->count() > 0)
    <div class="row">
        @foreach($files as $file)
        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
            <div class="card file-card h-100">
                <div class="card-body p-3">
                    <!-- File Preview -->
                    <div class="text-center mb-3">
                        @if($file->isImage())
                            @if($file->thumbnail_path)
                                <img src="{{ $file->thumbnail_url }}" alt="{{ $file->name }}" class="img-fluid rounded" style="max-height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                    <i class="bi bi-image fs-1 text-muted"></i>
                                </div>
                            @endif
                        @elseif($file->isVideo())
                            <div class="bg-dark rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                <i class="bi bi-play-circle fs-1 text-white"></i>
                            </div>
                        @elseif($file->isDocument())
                            <div class="bg-primary rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                <i class="bi bi-file-text fs-1 text-white"></i>
                            </div>
                        @else
                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                                <i class="bi bi-file fs-1 text-white"></i>
                            </div>
                        @endif
                    </div>
                    
                    <!-- File Info -->
                    <div class="mb-2">
                        <h6 class="card-title mb-1" title="{{ $file->display_name ?: $file->name }}">
                            {{ Str::limit($file->display_name ?: $file->name, 25) }}
                        </h6>
                        <div class="small text-muted">
                            {{ $file->human_size }} • {{ $file->created_at->diffForHumans() }}
                        </div>
                    </div>
                    
                    <!-- File Badges -->
                    <div class="mb-3">
                        @if($file->is_public)
                            <span class="badge bg-success">Public</span>
                        @else
                            <span class="badge bg-warning">Private</span>
                        @endif
                        
                        @if($file->folder)
                            <span class="badge bg-secondary">{{ $file->folder }}</span>
                        @endif
                        
                        @if($file->is_optimized)
                            <span class="badge bg-info">Optimized</span>
                        @endif
                        
                        <div class="small text-muted mt-1">
                            <i class="bi bi-download me-1"></i>{{ number_format($file->download_count) }} downloads
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('file.show', $file->slug) }}" class="btn btn-sm btn-outline-primary" title="View">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('file.download', $file->slug) }}" class="btn btn-sm btn-outline-success" title="Download">
                            <i class="bi bi-download"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-warning" title="Edit" onclick="editFile({{ $file->id }})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        @if(auth()->user()->canDeleteFiles() || $file->user_id === auth()->id())
                        <button type="button" class="btn btn-sm btn-outline-danger" title="Delete" onclick="deleteFile({{ $file->id }})">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                    </div>
                </div>
                
                <!-- File Owner (for admins) -->
                @if(auth()->user()->canManageAllFiles() && $file->user)
                <div class="card-footer py-2">
                    <small class="text-muted">
                        <i class="bi bi-person me-1"></i>{{ $file->user->name }}
                    </small>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $files->appends(request()->query())->links() }}
    </div>
@else
    <div class="text-center py-5">
        <i class="bi bi-inbox fs-1 text-muted"></i>
        <h4 class="mt-3">No Files Found</h4>
        <p class="text-muted">{{ request()->hasAny(['search', 'type', 'folder', 'visibility']) ? 'Try adjusting your filters or search terms.' : 'Upload your first file to get started.' }}</p>
        @if(!request()->hasAny(['search', 'type', 'folder', 'visibility']))
            <a href="{{ route('dashboard.upload') }}" class="btn btn-primary">
                <i class="bi bi-cloud-upload me-2"></i>
                Upload Files
            </a>
        @endif
    </div>
@endif

<!-- Edit File Modal -->
<div class="modal fade" id="editFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editFileForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDisplayName" class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="editDisplayName" name="display_name">
                    </div>
                    <div class="mb-3">
                        <label for="editFolder" class="form-label">Folder</label>
                        <input type="text" class="form-control" id="editFolder" name="folder" placeholder="Optional">
                    </div>
                    <div class="mb-3">
                        <label for="editVisibility" class="form-label">Visibility</label>
                        <select class="form-select" id="editVisibility" name="is_public">
                            <option value="1">Public</option>
                            <option value="0">Private</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this file? This action cannot be undone.</p>
                <div id="deleteFileName" class="fw-bold"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete File</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentFileId = null;

function editFile(fileId) {
    currentFileId = fileId;
    
    // Fetch file data
    fetch(`/api/files/${fileId}`, {
        headers: {
            'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const file = data.data;
            document.getElementById('editDisplayName').value = file.display_name || '';
            document.getElementById('editFolder').value = file.folder || '';
            document.getElementById('editVisibility').value = file.is_public ? '1' : '0';
            
            new bootstrap.Modal(document.getElementById('editFileModal')).show();
        } else {
            alert('Failed to load file data: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

function deleteFile(fileId) {
    currentFileId = fileId;
    
    // Fetch file data for confirmation
    fetch(`/api/files/${fileId}`, {
        headers: {
            'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('deleteFileName').textContent = data.data.name;
            new bootstrap.Modal(document.getElementById('deleteFileModal')).show();
        } else {
            alert('Failed to load file data: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

// Edit form submission
document.getElementById('editFileForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {
        display_name: formData.get('display_name'),
        folder: formData.get('folder'),
        is_public: formData.get('is_public') === '1'
    };
    
    fetch(`/api/files/${currentFileId}`, {
        method: 'PUT',
        headers: {
            'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editFileModal')).hide();
            location.reload(); // Refresh page to show changes
        } else {
            alert('Failed to update file: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

// Delete confirmation
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    fetch(`/api/files/${currentFileId}`, {
        method: 'DELETE',
        headers: {
            'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || ''),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteFileModal')).hide();
            location.reload(); // Refresh page to show changes
        } else {
            alert('Failed to delete file: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});
</script>
@endpush