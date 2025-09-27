@extends('layouts.dashboard')

@section('title', 'Statistics')

@section('content')
<div class="row">
    <!-- Overview Cards -->
    <div class="col-md-3 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Files</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_files']) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-file-earmark fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Storage</h6>
                        <h3 class="mb-0">{{ $stats['total_size_human'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-hdd fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Total Downloads</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_downloads']) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-download fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title">Active Users</h6>
                        <h3 class="mb-0">{{ number_format($stats['active_users']) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- File Types Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pie-chart me-2"></i>
                    File Types Distribution
                </h5>
            </div>
            <div class="card-body">
                <canvas id="fileTypesChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Upload Trends Chart -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    Upload Trends (Last 30 Days)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="uploadTrendsChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Files -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-trophy me-2"></i>
                    Most Downloaded Files
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>File</th>
                                <th>Downloads</th>
                                <th>Size</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['top_files'] as $file)
                                <tr>
                                    <td>
                                        <i class="bi bi-file-earmark me-2"></i>
                                        {{ Str::limit($file->original_name, 30) }}
                                    </td>
                                    <td>{{ number_format($file->downloads) }}</td>
                                    <td>{{ $file->human_size }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No files found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Usage -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-hdd-stack me-2"></i>
                    Storage Usage by User
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Files</th>
                                <th>Storage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['user_storage'] as $user)
                                <tr>
                                    <td>
                                        <i class="bi bi-person me-2"></i>
                                        {{ $user->name }}
                                    </td>
                                    <td>{{ number_format($user->files_count) }}</td>
                                    <td>{{ $user->total_size_human }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if stats data exists
    @if(isset($stats))
    // File Types Chart
    const fileTypesCtx = document.getElementById('fileTypesChart');
    if (fileTypesCtx) {
        const fileTypesChart = new Chart(fileTypesCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($stats['file_types'] ?? [])) !!},
                datasets: [{
                    data: {!! json_encode(array_values($stats['file_types'] ?? [])) !!},
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
                        '#FF9F40'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Upload Trends Chart
    const uploadTrendsCtx = document.getElementById('uploadTrendsChart');
    if (uploadTrendsCtx) {
        const uploadTrendsChart = new Chart(uploadTrendsCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($stats['upload_trends'] ?? [])) !!},
                datasets: [{
                    label: 'Files Uploaded',
                    data: {!! json_encode(array_values($stats['upload_trends'] ?? [])) !!},
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endpush
@endsection