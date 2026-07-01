@extends('layouts.dashboard')

@section('title', 'Activities')

@section('content')
<div x-data="{
    filtersOpen: localStorage.getItem('activitiesFiltersOpen') === 'true' || {{ request()->hasAny(['user', 'action', 'date_from', 'date_to']) ? 'true' : 'false' }}
}" x-init="$watch('filtersOpen', v => localStorage.setItem('activitiesFiltersOpen', v))">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h5 class="font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    File Activities
                </h5>
                <div class="flex gap-2">
                    <button type="button" @click="filtersOpen = !filtersOpen" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filters
                    </button>
                    <a href="{{ route('dashboard.activities') }}" id="refreshActivities" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Refresh
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div x-show="filtersOpen" x-transition id="filtersCollapse" class="border-b border-slate-200 dark:border-slate-800">
            <div class="p-5">
                <form method="GET" action="{{ route('dashboard.activities') }}" id="activitiesFilterForm">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                        <div class="lg:col-span-3">
                            <label for="user_id" class="block text-sm font-medium mb-1">User</label>
                            <select name="user_id" id="user_id" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-3">
                            <label for="action" class="block text-sm font-medium mb-1">Action</label>
                            <select name="action" id="action" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100">
                                <option value="">All Actions</option>
                                @foreach($actions as $action)
                                    <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                        {{ ucfirst($action) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-2">
                            <label for="date_from" class="block text-sm font-medium mb-1">From Date</label>
                            <input type="date" name="date_from" id="date_from" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" value="{{ request('date_from') }}">
                        </div>
                        <div class="lg:col-span-2">
                            <label for="date_to" class="block text-sm font-medium mb-1">To Date</label>
                            <input type="date" name="date_to" id="date_to" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" value="{{ request('date_to') }}">
                        </div>
                        <div class="lg:col-span-2 flex items-end gap-2">
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Filter
                            </button>
                            <a href="{{ route('dashboard.activities') }}" id="clearFiltersBtn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="p-5">
            @if($activities->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800">
                                <th class="text-left py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Time</th>
                                <th class="text-left py-3 px-2 font-medium text-slate-500 dark:text-slate-400">User</th>
                                <th class="text-left py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Action</th>
                                <th class="text-left py-3 px-2 font-medium text-slate-500 dark:text-slate-400">File</th>
                                <th class="text-left py-3 px-2 font-medium text-slate-500 dark:text-slate-400">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            @foreach($activities as $activity)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                    <td class="py-3 px-2">
                                        <span class="text-xs text-slate-500 dark:text-slate-400 activity-timestamp" title="">
                                            {{ $activity->created_at->format('M d, Y') }}<br>
                                            {{ $activity->created_at->format('H:i:s') }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-8 h-8 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <div>
                                                <div class="font-medium">{{ $activity->user->name }}</div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 px-2">
                                        @php
                                            $actionStyles = [
                                                'upload' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300',
                                                'download' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300',
                                                'delete' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                                'view' => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
                                                'share' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300',
                                            ];
                                            $style = $actionStyles[$activity->action] ?? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300';
                                        @endphp
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium {{ $style }}">
                                            @switch($activity->action)
                                                @case('upload')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                                    @break
                                                @case('download')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                    @break
                                                @case('delete')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    @break
                                                @case('view')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                    @break
                                                @case('share')
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                                    @break
                                                @default
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            @endswitch
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-2">
                                        @if($activity->file)
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <div>
                                                    <div class="font-medium">{{ Str::limit($activity->file->original_name, 30) }}</div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $activity->file->human_size }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-slate-500 dark:text-slate-400 italic">File deleted</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-2">
                                        @if($activity->ip_address)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                {{ $activity->ip_address }}
                                            </div>
                                        @endif
                                        @if($activity->user_agent)
                                            <div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-1">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                                {{ Str::limit($activity->user_agent, 50) }}
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mt-6">
                    <div class="text-sm text-slate-500 dark:text-slate-400">
                        Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} activities
                    </div>
                    {{ $activities->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <h5 class="text-lg font-semibold text-slate-500 dark:text-slate-400 mt-4">No Activities Found</h5>
                    <p class="text-slate-500 dark:text-slate-400 mt-1">No file activities match your current filters.</p>
                    @if(request()->hasAny(['user', 'action', 'date_from', 'date_to']))
                        <a href="{{ route('dashboard.activities') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-sm mt-4">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('activitiesFilterForm');
    const filterInputs = filterForm.querySelectorAll('select, input');

    filterInputs.forEach(input => {
        if (input.type !== 'submit') {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        }
    });

    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
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

    const refreshBtn = document.getElementById('refreshActivities');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            location.reload();
        });
    }

    document.querySelectorAll('.activity-timestamp').forEach(timestamp => {
        const parts = timestamp.innerHTML.trim().split('<br>');
        if (parts.length === 2) {
            const date = new Date(parts[0].trim() + ' ' + parts[1].trim());
            if (!isNaN(date.getTime())) {
                timestamp.setAttribute('title', date.toLocaleString());
            }
        }
    });
});
</script>
@endpush
