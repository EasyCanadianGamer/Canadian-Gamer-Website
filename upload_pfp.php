<?php
header('Content-Type: application/json');

$targetDir = '/usr/share/nginx/html/assets/images/';
$allowedExtensions = ['png', 'jpg', 'jpeg', 'gif'];
$pfpFilePattern = $targetDir . 'pfp.*'; // matches existing PFP
$debug = [];

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $debug['error'] = 'Invalid request method';
    echo json_encode($debug);
    exit;
}

// Check if a file is uploaded
if (empty($_FILES['pfp']['name'])) {
    $debug['error'] = 'No PFP file uploaded';
    echo json_encode($debug);
    exit;
}

// Ensure target directory exists
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0775, true)) {
        $debug['error'] = 'Failed to create target directory';
        echo json_encode($debug);
        exit;
    }
}

// Ensure directory is writable
if (!is_writable($targetDir)) {
    chmod($targetDir, 0775);
}

// Remove any existing PFP
foreach (glob($pfpFilePattern) as $file) {
    unlink($file);
}

// Process the uploaded file
$uploadedFile = $_FILES['pfp']['tmp_name'];
$uploadedName = $_FILES['pfp']['name'];
$extension = strtolower(pathinfo($uploadedName, PATHINFO_EXTENSION));

if (!in_array($extension, $allowedExtensions)) {
    $debug['error'] = 'Invalid file type. Only png, jpg, jpeg, gif allowed.';
    echo json_encode($debug);
    exit;
}

// Save the new PFP as pfp.extension
$destination = $targetDir . 'pfp.' . $extension;
if (is_uploaded_file($uploadedFile) && move_uploaded_file($uploadedFile, $destination)) {
    $debug['status'] = 'success';
    $debug['file'] = '/assets/images/pfp.' . $extension;
} else {
    $debug['status'] = 'error';
    $debug['error'] = 'Failed to move uploaded file. Check permissions.';
}

echo json_encode($debug);
exit;
