@extends('layouts.dashboard')

@section('title', 'Upload Files')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h5 class="font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                Upload Files
            </h5>
        </div>
        <div class="p-5">
            <!-- Upload Zone -->
            <div class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center mb-6 transition-colors cursor-pointer hover:border-indigo-400 dark:hover:border-indigo-500" id="uploadZone">
                <svg class="w-12 h-12 mx-auto text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                <h5 class="font-medium mb-1">Drag &amp; Drop Files Here</h5>
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">or click to browse files</p>
                <input type="file" id="fileInput" multiple class="hidden" accept="{{ implode(',', array_map(fn($type) => '.' . $type, $allowedTypes)) }}">
                <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 text-sm font-medium" onclick="document.getElementById('fileInput').click()">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/></svg>
                    Browse Files
                </button>
            </div>

            <!-- File List -->
            <div id="fileList" class="hidden">
                <h6 class="text-sm font-medium mb-3">Selected Files:</h6>
                <div id="selectedFiles" class="space-y-2"></div>
                <div class="flex gap-2 mt-4">
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 text-sm font-medium" id="uploadBtn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Upload Files
                    </button>
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 text-sm" id="clearBtn">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Upload Progress -->
            <div id="uploadProgress" class="hidden">
                <h6 class="text-sm font-medium mb-3">Upload Progress:</h6>
                <div class="w-full h-2 bg-slate-200 dark:bg-slate-700 rounded-full mb-3 overflow-hidden">
                    <div id="progressBar" class="h-full bg-indigo-600 rounded-full transition-all duration-300 text-xs text-white text-center leading-none" style="width: 0%"></div>
                </div>
                <div id="uploadStatus"></div>
            </div>

            <!-- Upload Settings -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div>
                    <label for="folder" class="block text-sm font-medium mb-1">Folder (Optional)</label>
                    <input type="text" class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" id="folder" placeholder="e.g., images, documents">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Leave empty for root folder</p>
                </div>
                <div>
                    <label for="visibility" class="block text-sm font-medium mb-1">Visibility</label>
                    <select class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100" id="visibility">
                        <option value="public">Public (Anyone can access)</option>
                        <option value="private">Private (Only you can access)</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Guidelines -->
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm mt-6">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h6 class="font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Upload Guidelines
            </h6>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <h6 class="text-sm font-medium mb-2">Allowed File Types:</h6>
                <div class="flex flex-wrap gap-1">
                    @foreach($allowedTypes as $type)
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">{{ strtoupper($type) }}</span>
                    @endforeach
                </div>
            </div>
            <div>
                <h6 class="text-sm font-medium mb-2">Maximum File Size:</h6>
                <p class="text-sm text-slate-600 dark:text-slate-300">{{ number_format($maxFileSize / 1024 / 1024, 0) }} MB per file</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadZone = document.getElementById('uploadZone');
    const fileInput = document.getElementById('fileInput');
    const fileList = document.getElementById('fileList');
    const selectedFiles = document.getElementById('selectedFiles');
    const uploadBtn = document.getElementById('uploadBtn');
    const clearBtn = document.getElementById('clearBtn');
    const uploadProgress = document.getElementById('uploadProgress');
    const progressBar = document.getElementById('progressBar');
    const uploadStatus = document.getElementById('uploadStatus');

    let files = [];
    const maxFileSize = {{ $maxFileSize }};
    const allowedTypes = {!! json_encode($allowedTypes) !!};

    uploadZone.addEventListener('click', function(e) {
        if (e.target === uploadZone || e.target.closest('#uploadZone') && !e.target.closest('button')) {
            fileInput.click();
        }
    });

    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/30');
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/30');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-950/30');
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    function handleFiles(fileList) {
        for (let file of fileList) {
            if (validateFile(file)) {
                files.push(file);
            }
        }
        updateFileList();
    }

    function validateFile(file) {
        const extension = file.name.split('.').pop().toLowerCase();

        if (!allowedTypes.includes(extension)) {
            alert(`File type .${extension} is not allowed`);
            return false;
        }

        if (file.size > maxFileSize) {
            alert(`File ${file.name} is too large. Maximum size is ${Math.round(maxFileSize / 1024 / 1024)}MB`);
            return false;
        }

        return true;
    }

    function updateFileList() {
        if (files.length === 0) {
            fileList.classList.add('hidden');
            return;
        }

        fileList.classList.remove('hidden');
        selectedFiles.innerHTML = '';

        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'border border-slate-200 dark:border-slate-700 rounded-lg p-3 flex justify-between items-center';
            fileItem.innerHTML = `
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <div class="min-w-0">
                        <strong class="text-sm truncate block">${file.name}</strong>
                        <small class="text-slate-500 dark:text-slate-400 text-xs">(${formatFileSize(file.size)})</small>
                    </div>
                </div>
                <button type="button" class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-red-300 dark:border-red-600 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50 shrink-0" onclick="removeFile(${index})">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;
            selectedFiles.appendChild(fileItem);
        });
    }

    window.removeFile = function(index) {
        files.splice(index, 1);
        updateFileList();
    };

    clearBtn.addEventListener('click', function() {
        files = [];
        fileInput.value = '';
        updateFileList();
    });

    uploadBtn.addEventListener('click', function() {
        if (files.length === 0) {
            alert('Please select files to upload');
            return;
        }

        uploadFiles();
    });

    function uploadFiles() {
        const formData = new FormData();

        files.forEach(file => {
            formData.append('files[]', file);
        });

        formData.append('folder', document.getElementById('folder').value);
        formData.append('visibility', document.getElementById('visibility').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        uploadProgress.classList.remove('hidden');
        fileList.classList.add('hidden');

        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressBar.textContent = Math.round(percentComplete) + '%';
            }
        });

        xhr.addEventListener('load', function() {
            if (xhr.status === 201) {
                const response = JSON.parse(xhr.responseText);
                uploadStatus.innerHTML = `
                    <div class="rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/50 px-4 py-3 text-emerald-800 dark:text-emerald-200 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        ${response.message || 'Files uploaded successfully!'}
                    </div>
                `;

                setTimeout(() => {
                    files = [];
                    fileInput.value = '';
                    document.getElementById('folder').value = '';
                    document.getElementById('visibility').value = 'public';
                    uploadProgress.classList.add('hidden');
                    progressBar.style.width = '0%';
                    progressBar.textContent = '';
                    uploadStatus.innerHTML = '';
                }, 3000);
            } else {
                const response = JSON.parse(xhr.responseText);
                uploadStatus.innerHTML = `
                    <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/50 px-4 py-3 text-red-800 dark:text-red-200 text-sm flex items-center gap-2">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        ${response.message || 'Upload failed!'}
                    </div>
                `;
            }
        });

        xhr.addEventListener('error', function() {
            uploadStatus.innerHTML = `
                <div class="rounded-lg border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/50 px-4 py-3 text-red-800 dark:text-red-200 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Upload failed! Please try again.
                </div>
            `;
        });

        xhr.open('POST', '{{ route("api.files.upload") }}');
        xhr.send(formData);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>
@endpush
