@extends('layouts.dashboard')

@section('title', 'Activities')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-activity me-2"></i>
                        File Activities
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                            <i class="bi bi-funnel me-1"></i>
                            Filters
                        </button>
                        <a href="{{ route('dashboard.activities') }}" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-arrow-clockwise me-1"></i>
                            Refresh
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="collapse {{ request()->hasAny(['user', 'action', 'date_from', 'date_to']) ? 'show' : '' }}" id="filtersCollapse">
                <div class="card-body border-bottom">
                    <form method="GET" action="{{ route('dashboard.activities') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="user" class="form-label">User</label>
                                <select name="user" id="user" class="form-select">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ request('user') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="action" class="form-label">Action</label>
                                <select name="action" id="action" class="form-select">
                                    <option value="">All Actions</option>
                                    @foreach($actions as $action)
                                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                            {{ ucfirst($action) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label">From Date</label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label">To Date</label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i>
                                        Filter
                                    </button>
                                    <a href="{{ route('dashboard.activities') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                @if($activities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>File</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activities as $activity)
                                    <tr>
                                        <td>
                                            <small class="text-muted">
                                                {{ $activity->created_at->format('M d, Y') }}<br>
                                                {{ $activity->created_at->format('H:i:s') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle me-2 text-muted"></i>
                                                <div>
                                                    <div class="fw-medium">{{ $activity->user->name }}</div>
                                                    <small class="text-muted">{{ $activity->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $actionColors = [
                                                    'upload' => 'success',
                                                    'download' => 'info',
                                                    'delete' => 'danger',
                                                    'view' => 'secondary',
                                                    'share' => 'warning'
                                                ];
                                                $color = $actionColors[$activity->action] ?? 'primary';
                                            @endphp
                                            <span class="badge bg-{{ $color }}">
                                                @switch($activity->action)
                                                    @case('upload')
                                                        <i class="bi bi-cloud-upload me-1"></i>
                                                        @break
                                                    @case('download')
                                                        <i class="bi bi-download me-1"></i>
                                                        @break
                                                    @case('delete')
                                                        <i class="bi bi-trash me-1"></i>
                                                        @break
                                                    @case('view')
                                                        <i class="bi bi-eye me-1"></i>
                                                        @break
                                                    @case('share')
                                                        <i class="bi bi-share me-1"></i>
                                                        @break
                                                    @default
                                                        <i class="bi bi-activity me-1"></i>
                                                @endswitch
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($activity->file)
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $extension = strtolower(pathinfo($activity->file->original_name, PATHINFO_EXTENSION));
                                                        $iconClass = match($extension) {
                                                            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'bi-image',
                                                            'pdf' => 'bi-file-pdf',
                                                            'doc', 'docx' => 'bi-file-word',
                                                            'xls', 'xlsx' => 'bi-file-excel',
                                                            'ppt', 'pptx' => 'bi-file-ppt',
                                                            'zip', 'rar', '7z' => 'bi-file-zip',
                                                            'mp4', 'avi', 'mov' => 'bi-file-play',
                                                            'mp3', 'wav', 'flac' => 'bi-file-music',
                                                            default => 'bi-file-earmark'
                                                        };
                                                    @endphp
                                                    <i class="{{ $iconClass }} me-2 text-muted"></i>
                                                    <div>
                                                        <div class="fw-medium">{{ Str::limit($activity->file->original_name, 30) }}</div>
                                                        <small class="text-muted">{{ $activity->file->human_size }}</small>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">File deleted</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($activity->ip_address)
                                                <small class="text-muted">
                                                    <i class="bi bi-geo-alt me-1"></i>
                                                    {{ $activity->ip_address }}
                                                </small>
                                            @endif
                                            @if($activity->user_agent)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="bi bi-browser-chrome me-1"></i>
                                                    {{ Str::limit($activity->user_agent, 50) }}
                                                </small>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <div class="text-muted">
                            Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} activities
                        </div>
                        {{ $activities->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-activity fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No Activities Found</h5>
                        <p class="text-muted">No file activities match your current filters.</p>
                        @if(request()->hasAny(['user', 'action', 'date_from', 'date_to']))
                            <a href="{{ route('dashboard.activities') }}" class="btn btn-outline-primary">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Clear Filters
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const filterForm = document.querySelector('form[action="{{ route('dashboard.activities') }}"]');
    const filterInputs = filterForm.querySelectorAll('select, input');
    
    // Auto-submit form when filters change
    filterInputs.forEach(input => {
        if (input.type !== 'submit') {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });
    
    // Clear filters
    const clearFiltersBtn = document.querySelector('a[href="{{ route('dashboard.activities') }}"].btn-outline-secondary');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function(e) {
            e.preventDefault();
            filterInputs.forEach(input => {
                if (input.tagName === 'SELECT') {
                    input.selectedIndex = 0;
                } else if (input.type !== 'submit') {
                    input.value = '';
                }
            });
            filterForm.submit();
        });
    }
    
    // Refresh activities
    const refreshBtn = document.querySelector('a[href="{{ route('dashboard.activities') }}"].btn-outline-primary');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }
    
    // Format timestamps for better readability
    const timestamps = document.querySelectorAll('td small.text-muted');
    timestamps.forEach(timestamp => {
        const timeText = timestamp.textContent.trim();
        if (timeText.includes(':')) {
            const date = new Date(timestamp.parentElement.querySelector('br').previousSibling.textContent + ' ' + timeText);
            if (!isNaN(date.getTime())) {
                timestamp.setAttribute('title', date.toLocaleString());
            }
        }
    });
    
    // Add hover effects for activity rows
    const activityRows = document.querySelectorAll('tbody tr');
    activityRows.forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'rgba(0,123,255,0.1)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
    
    // Filter collapse state management
    const filtersCollapse = document.getElementById('filtersCollapse');
    const filterToggleBtn = document.querySelector('[data-bs-target="#filtersCollapse"]');
    
    if (filtersCollapse && filterToggleBtn) {
        filtersCollapse.addEventListener('shown.bs.collapse', function() {
            localStorage.setItem('activitiesFiltersOpen', 'true');
        });
        
        filtersCollapse.addEventListener('hidden.bs.collapse', function() {
            localStorage.setItem('activitiesFiltersOpen', 'false');
        });
        
        // Restore filter state on page load
        const filtersOpen = localStorage.getItem('activitiesFiltersOpen');
        if (filtersOpen === 'true' && !filtersCollapse.classList.contains('show')) {
            filterToggleBtn.click();
        }
    }
});
</script>
@endpush