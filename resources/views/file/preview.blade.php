@extends('layouts.dashboard')

@section('title', 'File Preview')

@section('toolbar')
<div class="flex flex-wrap items-center gap-2">
    <a href="{{ route('file.download', $file->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
        Download
    </a>
    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm" onclick="copyToClipboard('{{ $file->cdn_url }}')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        Copy URL
    </button>
    @if($file->webp_path)
    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-sky-300 dark:border-sky-600 text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/50 text-sm" onclick="copyToClipboard('{{ $file->webp_url }}')">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
        Copy WebP URL
    </button>
    @endif
    <a href="{{ route('dashboard.files') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Back to Files
    </a>
</div>
@endsection

@section('content')
<div x-data="{ showSuccess: false, successMessage: '' }">
    <!-- Success Toast -->
    <div x-show="showSuccess" x-cloak x-transition class="fixed top-20 right-4 z-50 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 px-4 py-3 text-emerald-800 dark:text-emerald-200 shadow-lg text-sm" id="successAlert">
        <span x-text="successMessage" id="successMessage"></span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- File Preview -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h5 class="font-semibold truncate">{{ $file->display_name ?: $file->name }}</h5>
                </div>
                <div class="p-5 text-center">
                    @if($file->isImage())
                        <img src="{{ $file->url }}" alt="{{ $file->name }}" class="mx-auto rounded-lg shadow max-h-[600px]">

                        @if($file->webp_path)
                        <div class="mt-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">WebP Version Available:</p>
                            <img src="{{ $file->webp_url }}" alt="{{ $file->name }} (WebP)" class="mx-auto rounded-lg border border-slate-200 dark:border-slate-700 max-h-[150px]">
                        </div>
                        @endif
                    @elseif($file->isVideo())
                        <video controls class="w-full max-h-[600px] rounded-lg">
                            <source src="{{ $file->url }}" type="{{ $file->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div class="py-12">
                            <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <h4 class="mt-4 text-lg font-semibold">{{ $file->name }}</h4>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">Preview not available for this file type</p>
                            <a href="{{ route('file.download', $file->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium mt-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download to View
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- File Information -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h6 class="font-semibold">File Information</h6>
                </div>
                <div class="p-5">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400 shrink-0">Original Name:</dt>
                            <dd class="text-right font-medium break-all">{{ $file->name }}</dd>
                        </div>
                        @if($file->display_name && $file->display_name !== $file->name)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400 shrink-0">Display Name:</dt>
                            <dd class="text-right font-medium">{{ $file->display_name }}</dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Size:</dt>
                            <dd class="font-medium">{{ $file->human_size }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Type:</dt>
                            <dd class="text-right">
                                <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ strtoupper($file->extension) }}</span>
                                <br><span class="text-xs text-slate-500 dark:text-slate-400">{{ $file->mime_type }}</span>
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Visibility:</dt>
                            <dd>
                                @if($file->is_public)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">Public</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">Private</span>
                                @endif
                            </dd>
                        </div>
                        @if($file->folder)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Folder:</dt>
                            <dd><span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-300">{{ $file->folder }}</span></dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Uploaded:</dt>
                            <dd class="text-right">
                                {{ $file->created_at->format('M d, Y H:i') }}
                                <br><span class="text-xs text-slate-500 dark:text-slate-400">{{ $file->created_at->diffForHumans() }}</span>
                            </dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Downloads:</dt>
                            <dd class="font-medium">{{ number_format($file->download_count) }}</dd>
                        </div>
                        @if($file->last_accessed_at)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Last Accessed:</dt>
                            <dd class="text-right">
                                {{ $file->last_accessed_at->format('M d, Y H:i') }}
                                <br><span class="text-xs text-slate-500 dark:text-slate-400">{{ $file->last_accessed_at->diffForHumans() }}</span>
                            </dd>
                        </div>
                        @endif
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Uploaded by:</dt>
                            <dd class="font-medium">{{ $file->user->name }}</dd>
                        </div>
                    </dl>

                    @if($file->metadata && count($file->metadata) > 0)
                    <div class="border-t border-slate-200 dark:border-slate-800 mt-4 pt-4">
                        <h6 class="font-semibold text-sm mb-3">Metadata</h6>
                        <dl class="space-y-2 text-sm">
                            @foreach($file->metadata as $key => $value)
                            <div class="flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-slate-400 shrink-0">{{ ucfirst(str_replace('_', ' ', $key)) }}:</dt>
                                <dd class="text-right break-all">{{ is_array($value) ? json_encode($value) : $value }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>
                    @endif
                </div>
            </div>

            <!-- File URLs -->
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h6 class="font-semibold">File URLs</h6>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Direct URL:</label>
                        <div class="flex gap-2">
                            <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" value="{{ $file->cdn_url }}" readonly id="directUrl">
                            <button class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 shrink-0" type="button" onclick="copyToClipboard('{{ $file->cdn_url }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Download URL:</label>
                        <div class="flex gap-2">
                            <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" value="{{ route('file.download', $file->slug) }}" readonly id="downloadUrl">
                            <button class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 shrink-0" type="button" onclick="copyToClipboard('{{ route('file.download', $file->slug) }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>

                    @if($file->thumbnail_path)
                    <div>
                        <label class="block text-sm font-medium mb-1">Thumbnail URL:</label>
                        <div class="flex gap-2">
                            <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" value="{{ $file->thumbnail_url }}" readonly id="thumbnailUrl">
                            <button class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 shrink-0" type="button" onclick="copyToClipboard('{{ $file->thumbnail_url }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                    @endif

                    @if($file->webp_path)
                    <div>
                        <label class="block text-sm font-medium mb-1">WebP URL:</label>
                        <div class="flex gap-2">
                            <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" value="{{ $file->webp_url }}" readonly id="webpUrl">
                            <button class="inline-flex items-center justify-center w-10 h-10 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 shrink-0" type="button" onclick="copyToClipboard('{{ $file->webp_url }}')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if(auth()->user()->canManageAllFiles() || $file->user_id === auth()->id())
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h6 class="font-semibold">Actions</h6>
                </div>
                <div class="p-5 space-y-2">
                    <button type="button" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 rounded-lg border border-amber-300 dark:border-amber-600 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/50 text-sm" onclick="editFile({{ $file->id }})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit File
                    </button>
                    @if(auth()->user()->canDeleteFiles() || $file->user_id === auth()->id())
                    <button type="button" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 rounded-lg border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50 text-sm" onclick="deleteFile({{ $file->id }})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete File
                    </button>
                    @endif
                    @if($file->isImage() && !$file->is_optimized)
                    <button type="button" class="inline-flex items-center justify-center gap-2 w-full px-4 py-2 rounded-lg border border-sky-300 dark:border-sky-600 text-sky-600 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-sky-950/50 text-sm" onclick="optimizeFile({{ $file->id }})">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Optimize File
                    </button>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewPage() {
    const el = document.querySelector('[x-data]');
    return el ? Alpine.$data(el) : null;
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        showSuccess('URL copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
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
    const page = previewPage();
    if (page) {
        page.successMessage = message;
        page.showSuccess = true;
        setTimeout(() => { page.showSuccess = false; }, 3000);
    }
}

function editFile(fileId) {
    window.location.href = '{{ route("dashboard.files") }}?edit=' + fileId;
}

function deleteFile(fileId) {
    if (confirm('Are you sure you want to delete this file? This action cannot be undone.')) {
        apiFetch(`/api/files/${fileId}`, { method: 'DELETE' })
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
        alert('Optimization feature coming soon!');
    }
}
</script>
@endpush
