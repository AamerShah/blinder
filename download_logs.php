<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    exit('Unauthorized access');
}

$logs_file = __DIR__ . '/logs.json';

// Check if the logs file exists
if (!file_exists($logs_file)) {
    http_response_code(404);
    exit('Logs file not found');
}

// Set headers for download
header('Content-Type: application/json');
header('Content-Disposition: attachment; filename="logs.json"');
header('Content-Length: ' . filesize($logs_file));

// Output the file contents
readfile($logs_file);
exit;
?>
