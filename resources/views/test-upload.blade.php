<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>File Upload Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold mb-6">File Upload Test</h1>
        
        <!-- Single File Upload -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Single File Upload</h2>
            <form id="singleUploadForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select File</label>
                    <input type="file" name="file" id="singleFile" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Type (optional)</label>
                    <select name="uploadType" id="singleUploadType" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="temp">Temp</option>
                        <option value="cv">CV</option>
                        <option value="profile-photos">Profile Photos</option>
                        <option value="company-logos">Company Logos</option>
                        <option value="job-documents">Job Documents</option>
                        <option value="application-documents">Application Documents</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    Upload File
                </button>
                <div id="singleResult" class="mt-4 p-4 bg-gray-50 rounded-lg hidden"></div>
            </form>
        </div>

        <!-- Multiple File Upload -->
        <div>
            <h2 class="text-xl font-semibold mb-4">Multiple File Upload</h2>
            <form id="multipleUploadForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Files</label>
                    <input type="file" name="files[]" id="multipleFiles" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Type (optional)</label>
                    <select name="uploadType" id="multipleUploadType" class="block w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="temp">Temp</option>
                        <option value="cv">CV</option>
                        <option value="profile-photos">Profile Photos</option>
                        <option value="company-logos">Company Logos</option>
                        <option value="job-documents">Job Documents</option>
                        <option value="application-documents">Application Documents</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition">
                    Upload Files
                </button>
                <div id="multipleResult" class="mt-4 p-4 bg-gray-50 rounded-lg hidden"></div>
            </form>
        </div>
    </div>

    <script>
        const API_BASE = '/api';
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        // Single file upload
        document.getElementById('singleUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData();
            const fileInput = document.getElementById('singleFile');
            const uploadType = document.getElementById('singleUploadType').value;
            const resultDiv = document.getElementById('singleResult');

            if (!fileInput.files[0]) {
                showWarningToast('Please select a file');
                return;
            }

            formData.append('file', fileInput.files[0]);
            formData.append('uploadType', uploadType);

            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<p class="text-blue-600">Uploading...</p>';

            try {
                const response = await fetch(`${API_BASE}/upload`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    resultDiv.innerHTML = `
                        <p class="text-green-600 font-semibold mb-2">✓ Upload Successful!</p>
                        <div class="text-sm space-y-1">
                            <p><strong>File Path:</strong> ${data.data.filePath}</p>
                            <p><strong>Download URL:</strong> <a href="${data.data.downloadURL}" target="_blank" class="text-blue-600 hover:underline">${data.data.downloadURL}</a></p>
                            <p><strong>Original Name:</strong> ${data.data.originalname}</p>
                            <p><strong>Size:</strong> ${(data.data.size / 1024).toFixed(2)} KB</p>
                            <p><strong>MIME Type:</strong> ${data.data.mimetype}</p>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `<p class="text-red-600">✗ Upload Failed: ${data.message || 'Unknown error'}</p>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<p class="text-red-600">✗ Error: ${error.message}</p>`;
            }
        });

        // Multiple file upload
        document.getElementById('multipleUploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData();
            const fileInput = document.getElementById('multipleFiles');
            const uploadType = document.getElementById('multipleUploadType').value;
            const resultDiv = document.getElementById('multipleResult');

            if (!fileInput.files || fileInput.files.length === 0) {
                showWarningToast('Please select at least one file');
                return;
            }

            for (let i = 0; i < fileInput.files.length; i++) {
                formData.append('files[]', fileInput.files[i]);
            }
            formData.append('uploadType', uploadType);

            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<p class="text-blue-600">Uploading ' + fileInput.files.length + ' file(s)...</p>';

            try {
                const response = await fetch(`${API_BASE}/upload-multiple`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    credentials: 'include',
                    body: formData,
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    let filesHtml = '<p class="text-green-600 font-semibold mb-2">✓ Upload Successful!</p>';
                    filesHtml += `<p class="mb-2">${data.data.files.length} file(s) uploaded:</p>`;
                    filesHtml += '<div class="space-y-2">';
                    data.data.files.forEach((file, index) => {
                        filesHtml += `
                            <div class="border border-gray-200 rounded p-2">
                                <p class="font-medium">File ${index + 1}: ${file.originalname}</p>
                                <p class="text-sm text-gray-600">URL: <a href="${file.url}" target="_blank" class="text-blue-600 hover:underline">${file.url}</a></p>
                                <p class="text-sm text-gray-600">Size: ${(file.size / 1024).toFixed(2)} KB</p>
                            </div>
                        `;
                    });
                    filesHtml += '</div>';
                    resultDiv.innerHTML = filesHtml;
                } else {
                    resultDiv.innerHTML = `<p class="text-red-600">✗ Upload Failed: ${data.message || 'Unknown error'}</p>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<p class="text-red-600">✗ Error: ${error.message}</p>`;
            }
        });
    </script>
</body>
</html>
