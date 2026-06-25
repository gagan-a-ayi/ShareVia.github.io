<?php
session_start();

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    http_response_code(403);
    exit('Forbidden');
}

$file = $_GET['file'] ?? '';

if (empty($file) || strpos($file, '..') !== false) {
    http_response_code(400);
    exit('Invalid file');
}

$filepath = 'uploads/' . $file;

if (!file_exists($filepath)) {
    http_response_code(404);
    exit('File not found');
}

// Load metadata to get original name
$metaFile = 'data/files_meta.json';
$filesMetadata = json_decode(file_get_contents($metaFile), true) ?? [];

$originalName = $file;
foreach ($filesMetadata as $meta) {
    if ($meta['filename'] === $file) {
        $originalName = $meta['original_name'];
        break;
    }
}

header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $originalName . '"');
header('Content-Length: ' . filesize($filepath));
readfile($filepath);
?>