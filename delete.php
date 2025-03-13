<?php
// Set the uploads directory
$uploadDir = 'uploads/';

// Initialize response array
$response = array(
    'success' => false,
    'message' => ''
);

// Check if file parameter is set
if (isset($_POST['file'])) {
    $fileName = $_POST['file'];
    $filePath = $uploadDir . $fileName;
    
    // Validate file path to prevent directory traversal
    $realPath = realpath($filePath);
    $uploadDirRealPath = realpath($uploadDir);
    
    if ($realPath === false || strpos($realPath, $uploadDirRealPath) !== 0) {
        $response['message'] = 'Invalid file path';
        echo json_encode($response);
        exit;
    }
    
    // Check if file exists
    if (file_exists($filePath) && is_file($filePath)) {
        // Attempt to delete the file
        if (unlink($filePath)) {
            $response['success'] = true;
            $response['message'] = 'File deleted successfully';
        } else {
            $response['message'] = 'Error deleting file. Please check permissions.';
        }
    } else {
        $response['message'] = 'File not found';
    }
} else {
    $response['message'] = 'No file specified';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>