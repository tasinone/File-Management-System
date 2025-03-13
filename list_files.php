<?php
// Set the uploads directory
$uploadDir = 'uploads/';

// Create the directory if it doesn't exist
if (!file_exists($uploadDir) && !is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
    $files = array();
} else {
    // Get all files in the directory
    $fileList = scandir($uploadDir);
    
    // Filter out directories
    $files = array_filter($fileList, function($file) use ($uploadDir) {
        return !is_dir($uploadDir . $file) && $file !== '.' && $file !== '..';
    });
    
    // Format file information
    $formattedFiles = array();
    foreach ($files as $file) {
        $fileInfo = pathinfo($uploadDir . $file);
        $formattedFiles[] = array(
            'name' => $fileInfo['filename'],
            'extension' => isset($fileInfo['extension']) ? $fileInfo['extension'] : '',
            'fullName' => $file
        );
    }
    
    // Sort files by name
    usort($formattedFiles, function($a, $b) {
        return strcasecmp($a['name'], $b['name']);
    });
    
    $files = $formattedFiles;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($files);
?>