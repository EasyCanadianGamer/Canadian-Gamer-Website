<?php
header('Content-Type: application/json');

$targetDir = '/usr/share/nginx/html/assets/audio/';
$featuredFile = $targetDir . 'featured.mp3';

// Collect debug info
$debug = [
    'php_tmp_dir' => sys_get_temp_dir(),
    'target_dir_exists' => is_dir($targetDir),
    'target_dir_writable' => is_writable($targetDir),
    'files_received' => $_FILES
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $debug['error'] = 'Invalid request method';
    echo json_encode($debug);
    exit;
}

// Check if file uploaded
if (empty($_FILES['audio']['name'])) {
    $debug['error'] = 'No audio file uploaded';
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
    $debug['target_dir_writable'] = is_writable($targetDir);
}

// Remove previous featured file
if (file_exists($featuredFile)) {
    unlink($featuredFile);
}

// Move uploaded file
$tmpFile = $_FILES['audio']['tmp_name'];
if (is_uploaded_file($tmpFile) && move_uploaded_file($tmpFile, $featuredFile)) {
    $debug['status'] = 'success';
    $debug['file'] = 'assets/audio/featured.mp3';
} else {
    $debug['status'] = 'error';
    $debug['error'] = 'Failed to move uploaded file. Check PHP temp dir and folder permissions.';
    $debug['tmp_file_exists'] = file_exists($tmpFile);
    $debug['tmp_file_path'] = $tmpFile;
    $debug['tmp_file_perm'] = is_writable($tmpFile) ? 'writable' : 'not writable';
}

echo json_encode($debug);
exit;
