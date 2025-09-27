@extends('layouts.dashboard')

@section('title', 'Upload Files')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-cloud-upload me-2"></i>
                    Upload Files
                </h5>
            </div>
            <div class="card-body">
                <!-- Upload Zone -->
                <div class="upload-zone p-5 text-center mb-4" id="uploadZone">
                    <i class="bi bi-cloud-upload fs-1 text-muted mb-3"></i>
                    <h5>Drag & Drop Files Here</h5>
                    <p class="text-muted mb-3">or click to browse files</p>
                    <input type="file" id="fileInput" multiple class="d-none" accept="{{ implode(',', array_map(fn($type) => '.' . $type, $allowedTypes)) }}">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                        <i class="bi bi-folder2-open me-2"></i>
                        Browse Files
                    </button>
                </div>

                <!-- File List -->
                <div id="fileList" class="d-none">
                    <h6>Selected Files:</h6>
                    <div id="selectedFiles"></div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success" id="uploadBtn">
                            <i class="bi bi-cloud-upload me-2"></i>
                            Upload Files
                        </button>
                        <button type="button" class="btn btn-secondary" id="clearBtn">
                            <i class="bi bi-x-circle me-2"></i>
                            Clear All
                        </button>
                    </div>
                </div>

                <!-- Upload Progress -->
                <div id="uploadProgress" class="d-none">
                    <h6>Upload Progress:</h6>
                    <div class="progress mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <div id="uploadStatus"></div>
                </div>

                <!-- Upload Settings -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <label for="folder" class="form-label">Folder (Optional)</label>
                        <input type="text" class="form-control" id="folder" placeholder="e.g., images, documents">
                        <div class="form-text">Leave empty for root folder</div>
                    </div>
                    <div class="col-md-6">
                        <label for="visibility" class="form-label">Visibility</label>
                        <select class="form-select" id="visibility">
                            <option value="public">Public (Anyone can access)</option>
                            <option value="private">Private (Only you can access)</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Guidelines -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    Upload Guidelines
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Allowed File Types:</h6>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach($allowedTypes as $type)
                                <span class="badge bg-secondary">{{ strtoupper($type) }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6>Maximum File Size:</h6>
                        <p class="mb-0">{{ number_format($maxFileSize / 1024 / 1024, 0) }} MB per file</p>
                    </div>
                </div>
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
    const progressBar = document.querySelector('.progress-bar');
    const uploadStatus = document.getElementById('uploadStatus');

    let files = [];
    const maxFileSize = {{ $maxFileSize }};
    const allowedTypes = {!! json_encode($allowedTypes) !!};

    // Drag and drop functionality
    uploadZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadZone.classList.add('border-primary', 'bg-light');
    });

    uploadZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('border-primary', 'bg-light');
    });

    uploadZone.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadZone.classList.remove('border-primary', 'bg-light');
        handleFiles(e.dataTransfer.files);
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        handleFiles(e.target.files);
    });

    // Handle selected files
    function handleFiles(fileList) {
        for (let file of fileList) {
            if (validateFile(file)) {
                files.push(file);
            }
        }
        updateFileList();
    }

    // Validate file
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

    // Update file list display
    function updateFileList() {
        if (files.length === 0) {
            fileList.classList.add('d-none');
            return;
        }

        fileList.classList.remove('d-none');
        selectedFiles.innerHTML = '';

        files.forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'border rounded p-2 mb-2 d-flex justify-content-between align-items-center';
            fileItem.innerHTML = `
                <div>
                    <i class="bi bi-file-earmark me-2"></i>
                    <strong>${file.name}</strong>
                    <small class="text-muted ms-2">(${formatFileSize(file.size)})</small>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                    <i class="bi bi-x"></i>
                </button>
            `;
            selectedFiles.appendChild(fileItem);
        });
    }

    // Remove file
    window.removeFile = function(index) {
        files.splice(index, 1);
        updateFileList();
    };

    // Clear all files
    clearBtn.addEventListener('click', function() {
        files = [];
        fileInput.value = '';
        updateFileList();
    });

    // Upload files
    uploadBtn.addEventListener('click', function() {
        if (files.length === 0) {
            alert('Please select files to upload');
            return;
        }

        uploadFiles();
    });

    // Upload files function
    function uploadFiles() {
        const formData = new FormData();

        files.forEach(file => {
            formData.append('files[]', file);
        });

        formData.append('folder', document.getElementById('folder').value);
        formData.append('visibility', document.getElementById('visibility').value);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        // Show progress
        uploadProgress.classList.remove('d-none');
        fileList.classList.add('d-none');

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
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        ${response.message || 'Files uploaded successfully!'}
                    </div>
                `;

                // Reset form
                setTimeout(() => {
                    files = [];
                    fileInput.value = '';
                    document.getElementById('folder').value = '';
                    document.getElementById('visibility').value = 'public';
                    uploadProgress.classList.add('d-none');
                    progressBar.style.width = '0%';
                    progressBar.textContent = '';
                    uploadStatus.innerHTML = '';
                }, 3000);
            } else {
                const response = JSON.parse(xhr.responseText);
                uploadStatus.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${response.message || 'Upload failed!'}
                    </div>
                `;
            }
        });

        xhr.addEventListener('error', function() {
            uploadStatus.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Upload failed! Please try again.
                </div>
            `;
        });

        xhr.open('POST', '{{ route("api.files.upload") }}');
        xhr.send(formData);
    }

    // Format file size
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
