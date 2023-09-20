<?php
include  'settings.php';
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Not authorized");
}



// Execute the systemctl command to get the V2Ray status
exec('systemctl status v2ray', $output, $returnVar);

// Check if the command executed successfully


if ($returnVar === 0) {
    // Output the command output
    foreach ($output as $line) {
        echo $line . "<br>";
    }
} else {
    echo "Failed to retrieve V2Ray status";
}

// Execute the systemctl restart command
//exec('systemctl restart v2ray', $output, $returnCode);

$command = '/var/www/v2ray_restart.sh';
$output = exec($command, $outputArray, $returnCode);

// Check the return code to determine if the restart was successful
if ($returnCode === 0) {
    echo 'v2ray service restarted successfully.';
} else {
    echo 'Failed to restart v2ray service. Error code: ' . $returnCode;
}

?>