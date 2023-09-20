<?php
include  'inc/func.php';

include 'settings.php';

$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die("Not authorized");
}
$configFile = $dir.'/config.json';

// Load the JSON configuration file
$config = json_decode(file_get_contents($configFile), true);

// Find the client with the specified UUID and update only the UUID value
$idToUpdate = $_GET['uuid'];
if (!$idToUpdate) {
    echo "UUID is not provided.";
    exit;
}
$newUUID = generateUUID();
$clients = &$config['inbound']['settings']['clients'];
foreach ($clients as &$client) {
    if ($client['id'] === $idToUpdate) {
//        $client['id'] = 'NEW_UUID_VALUE';
            $client['id']  = generateUUID();
        break;
    }
}

// Save the updated configuration back to the file
file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

// Redirect to client.php
header('Location: client.php');
exit;