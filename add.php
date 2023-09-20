<?php
include  'inc/func.php';
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

function readConfigFile($filename) {
$contents = file_get_contents($filename);
// Remove the slashes from paths
$contents = str_replace('\/', '/', $contents);
return json_decode($contents, true);
}

function writeConfigFile($filename, $config) {
$encodedConfig = json_encode($config, JSON_PRETTY_PRINT);
file_put_contents($filename, $encodedConfig);
}

$filename = $dir."/config.json";

// Read the existing configuration
$existingConfig = readConfigFile($filename);

// Add new UUIDs here
$uuidsToAdd = [];

$uuidsToAdd[] = generateUUID();

if(!isset($_GET['email'])){
    echo 'error';exit;
}
// Add new UUIDs to the existing configuration
foreach ($uuidsToAdd as $uuid) {
    $existingConfig['inbound']['settings']['clients'][] = [
        "id" => $uuid,
        "security" => null,
        'email'=>@$_GET['email']
    ];


}

// Write the updated configuration back to the file
writeConfigFile($filename, $existingConfig);
echo (json_encode($existingConfig));
//echo "Added new UUIDs to the existing VMess configuration.\n";
//echo "Updated configuration saved to: $filename\n";


$_SESSION['message'] = 'User ADDED';

// Redirect to client.php
header('Location: client.php');
exit;
?>

