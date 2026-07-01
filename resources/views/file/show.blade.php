@extends('layouts.dashboard')

@section('title', 'View File - ' . $file->display_name)

@section('content')
<div>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-semibold">{{ $file->display_name }}</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">{{ $file->name }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('file.download', $file->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download
            </a>
            @auth
                @if(auth()->user()->canManageAllFiles() || $file->user_id === auth()->id())
                    <a href="{{ route('dashboard.files') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Back to Files
                    </a>
                @endif
            @endauth
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- File Preview -->
        <div class="lg:col-span-2">
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="p-5 text-center">
                    @if($file->isImage())
                        <img src="{{ $file->url }}" alt="{{ $file->display_name }}" class="mx-auto rounded-lg max-h-[500px]">
                    @elseif($file->isVideo())
                        <video controls class="w-full max-h-[500px] rounded-lg">
                            <source src="{{ $file->url }}" type="{{ $file->mime_type }}">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <div class="py-12">
                            <svg class="w-16 h-16 mx-auto text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <h4 class="mt-4 text-lg font-semibold">{{ $file->display_name }}</h4>
                            <p class="text-slate-500 dark:text-slate-400 mt-1">{{ strtoupper($file->extension) }} File</p>
                            <a href="{{ route('file.download', $file->slug) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium mt-4">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Download to View
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- File Details -->
        <div>
            <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h5 class="font-semibold">File Details</h5>
                </div>
                <div class="p-5">
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Size:</dt>
                            <dd class="font-medium">{{ number_format($file->size / 1024 / 1024, 2) }} MB</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Type:</dt>
                            <dd class="font-medium text-right break-all">{{ $file->mime_type }}</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Extension:</dt>
                            <dd class="font-medium">{{ strtoupper($file->extension) }}</dd>
                        </div>

                        @if($file->folder)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Folder:</dt>
                            <dd class="font-medium">{{ $file->folder }}</dd>
                        </div>
                        @endif

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

                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Downloads:</dt>
                            <dd class="font-medium">{{ number_format($file->download_count) }}</dd>
                        </div>

                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Uploaded:</dt>
                            <dd class="font-medium">{{ $file->created_at->format('M d, Y H:i') }}</dd>
                        </div>

                        @if($file->user)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Uploaded by:</dt>
                            <dd class="font-medium">{{ $file->user->name }}</dd>
                        </div>
                        @endif

                        @if($file->last_accessed_at)
                        <div class="flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-slate-400">Last accessed:</dt>
                            <dd class="font-medium">{{ $file->last_accessed_at->format('M d, Y H:i') }}</dd>
                        </div>
                        @endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
