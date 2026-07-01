@extends('layouts.dashboard')

@section('title', 'File Management')

@section('toolbar')
<div class="flex items-center gap-2">
    <a href="{{ route('dashboard.upload') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
        Upload Files
    </a>
    <a href="#file-filters" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
        Filters
    </a>
</div>
@endsection

@section('content')
<div id="fileManager" x-data="{ editOpen: false, deleteOpen: false }">
    <!-- Search and Filter Bar -->
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm mb-6" id="file-filters">
        <div class="p-4">
            <form method="GET" action="{{ route('dashboard.files') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <div class="lg:col-span-4">
                    <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" name="search" placeholder="Search files..." value="{{ request('search') }}">
                </div>
                <div class="lg:col-span-2">
                    <select class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" name="type">
                        <option value="">All Types</option>
                        <option value="images" {{ request('type') === 'images' ? 'selected' : '' }}>Images</option>
                        <option value="videos" {{ request('type') === 'videos' ? 'selected' : '' }}>Videos</option>
                        <option value="documents" {{ request('type') === 'documents' ? 'selected' : '' }}>Documents</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <select class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" name="folder">
                        <option value="">All Folders</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder }}" {{ request('folder') === $folder ? 'selected' : '' }}>{{ $folder }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <select class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" name="visibility">
                        <option value="">All Visibility</option>
                        <option value="public" {{ request('visibility') === 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ request('visibility') === 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>
                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 px-4 py-2 rounded-lg border border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                    <a href="{{ route('dashboard.files') }}" class="inline-flex flex-1 items-center justify-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Files Grid -->
    @if($files->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($files as $file)
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm flex flex-col">
                <div class="p-3 flex-1">
                    <!-- File Preview -->
                    <div class="text-center mb-3">
                        @if($file->isImage())
                            @if($file->thumbnail_path)
                                <img src="{{ $file->thumbnail_url }}" alt="{{ $file->name }}" class="mx-auto rounded-lg max-h-[120px] object-cover">
                            @else
                                <div class="bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center h-[120px]">
                                    <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                            @endif
                        @elseif($file->isVideo())
                            <div class="bg-slate-800 rounded-lg flex items-center justify-center h-[120px]">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        @elseif($file->isDocument())
                            <div class="bg-indigo-600 rounded-lg flex items-center justify-center h-[120px]">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        @else
                            <div class="bg-slate-500 rounded-lg flex items-center justify-center h-[120px]">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        @endif
                    </div>

                    <!-- File Info -->
                    <div class="mb-2">
                        <h6 class="font-medium text-sm mb-1 truncate" title="{{ $file->display_name ?: $file->name }}">
                            {{ Str::limit($file->display_name ?: $file->name, 25) }}
                        </h6>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $file->human_size }} &bull; {{ $file->created_at->diffForHumans() }}
                        </div>
                    </div>

                    <!-- File Badges -->
                    <div class="mb-3">
                        @if($file->is_public)
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Public</span>
                        @else
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Private</span>
                        @endif

                        @if($file->folder)
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ $file->folder }}</span>
                        @endif

                        @if($file->is_optimized)
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300">Optimized</span>
                        @endif

                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            {{ number_format($file->download_count) }} downloads
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-1">
                        <a href="{{ route('file.show', $file->slug) }}" class="inline-flex flex-1 items-center justify-center w-8 h-8 rounded-lg border border-indigo-300 dark:border-indigo-600 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/50" title="View">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('file.download', $file->slug) }}" class="inline-flex flex-1 items-center justify-center w-8 h-8 rounded-lg border border-emerald-300 dark:border-emerald-600 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/50" title="Download">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        </a>
                        <button type="button" class="inline-flex flex-1 items-center justify-center w-8 h-8 rounded-lg border border-amber-300 dark:border-amber-600 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50" title="Edit" onclick="editFile({{ $file->id }})">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        @if(auth()->user()->canDeleteFiles() || $file->user_id === auth()->id())
                        <button type="button" class="inline-flex flex-1 items-center justify-center w-8 h-8 rounded-lg border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50" title="Delete" onclick="deleteFile({{ $file->id }})">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                        @endif
                    </div>
                </div>

                @if(auth()->user()->canManageAllFiles() && $file->user)
                <div class="px-3 py-2 border-t border-slate-200 dark:border-slate-800">
                    <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        {{ $file->user->name }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="flex justify-center mt-6">
            {{ $files->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <h4 class="mt-4 text-lg font-semibold">No Files Found</h4>
            <p class="text-slate-500 dark:text-slate-400 mt-1">{{ request()->hasAny(['search', 'type', 'folder', 'visibility']) ? 'Try adjusting your filters or search terms.' : 'Upload your first file to get started.' }}</p>
            @if(!request()->hasAny(['search', 'type', 'folder', 'visibility']))
                <a href="{{ route('dashboard.upload') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium mt-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Upload Files
                </a>
            @endif
        </div>
    @endif

    <!-- Edit File Modal -->
    <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="editOpen = false">
        <div x-show="editOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/50" @click="editOpen = false"></div>
        <div x-show="editOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                <h5 class="font-semibold">Edit File</h5>
                <button type="button" @click="editOpen = false" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="editFileForm">
                <div class="p-5 space-y-4">
                    <div>
                        <label for="editDisplayName" class="block text-sm font-medium mb-1">Display Name</label>
                        <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" id="editDisplayName" name="display_name">
                    </div>
                    <div>
                        <label for="editFolder" class="block text-sm font-medium mb-1">Folder</label>
                        <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" id="editFolder" name="folder" placeholder="Optional">
                    </div>
                    <div>
                        <label for="editVisibility" class="block text-sm font-medium mb-1">Visibility</label>
                        <select class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" id="editVisibility" name="is_public">
                            <option value="1">Public</option>
                            <option value="0">Private</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                    <button type="button" @click="editOpen = false" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">Cancel</button>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteOpen = false">
        <div x-show="deleteOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/50" @click="deleteOpen = false"></div>
        <div x-show="deleteOpen" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="relative w-full max-w-md rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xl">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                <h5 class="font-semibold">Delete File</h5>
                <button type="button" @click="deleteOpen = false" class="inline-flex items-center justify-center w-8 h-8 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5">
                <p class="text-slate-600 dark:text-slate-300">Are you sure you want to delete this file? This action cannot be undone.</p>
                <div id="deleteFileName" class="font-semibold mt-2"></div>
            </div>
            <div class="flex justify-end gap-2 px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                <button type="button" @click="deleteOpen = false" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">Cancel</button>
                <button type="button" id="confirmDeleteBtn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 text-sm font-medium">Delete File</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentFileId = null;

function fileManager() {
    return Alpine.$data(document.getElementById('fileManager'));
}

function editFile(fileId) {
    currentFileId = fileId;

    apiFetch(`/api/files/${fileId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const file = data.data;
            document.getElementById('editDisplayName').value = file.display_name || '';
            document.getElementById('editFolder').value = file.folder || '';
            document.getElementById('editVisibility').value = file.is_public ? '1' : '0';

            fileManager().editOpen = true;
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

    apiFetch(`/api/files/${fileId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('deleteFileName').textContent = data.data.name;
            fileManager().deleteOpen = true;
        } else {
            alert('Failed to load file data: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
}

document.getElementById('editFileForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {
        display_name: formData.get('display_name'),
        folder: formData.get('folder'),
        is_public: formData.get('is_public') === '1'
    };

    apiFetch(`/api/files/${currentFileId}`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fileManager().editOpen = false;
            location.reload();
        } else {
            alert('Failed to update file: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    apiFetch(`/api/files/${currentFileId}`, { method: 'DELETE' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            fileManager().deleteOpen = false;
            location.reload();
        } else {
            alert('Failed to delete file: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error: ' + error.message);
    });
});

const editParam = new URLSearchParams(window.location.search).get('edit');
if (editParam) {
    editFile(editParam);
}
</script>
@endpush
