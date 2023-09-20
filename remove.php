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



// Load the JSON configuration file
$configFile = $dir.'/config.json';
$config = json_decode(file_get_contents($configFile), true);

// Remove the client with the specified email
$idToRemove = $_GET['uuid'];
if(!$_GET['uuid']){
    echo "ID IS NOT CORRECT_" ;exit;
}
$updatedClients = array_filter($config['inbound']['settings']['clients'], function ($client) use ($idToRemove) {

    if($client['email']){

    }
    return $client['id'] !== $idToRemove;
});
$config['inbound']['settings']['clients'] = array_values($updatedClients);

// Save the updated configuration back to the file
file_put_contents($configFile, json_encode($config, JSON_PRETTY_PRINT));

//echo 'Client with email ' . $emailToRemove . ' has been removed from the configuration.';


// Redirect to client.php
header('Location: client.php');
exit;

?>