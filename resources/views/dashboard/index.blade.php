@extends('layouts.dashboard')

@section('title', 'Dashboard Overview')

@php
if (! function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) { $bytes /= 1024; }
        return round($bytes, $precision).' '.$units[$i];
    }
}
@endphp

@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    @foreach([
        ['label' => 'Total Files', 'value' => number_format($userStats['total_files'])],
        ['label' => 'Storage Used', 'value' => formatBytes($userStats['total_size'])],
        ['label' => 'Public Files', 'value' => number_format($userStats['public_files'])],
        ['label' => 'Private Files', 'value' => number_format($userStats['private_files'])],
    ] as $stat)
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm">
        <p class="text-3xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

@if($storageStats)
<div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm mb-6">
    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
        <h2 class="font-semibold">System Statistics</h2>
    </div>
    <div class="p-5 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 text-center">
        @foreach([
            ['label' => 'Total Files', 'value' => number_format($storageStats['total_files']), 'color' => 'text-indigo-600 dark:text-indigo-400'],
            ['label' => 'Total Size', 'value' => $storageStats['total_size_human'], 'color' => 'text-sky-600 dark:text-sky-400'],
            ['label' => 'Images', 'value' => number_format($storageStats['image_files']), 'color' => 'text-emerald-600 dark:text-emerald-400'],
            ['label' => 'Videos', 'value' => number_format($storageStats['video_files']), 'color' => 'text-amber-600 dark:text-amber-400'],
            ['label' => 'Documents', 'value' => number_format($storageStats['document_files']), 'color' => 'text-rose-600 dark:text-rose-400'],
            ['label' => 'Others', 'value' => number_format($storageStats['other_files']), 'color' => 'text-slate-600 dark:text-slate-400'],
        ] as $stat)
        <div>
            <p class="text-2xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="font-semibold">Recent Files</h2>
            <a href="{{ route('dashboard.files') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View All</a>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-800">
            @forelse($recentFiles as $file)
            <div class="flex items-center gap-3 px-5 py-3">
                @if($file->isImage() && $file->thumbnail_path)
                <img src="{{ $file->thumbnail_url }}" alt="" class="w-12 h-12 rounded-lg object-cover shrink-0">
                @else
                <div class="w-12 h-12 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center shrink-0 text-slate-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate">{{ Str::limit($file->display_name ?: $file->name, 40) }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $file->human_size }} · {{ $file->created_at->diffForHumans() }}</p>
                </div>
                <span class="shrink-0 px-2 py-0.5 rounded text-xs font-medium {{ $file->is_public ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300' }}">
                    {{ $file->is_public ? 'Public' : 'Private' }}
                </span>
            </div>
            @empty
            <div class="text-center py-12 text-slate-500 dark:text-slate-400">
                <p class="mb-4">No files uploaded yet</p>
                <a href="{{ route('dashboard.upload') }}" class="inline-flex px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Upload Your First File</a>
            </div>
            @endforelse
        </div>
    </div>

    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="font-semibold">Recent Activities</h2>
            <a href="{{ route('dashboard.activities') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View All</a>
        </div>
        <div class="divide-y divide-slate-200 dark:divide-slate-800 max-h-96 overflow-y-auto">
            @forelse($recentActivities->take(10) as $activity)
            <div class="px-5 py-3">
                <p class="text-sm">
                    <span class="font-medium">{{ $activity->user?->name ?? 'Guest' }}</span>
                    {{ $activity->action }}d
                    @if($activity->file)
                    <span class="font-medium">{{ Str::limit($activity->file->name, 20) }}</span>
                    @else
                    <em>deleted file</em>
                    @endif
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <p class="text-center py-12 text-slate-500 dark:text-slate-400">No recent activities</p>
            @endforelse
        </div>
    </div>
</div>

<div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
        <h2 class="font-semibold">Quick Actions</h2>
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('dashboard.upload') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Upload Files</a>
        <a href="{{ route('dashboard.files') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800">Browse Files</a>
        @can('view dashboard stats')
        <a href="{{ route('dashboard.stats') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800">View Statistics</a>
        @endcan
        <a href="{{ route('dashboard.activities') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800">View Activities</a>
    </div>
</div>
@endsection
