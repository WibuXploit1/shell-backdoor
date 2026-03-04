<?php
// Simple PHP File Manager (PHP 5.2.17 compatible)
// Password: Fm2025

error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$PASSWORD = 'Fm2025';
$DOCROOT = '/';
$MAX_UPLOAD = ini_get('upload_max_filesize');

function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function safe_join_path($base, $path) {
    $base = rtrim($base, DIRECTORY_SEPARATOR);
    if ($base === '') $base = DIRECTORY_SEPARATOR;

    $normalized = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
    $full = $base . DIRECTORY_SEPARATOR . ltrim($normalized, DIRECTORY_SEPARATOR);

    $real = realpath($full);
    if ($real === false) {
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $normalized), 'strlen');
        $cur = $base;
        foreach ($parts as $p) {
            if ($p === '.' || $p === '..') return false;
            $cur .= DIRECTORY_SEPARATOR . $p;
        }
        return $cur;
    }

    $base_check = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $real_check = rtrim($real, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    if (strpos($real_check, $base_check) === 0) return $real;
    return false;
}

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
    } else {
        $error = 'Password salah.';
    }
}

if ($logged_in && isset($_GET['logout'])) {
    unset($_SESSION['fm_auth']);
    header('Location: ' . basename(__FILE__));
    exit;
}

if (!$logged_in) {
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Login</title></head>
<body>
<h2>File Manager Login</h2>
<?php if (!empty($error)) echo '<p style="color:red">'.h($error).'</p>'; ?>
<form method="post">
  <input type="hidden" name="action" value="login">
  <label>Password: <input type="password" name="password"></label>
  <input type="submit" value="Login">
</form>
</body></html>
<?php
exit;
}

$dir = isset($_REQUEST['dir']) ? $_REQUEST['dir'] : '';
if ($dir === '.' || $dir === './') $dir = '';
$curDir = safe_join_path($DOCROOT, $dir);
if ($curDir === false) {
    $curDir = $DOCROOT;
    $dir = '';
}

$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

function check_csrf() {
    if (empty($_POST['token']) || $_POST['token'] !== $_SESSION['fm_token']) die('CSRF mismatch');
}

$msg = ''; $err = '';

if ($action === 'upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (!isset($_FILES['file'])) $err = 'Tidak ada file.';
    else {
        $up = $_FILES['file'];
        if ($up['error'] !== 0) $err = 'Error upload.';
        else {
            $target = safe_join_path($curDir, basename($up['name']));
            if (move_uploaded_file($up['tmp_name'], $target)) $msg = 'Upload sukses.';
            else $err = 'Gagal upload.';
        }
    }
}

if ($action === 'create_folder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = trim($_POST['name']);
    if ($name !== '') {
        $target = safe_join_path($curDir, $name);
        if (!file_exists($target)) {
            if (mkdir($target, 0755)) $msg = 'Folder dibuat.';
            else $err = 'Gagal membuat folder.';
        } else $err = 'Folder sudah ada.';
    } else $err = 'Nama kosong.';
}

if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = $_POST['name'];
    $target = safe_join_path($curDir, $name);
    if (is_dir($target)) {
        if (@rmdir($target)) $msg = 'Folder dihapus.';
        else $err = 'Gagal hapus folder.';
    } else {
        if (@unlink($target)) $msg = 'File dihapus.';
        else $err = 'Gagal hapus file.';
    }
}

if ($action === 'rename' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $old = $_POST['old']; $new = $_POST['new'];
    $told = safe_join_path($curDir, $old);
    $tnew = safe_join_path($curDir, $new);
    if (rename($told, $tnew)) $msg = 'Rename sukses.'; else $err = 'Gagal rename.';
}

if ($action === 'edit' && isset($_GET['name'])) {
    $name = $_GET['name'];
    $target = safe_join_path($curDir, $name);
    if (file_exists($target) && !is_dir($target)) $content = file_get_contents($target);
    else $err = 'File tidak ditemukan.';
}

if ($action === 'edit_save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $name = $_POST['name'];
    $target = safe_join_path($curDir, $name);
    if (file_exists($target) && is_writable($target)) {
        file_put_contents($target, $_POST['content']);
        $msg = 'File tersimpan.';
    } else $err = 'Tidak bisa menulis file.';
}

?>
<!doctype html>
<html><head><meta charset="utf-8"><title>File Manager</title>
<style>
body{font-family:Arial;font-size:13px;margin:10px}
table{border-collapse:collapse;width:100%}
td,th{border:1px solid #ccc;padding:5px}
th{background:#eee}
</style></head>
<body>
<h2>File Manager</h2>
<p>Root: <?php echo h($DOCROOT); ?> | PHP: <?php echo phpversion(); ?> | Upload max: <?php echo h($MAX_UPLOAD); ?> | <a href="?logout=1">Logout</a></p>
<?php if($msg) echo '<p style="color:green">'.h($msg).'</p>'; if($err) echo '<p style="color:red">'.h($err).'</p>'; ?>

<form method="post" enctype="multipart/form-data" action="?dir=<?php echo urlencode($dir); ?>">
<input type="hidden" name="action" value="upload">
<input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
<input type="file" name="file"><input type="submit" value="Upload">
</form>

<form method="post" action="?dir=<?php echo urlencode($dir); ?>">
<input type="hidden" name="action" value="create_folder">
<input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
<input type="text" name="name" placeholder="Nama folder">
<input type="submit" value="Buat Folder">
</form>

<?php
$items = @scandir($curDir);
if ($items === false) { echo '<p>Gagal membaca folder.</p>'; exit; }

$parent = dirname($dir);
if ($parent === '.') $parent = '';
echo '<p>Current: /'.h($dir).'</p>';
?>
<table><tr><th>Nama</th><th>Jenis</th><th>Ukuran</th><th>Action</th></tr>
<tr><td colspan="4"><a href="?dir=<?php echo urlencode($parent); ?>">⤴ Parent</a></td></tr>
<?php
foreach ($items as $f) {
    if ($f==='.'||$f==='..') continue;
    $p = $curDir.DIRECTORY_SEPARATOR.$f;
    $isDir = is_dir($p);
    echo '<tr><td>'.h($f).'</td><td>'.($isDir?'Folder':'File').'</td><td>'.($isDir?'-':filesize($p)).'</td><td>';
    if ($isDir) {
        echo '<a href="?dir='.urlencode(trim($dir.'/'.$f,'/')).'">Open</a> ';
    } else {
        echo '<a href="?dir='.urlencode($dir).'&action=edit&name='.urlencode($f).'">Edit</a> ';
    }
    ?>
    <form method="post" style="display:inline" action="?dir=<?php echo urlencode($dir); ?>">
    <input type="hidden" name="action" value="delete">
    <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
    <input type="hidden" name="name" value="<?php echo h($f); ?>">
    <input type="submit" value="Delete" onclick="return confirm('Hapus?')">
    </form>
    <form method="post" style="display:inline" action="?dir=<?php echo urlencode($dir); ?>">
    <input type="hidden" name="action" value="rename">
    <input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
    <input type="hidden" name="old" value="<?php echo h($f); ?>">
    <input type="text" name="new" value="<?php echo h($f); ?>" size="10">
    <input type="submit" value="Rename">
    </form>
    <?php
    echo '</td></tr>';
}
?>
</table>

<?php if($action==='edit' && isset($content)): ?>
<hr><h3>Edit: <?php echo h($_GET['name']); ?></h3>
<form method="post" action="?dir=<?php echo urlencode($dir); ?>">
<input type="hidden" name="action" value="edit_save">
<input type="hidden" name="name" value="<?php echo h($_GET['name']); ?>">
<input type="hidden" name="token" value="<?php echo h($TOKEN); ?>">
<textarea name="content" style="width:100%;height:400px"><?php echo h($content); ?></textarea>
<p><input type="submit" value="Simpan"></p>
</form>
<?php endif; ?>
</body></html>