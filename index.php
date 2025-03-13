<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        
        .upload-section {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"], input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #45a049;
        }
        
        .file-list {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .file-list th, .file-list td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        
        .file-list th {
            background-color: #f2f2f2;
            color: #333;
        }
        
        .file-list tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .file-list tr:hover {
            background-color: #f1f1f1;
        }
        
        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 3px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
        }
        
        .download-btn {
            background-color: #2196F3;
        }
        
        .download-btn:hover {
            background-color: #0b7dda;
        }
        
        .delete-btn {
            background-color: #f44336;
        }
        
        .delete-btn:hover {
            background-color: #d32f2f;
        }
        
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .loading {
            text-align: center;
            margin-top: 20px;
            display: none;
        }
        
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border-left-color: #09f;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>File Management System</h1>
        
        <div id="message-container"></div>
        
        <div class="upload-section">
            <form id="upload-form" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="file-name">File Name (Optional - defaults to original filename):</label>
                    <input type="text" id="file-name" name="file-name" placeholder="Enter custom file name">
                </div>
                
                <div class="form-group">
                    <label for="file-upload">Select File (Max 10MB):</label>
                    <input type="file" id="file-upload" name="file-upload" required>
                </div>
                
                <button type="submit" class="btn">Upload</button>
            </form>
        </div>
        
        <h2>Uploaded Files</h2>
        <div class="loading">
            <div class="spinner"></div>
            <p>Loading files...</p>
        </div>
        
        <table class="file-list">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Extension</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="file-list-body">
                <!-- Files will be listed here dynamically -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadForm = document.getElementById('upload-form');
            const fileListBody = document.getElementById('file-list-body');
            const messageContainer = document.getElementById('message-container');
            const loadingIndicator = document.querySelector('.loading');
            
            // Load files on page load
            loadFiles();
            
            // Handle form submission
            uploadForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const fileInput = document.getElementById('file-upload');
                const fileNameInput = document.getElementById('file-name');
                
                // Check if a file is selected
                if (!fileInput.files.length) {
                    showMessage('Please select a file to upload', 'error');
                    return;
                }
                
                // Check file size (10MB = 10 * 1024 * 1024 bytes)
                const maxSize = 10 * 1024 * 1024;
                if (fileInput.files[0].size > maxSize) {
                    showMessage('File size exceeds 10MB limit', 'error');
                    return;
                }
                
                // Create FormData object
                const formData = new FormData();
                formData.append('file', fileInput.files[0]);
                formData.append('fileName', fileNameInput.value.trim());
                
                // Show loading indicator
                loadingIndicator.style.display = 'block';
                
                // Send AJAX request
                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showMessage(data.message, 'success');
                        uploadForm.reset();
                        loadFiles(); // Reload file list
                    } else {
                        showMessage(data.message, 'error');
                    }
                    loadingIndicator.style.display = 'none';
                })
                .catch(error => {
                    showMessage('Error uploading file: ' + error.message, 'error');
                    loadingIndicator.style.display = 'none';
                });
            });
            
            // Function to load files from server
            function loadFiles() {
                loadingIndicator.style.display = 'block';
                
                fetch('list_files.php')
                .then(response => response.json())
                .then(data => {
                    fileListBody.innerHTML = '';
                    
                    if (data.length === 0) {
                        fileListBody.innerHTML = '<tr><td colspan="3" style="text-align: center;">No files uploaded yet</td></tr>';
                    } else {
                        data.forEach(file => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${file.name}</td>
                                <td>${file.extension}</td>
                                <td>
                                    <button class="action-btn download-btn" data-filename="${file.fullName}">Download</button>
                                    <button class="action-btn delete-btn" data-filename="${file.fullName}">Delete</button>
                                </td>
                            `;
                            fileListBody.appendChild(row);
                        });
                        
                        // Add event listeners for download and delete buttons
                        addButtonEventListeners();
                    }
                    
                    loadingIndicator.style.display = 'none';
                })
                .catch(error => {
                    showMessage('Error loading files: ' + error.message, 'error');
                    loadingIndicator.style.display = 'none';
                });
            }
            
            // Add event listeners to download and delete buttons
            function addButtonEventListeners() {
                // Download buttons
                document.querySelectorAll('.download-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const fileName = this.getAttribute('data-filename');
                        window.location.href = 'download.php?file=' + encodeURIComponent(fileName);
                    });
                });
                
                // Delete buttons
                document.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const fileName = this.getAttribute('data-filename');
                        
                        if (confirm('Are you sure you want to delete this file?')) {
                            loadingIndicator.style.display = 'block';
                            
                            fetch('delete.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: 'file=' + encodeURIComponent(fileName)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    showMessage(data.message, 'success');
                                    loadFiles(); // Reload file list
                                } else {
                                    showMessage(data.message, 'error');
                                }
                                loadingIndicator.style.display = 'none';
                            })
                            .catch(error => {
                                showMessage('Error deleting file: ' + error.message, 'error');
                                loadingIndicator.style.display = 'none';
                            });
                        }
                    });
                });
            }
            
            // Function to show messages
            function showMessage(message, type) {
                messageContainer.innerHTML = `<div class="message ${type}">${message}</div>`;
                
                // Auto-hide message after 5 seconds
                setTimeout(() => {
                    messageContainer.innerHTML = '';
                }, 5000);
            }
        });
    </script>
</body>
</html>