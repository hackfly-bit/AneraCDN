@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')

@php
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
@endphp

@section('content')
<div class="row">
    <!-- User Statistics -->
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-files fs-1"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">{{ number_format($userStats['total_files']) }}</div>
                        <div class="small">Total Files</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-hdd fs-1"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">{{ formatBytes($userStats['total_size']) }}</div>
                        <div class="small">Storage Used</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-globe fs-1"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">{{ number_format($userStats['public_files']) }}</div>
                        <div class="small">Public Files</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4">
        <div class="card stats-card h-100" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="bi bi-lock fs-1"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="fw-bold fs-4">{{ number_format($userStats['private_files']) }}</div>
                        <div class="small">Private Files</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($storageStats)
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">System Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        <div class="fs-4 fw-bold text-primary">{{ number_format($storageStats['total_files']) }}</div>
                        <div class="small text-muted">Total Files</div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="fs-4 fw-bold text-info">{{ $storageStats['total_size_human'] }}</div>
                        <div class="small text-muted">Total Size</div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="fs-4 fw-bold text-success">{{ number_format($storageStats['image_files']) }}</div>
                        <div class="small text-muted">Images</div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="fs-4 fw-bold text-warning">{{ number_format($storageStats['video_files']) }}</div>
                        <div class="small text-muted">Videos</div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="fs-4 fw-bold text-danger">{{ number_format($storageStats['document_files']) }}</div>
                        <div class="small text-muted">Documents</div>
                    </div>
                    <div class="col-md-2 text-center">
                        <div class="fs-4 fw-bold text-secondary">{{ number_format($storageStats['other_files']) }}</div>
                        <div class="small text-muted">Others</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <!-- Recent Files -->
    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Files</h5>
                <a href="{{ route('dashboard.files') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentFiles->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentFiles as $file)
                        <div class="list-group-item d-flex align-items-center">
                            <div class="flex-shrink-0 me-3">
                                @if($file->isImage() && $file->thumbnail_path)
                                    <img src="{{ $file->thumbnail_url }}" alt="{{ $file->name }}" class="file-thumbnail">
                                @else
                                    <div class="file-thumbnail bg-light d-flex align-items-center justify-content-center">
                                        @if($file->isImage())
                                            <i class="bi bi-image fs-4 text-muted"></i>
                                        @elseif($file->isVideo())
                                            <i class="bi bi-play-circle fs-4 text-muted"></i>
                                        @elseif($file->isDocument())
                                            <i class="bi bi-file-text fs-4 text-muted"></i>
                                        @else
                                            <i class="bi bi-file fs-4 text-muted"></i>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">{{ Str::limit($file->display_name ?: $file->name, 40) }}</div>
                                <div class="small text-muted">
                                    {{ $file->human_size }} • {{ $file->created_at->diffForHumans() }}
                                    @if($file->folder)
                                        • <span class="badge bg-secondary">{{ $file->folder }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                @if($file->is_public)
                                    <span class="badge bg-success">Public</span>
                                @else
                                    <span class="badge bg-warning">Private</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No files uploaded yet</p>
                        <a href="{{ route('dashboard.upload') }}" class="btn btn-primary">Upload Your First File</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Recent Activities -->
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Recent Activities</h5>
                <a href="{{ route('dashboard.activities') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentActivities->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentActivities->take(10) as $activity)
                        <div class="list-group-item px-0">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-2">
                                    @switch($activity->action)
                                        @case('upload')
                                            <i class="bi bi-cloud-upload text-success"></i>
                                            @break
                                        @case('download')
                                            <i class="bi bi-download text-primary"></i>
                                            @break
                                        @case('delete')
                                            <i class="bi bi-trash text-danger"></i>
                                            @break
                                        @case('update')
                                            <i class="bi bi-pencil text-warning"></i>
                                            @break
                                        @case('view')
                                            <i class="bi bi-eye text-info"></i>
                                            @break
                                        @default
                                            <i class="bi bi-activity text-muted"></i>
                                    @endswitch
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small">
                                        <strong>{{ $activity->user ? $activity->user->name : 'Guest' }}</strong>
                                        {{ $activity->action }}d
                                        @if($activity->file)
                                            <strong>{{ Str::limit($activity->file->name, 20) }}</strong>
                                        @else
                                            <em>deleted file</em>
                                        @endif
                                    </div>
                                    <div class="text-muted small">{{ $activity->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-activity fs-1 text-muted"></i>
                        <p class="text-muted mt-2">No recent activities</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('dashboard.upload') }}" class="btn btn-primary w-100">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Upload Files
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('dashboard.files') }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-folder me-2"></i>
                            Browse Files
                        </a>
                    </div>
                    @can('view dashboard stats')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('dashboard.stats') }}" class="btn btn-outline-info w-100">
                            <i class="bi bi-bar-chart me-2"></i>
                            View Statistics
                        </a>
                    </div>
                    @endcan
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('dashboard.activities') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-activity me-2"></i>
                            View Activities
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
@endphp