@extends('layouts.dashboard')

@section('title', 'Statistics')

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Files', 'value' => number_format($stats['total_files'])],
        ['label' => 'Total Storage', 'value' => $stats['total_size_human']],
        ['label' => 'Total Downloads', 'value' => number_format($stats['total_downloads'])],
        ['label' => 'Active Users', 'value' => number_format($stats['active_users'])],
    ] as $card)
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $card['label'] }}</p>
        <p class="text-3xl font-bold text-slate-900 dark:text-white mt-1">{{ $card['value'] }}</p>
    </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-5">
        <h2 class="font-semibold mb-4">File Types Distribution</h2>
        <div class="h-64"><canvas id="fileTypesChart"></canvas></div>
    </div>
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-5">
        <h2 class="font-semibold mb-4">Upload Trends (Last 30 Days)</h2>
        <div class="h-64"><canvas id="uploadTrendsChart"></canvas></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 font-semibold">Most Downloaded Files</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 dark:text-slate-400"><tr><th class="px-4 py-2">File</th><th class="px-4 py-2">Downloads</th><th class="px-4 py-2">Size</th></tr></thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($stats['top_files'] as $file)
                    <tr><td class="px-4 py-2">{{ Str::limit($file->original_name, 30) }}</td><td class="px-4 py-2">{{ number_format($file->downloads) }}</td><td class="px-4 py-2">{{ $file->human_size }}</td></tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No files found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 font-semibold">Storage Usage by User</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-slate-500 dark:text-slate-400"><tr><th class="px-4 py-2">User</th><th class="px-4 py-2">Files</th><th class="px-4 py-2">Storage</th></tr></thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                    @forelse($stats['user_storage'] as $user)
                    <tr><td class="px-4 py-2">{{ $user->name }}</td><td class="px-4 py-2">{{ number_format($user->files_count) }}</td><td class="px-4 py-2">{{ $user->total_size_human }}</td></tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-8 text-center text-slate-500">No data available</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = () => document.documentElement.classList.contains('dark');
    const chartColors = () => ({
        text: isDark() ? '#94a3b8' : '#64748b',
        grid: isDark() ? '#334155' : '#e2e8f0',
        line: isDark() ? '#818cf8' : '#4f46e5',
        fill: isDark() ? 'rgba(129, 140, 248, 0.15)' : 'rgba(79, 70, 229, 0.1)',
        doughnut: ['#818cf8', '#38bdf8', '#fbbf24', '#34d399', '#c084fc', '#fb7185'],
    });

    let fileTypesChart, uploadTrendsChart;

    function buildCharts() {
        const c = chartColors();
        const fileTypesCtx = document.getElementById('fileTypesChart');
        if (fileTypesCtx) {
            fileTypesChart?.destroy();
            fileTypesChart = new Chart(fileTypesCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($stats['file_types'] ?? [])) !!},
                    datasets: [{ data: {!! json_encode(array_values($stats['file_types'] ?? [])) !!}, backgroundColor: c.doughnut }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { color: c.text } } } }
            });
        }
        const uploadTrendsCtx = document.getElementById('uploadTrendsChart');
        if (uploadTrendsCtx) {
            uploadTrendsChart?.destroy();
            uploadTrendsChart = new Chart(uploadTrendsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode(array_keys($stats['upload_trends'] ?? [])) !!},
                    datasets: [{ label: 'Files Uploaded', data: {!! json_encode(array_values($stats['upload_trends'] ?? [])) !!}, borderColor: c.line, backgroundColor: c.fill, tension: 0.4, fill: true }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    scales: {
                        x: { ticks: { color: c.text }, grid: { color: c.grid } },
                        y: { beginAtZero: true, ticks: { color: c.text }, grid: { color: c.grid } }
                    },
                    plugins: { legend: { display: false } }
                }
            });
        }
    }

    buildCharts();
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
        if ((localStorage.theme || 'system') === 'system') buildCharts();
    });
    new MutationObserver(() => buildCharts()).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
});
</script>
@endpush
