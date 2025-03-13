<?php
// Set the uploads directory
$uploadDir = 'uploads/';

// Check if file parameter is set
if (isset($_GET['file'])) {
    $fileName = $_GET['file'];
    $filePath = $uploadDir . $fileName;
    
    // Validate file path to prevent directory traversal
    $realPath = realpath($filePath);
    $uploadDirRealPath = realpath($uploadDir);
    
    if ($realPath === false || strpos($realPath, $uploadDirRealPath) !== 0) {
        http_response_code(403);
        die('Invalid file path');
    }
    
    // Check if file exists
    if (file_exists($filePath) && is_file($filePath)) {
        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Clear output buffer
        ob_clean();
        flush();
        
        // Read file and output to browser
        readfile($filePath);
        exit;
    } else {
        http_response_code(404);
        die('File not found');
    }
} else {
    http_response_code(400);
    die('No file specified');
}
?>