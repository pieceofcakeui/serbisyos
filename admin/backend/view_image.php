<?php
session_start();

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    die('Access Denied');
}

if (isset($_GET['path'])) {
    $filePathFromDB = $_GET['path'];

    $pathAttempts = [
        dirname(__DIR__, 2) . '/account/' . $filePathFromDB,

        __DIR__ . '/../../account/' . $filePathFromDB,
        $_SERVER['DOCUMENT_ROOT'] . '/serbisyos/account/' . $filePathFromDB,
        $_SERVER['DOCUMENT_ROOT'] . '/serbisyos/' . $filePathFromDB
    ];
    
    $fullPath = null;

    foreach ($pathAttempts as $attemptPath) {
        $resolvedPath = realpath($attemptPath);
        if ($resolvedPath && file_exists($resolvedPath) && is_readable($resolvedPath)) {
            $fullPath = $resolvedPath;
            break;
        }
    }
    
    if (!$fullPath) {
        http_response_code(404);
        die('File not found');
    }

    $allowedDirs = [
        realpath(dirname(__DIR__, 2) . '/account/uploads/v-submissions-data/'),
        realpath($_SERVER['DOCUMENT_ROOT'] . '/serbisyos/account/uploads/v-submissions-data/'),
        realpath($_SERVER['DOCUMENT_ROOT'] . '/serbisyos/uploads/v-submissions-data/')
    ];
    
    $isAllowed = false;
    foreach ($allowedDirs as $allowedDir) {
        if ($allowedDir && strpos($fullPath, $allowedDir) === 0) {
            $isAllowed = true;
            break;
        }
    }
    
    if (!$isAllowed) {
        http_response_code(400);
        die('Invalid file path - security check failed');
    }

    $mime = mime_content_type($fullPath);
    header('Content-Type: ' . $mime);
    header('Content-Length: ' . filesize($fullPath));
    header('Cache-Control: private, max-age=3600');
    
    readfile($fullPath);
    exit;
}

http_response_code(404);
die('No path parameter provided');
?>