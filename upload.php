<?php
// Set the uploads directory
$uploadDir = 'uploads/';

// Create the directory if it doesn't exist
if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if file was uploaded
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // Get file information
    $file = $_FILES['file'];
    $originalName = $file['name'];
    $tempPath = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileType = $file['type'];
    
    // Check file size (10MB = 10 * 1024 * 1024 bytes)
    $maxSize = 10 * 1024 * 1024;
    if ($fileSize > $maxSize) {
        $response['message'] = 'File size exceeds 10MB limit';
        echo json_encode($response);
        exit;
    }
    
    // Get file extension
    $fileExt = pathinfo($originalName, PATHINFO_EXTENSION);
    
    // Use custom filename if provided, otherwise use original
    $customName = !empty($_POST['fileName']) ? $_POST['fileName'] : pathinfo($originalName, PATHINFO_FILENAME);
    
    // Generate safe filename - preserve spaces but remove potentially dangerous characters
    $fileName = preg_replace('/[^\w\s\-\.]/', '', $customName);
    
    // If filename is empty after sanitization, use a default
    if (empty($fileName)) {
        $fileName = 'file_' . time();
    }
    
    // Add extension to filename
    $fullFileName = $fileName . '.' . $fileExt;
    
    // Generate unique name if file already exists
    $counter = 1;
    while (file_exists($uploadDir . $fullFileName)) {
        $fullFileName = $fileName . '_' . $counter . '.' . $fileExt;
        $counter++;
    }
    
    // Move the file to the uploads directory
    if (move_uploaded_file($tempPath, $uploadDir . $fullFileName)) {
        $response['success'] = true;
        $response['message'] = 'File uploaded successfully';
    } else {
        $response['message'] = 'Error uploading file. Please try again.';
    }
} else {
    // Get upload error message
    $uploadErrors = array(
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
    );
    
    $errorCode = isset($_FILES['file']) ? $_FILES['file']['error'] : UPLOAD_ERR_NO_FILE;
    $response['message'] = isset($uploadErrors[$errorCode]) ? $uploadErrors[$errorCode] : 'Unknown upload error';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>