<?php
$valid_user_hash = '$2a$12$bK5fJmlxn1Ldqnb8G4uyNeFl5scFzY7rHDmz0n/gXQdHhP.mGa7RG'; 
$valid_pass_hash = '$2a$12$Or6xUx6qiHaUnEDKTq8CFO8yhAsl3AwQ9/.MyuPKux.pxJ1TTeg2e';

if (isset($_POST['user'], $_POST['pass'])) {
    if (
        password_verify($_POST['user'], $valid_user_hash) &&
        password_verify($_POST['pass'], $valid_pass_hash)
    ) {
        $_SESSION['login'] = true;
    } else {
        $error = "Login gagal, salah!";
    }
}

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: sans-serif; background: #000; color: white; display: flex; height: 100vh; align-items: center; justify-content: center; }
        form { background: #111; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(255,255,255,0.1); }
        input { display: block; width: 100%; padding: 10px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <form method="post">
        <h2>🔐 Login</h2>
        <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
        <input type="text" name="user" placeholder="Username" required>
        <input type="password" name="pass" placeholder="Password" required>
        <input type="submit" value="Login">
    </form>
</body>
</html>
<?php
exit;
}
$logFile = 'wop.txt';

$suspectPatterns = [
    'shell_exec', 'system\(', 'passthru', 'exec\(', 'popen', 'proc_open', 'pcntl_exec',
    'eval\(', 'base64_decode', 'gzuncompress', 'gzinflate', 'str_rot13', 'assert\(',
    'htmlspecialchars_decode', 'stripslashes', 'preg_replace.*\/e', 'create_function',
    'scandir', 'chmod', 'chown', 'file_get_contents', 'file_put_contents', 'fopen',
    'password', 'login', 'auth_pass', 'cmdOutput', '$_POST', '$_GET', '$_REQUEST',
    'cmd', 'shell', 'command', 'terminal', 'payload', 'JFIF', 'exif_read_data', 
    '__halt_compiler', 'GIF89a', '📂', '⚠️', '❌', '✅', '🔴', '🟢', '💀', '"sy"."st"."ym"'
];

if (isset($_GET['delete'])) {
    $fileToDelete = realpath($_GET['delete']);
    if ($fileToDelete && file_exists($fileToDelete) && pathinfo($fileToDelete, PATHINFO_EXTENSION) === 'php' && is_writable($fileToDelete)) {
        if (unlink($fileToDelete)) {
            echo "<script>alert('DONE'); window.location='?';</script>";
        }
    }
}

$logHandle = fopen($logFile, 'w');

echo "<style>
    body { font-family: monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
    th, td { border: 1px solid #333; padding: 10px; text-align: left; vertical-align: top; overflow: hidden; }
    th { background: #333; color: #fff; }
    tr:hover { background: #252525; }
    .btn-del { color: #ff4444; text-decoration: none; font-weight: bold; }
    .btn-open { background: #00ccff; color: #000; padding: 2px 8px; text-decoration: none; font-weight: bold; border-radius: 3px; font-size: 0.8em; display: inline-block; margin-top: 5px; }
    .pattern-tag { background: #444; color: #ffae00; padding: 2px 5px; border-radius: 3px; margin-right: 5px; font-size: 0.8em; display: inline-block; margin-bottom: 3px; }
    .code-view { 
        width: 100%; height: 150px; background: #000; color: #0f0; border: 1px solid #444; 
        margin-top: 10px; font-size: 11px; white-space: pre-wrap; word-wrap: break-word; 
        overflow-y: auto; box-sizing: border-box;
    }
    .status-badge { padding: 2px 5px; border-radius: 3px; font-size: 0.8em; font-weight: bold; display: block; width: fit-content; }
    .status-warn { background: #ff0000; color: #fff; }
    .status-ok { background: #008800; color: #fff; }
    .status-manual { background: #ffae00; color: #000; }
</style>";

echo "<h2> SHELL SCANNER </h2>";
echo "<p>gass wait... log: <strong>$logFile</strong></p>";
echo "<table>";
echo "<tr><th width='40'>No</th><th>File Path & Code</th><th width='150'>Live Status</th><th width='180'>Patterns</th><th width='75'>Action</th></tr>";

$directory = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));
$foundCount = 0;

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$serverRoot = realpath($_SERVER['DOCUMENT_ROOT']);

foreach ($directory as $file) {
    if ($file->isDir()) continue;

    $filePath = $file->getRealPath();
    $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    fwrite($logHandle, $filePath . PHP_EOL);

    if ($ext === 'php') {
        if ($filePath === __FILE__) continue;

        $content = @file_get_contents($filePath);
        if ($content === false) continue;

        $hits = [];
        foreach ($suspectPatterns as $pattern) {
            if (preg_match("/" . preg_quote($pattern, '/') . "/i", $content)) {
                $hits[] = str_replace('\\', '', $pattern);
            }
        }

        if (!empty($hits)) {
            $foundCount++;
            
            $relativeScriptPath = str_replace($serverRoot, '', $filePath);
            $relativeScriptPath = str_replace('\\', '/', $relativeScriptPath);
            $targetUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $relativeScriptPath;
            
            $ctx = stream_context_create(['http' => ['timeout' => 2]]);
            $testOutput = @file_get_contents($targetUrl, false, $ctx);
            
            $liveStatus = "<span class='status-badge status-ok'>aman</span>";
            if ($testOutput !== false && strlen(trim($testOutput)) > 0) {
                if (preg_match('/(password|login|command|shell|terminal|upload|execute|system|post)/i', $testOutput)) {
                    $liveStatus = "<span class='status-badge status-warn'> SHELL</span>";
                } else {
                    $liveStatus = "<span class='status-badge status-manual'>Cek Manual</span>";
                }
            }

            echo "<tr>";
            echo "<td>$foundCount</td>";
            echo "<td>
                    <span style='color:#fff; font-weight:bold;'>$filePath</span><br>
                    <textarea class='code-view' readonly>" . htmlspecialchars($content) . "</textarea>
                  </td>";
            echo "<td>
                    $liveStatus
                    <a href='$targetUrl' target='_blank' class='btn-open'> OPEN </a>
                    <div style='font-size: 0.7em; color: #666; margin-top: 5px;'>Size: ".strlen($content)." bytes</div>
                  </td>";
            echo "<td>";
            foreach (array_unique($hits) as $h) echo "<span class='pattern-tag'>$h</span>";
            echo "</td>";
            echo "<td><a class='btn-del' href='?delete=" . urlencode($filePath) . "' onclick='return confirm(\"Sikat?\")'>HAPUS</a></td>";
            echo "</tr>";
        }
    }
}

fclose($logHandle);
if ($foundCount === 0) echo "<tr><td colspan='5' style='text-align:center;'>BAGUS AMAN</td></tr>";
echo "</table>";
?>