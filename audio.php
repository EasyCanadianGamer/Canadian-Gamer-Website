<?php
$targetDir = 'assets/audio/';
$featuredFile = $targetDir . 'featured.mp3'; // always use this name

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_FILES['audio']['name'])) {
        // Remove previous file if exists
        if (file_exists($featuredFile)) unlink($featuredFile);

        // Save new audio as featured.mp3
        move_uploaded_file($_FILES['audio']['tmp_name'], $featuredFile);
        echo json_encode(['status' => 'success', 'file' => $featuredFile]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No audio file uploaded']);
    }
    exit;
}