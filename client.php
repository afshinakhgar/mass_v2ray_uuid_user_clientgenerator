<?php
include  'inc/func.php';
include  'settings.php';


$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];



$valid_users = array_keys($valid_passwords);
$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);
if(!isset($_GET['uuid'])){
    if (!$validated) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        die ("Not authorized");
    }

}
$showAccess = false;

if(!isset($_GET['uuid'])){

    $showAccess = true;
}




require 'vendor/autoload.php'; // Make sure to require the autoload file from the library

use chillerlan\QRCode\QRCode ;
use chillerlan\QRCode\Common\EccLevel;
use PHPUnit\Util\Color;


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

$existingConfig = readConfigFile($filename);

$clients = $existingConfig['inbound']['settings']['clients'];
$data = json_encode($existingConfig['inbound']['settings']['clients']);
foreach($clients as $i=>$item){
    $vmess = '';
    $dataN = [
        'id'=> $item['id'],
        'add'=> $server_addr,
        'ps'=> 'Iran:|'.$item['email'],
        'host'=> $host,
        'net'=> 'tcp',
        'path'=> '',
        'port'=> $port,
        'v'=> '2',
        'sni'=> '',
        'scy'=> 'auto',
        'tls'=> '',
        'aid'=> '0',
        'type'=> 'http',
    ];


    $dataNew[$i] = $dataN;
    $vmess = base64_encode(json_encode($dataN));

    $dataNew[$i]['vmess'] = 'vmess://'.$vmess;


    $showArrayData [] =
        [
            'id'=> $item['id'],
            'add'=> $server_addr,
            'host'=> $host,
            'email'=> $item['email'],
            'port'=> $port,
            'net'=> 'tcp',
            'ps'=> 'ENGLAND:|'.$item['email'],
            'vmess'=> $dataNew[$i]['vmess'],
        ];
}

if(isset($_GET['uuid'])){
    foreach ($showArrayData as $rowdata){
        if($rowdata['id'] == $_GET['uuid']){
            $rowSelected = $rowdata;


            break;
        }
    }

    unset($showArrayData);

    $showArrayData = [$rowSelected];



}

$totalAccount =  count($showArrayData);


?>


<html>
<head>
    <title>Clients</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <style>
        body{
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="container ">

    <h2 class="h2 lead"> Total Accounts : <?= $totalAccount?></h2>

    <table class="table table-responsive table-striped center ">
        <tr class="text-center">
            <th>
                QR
            </th>
            <th>
                Download Client
            </th>
            <th>
                user
            </th>
            <th>
                id
            </th>
            <th>
                ip
            </th>
            <th>
                port
            </th>
            <th>
                host
            </th>
            <th>
                vmess
            </th>
            <th>
                DELETE
            </th>
            <th>
                regenerate UUID
            </th>

        </tr>
        <?php foreach ($showArrayData as $row):?>


            <tr>
                <td>
                    <?php


                    // quick and simple:
                    echo '<img src="'.(new QRCode)->render($row['vmess']).'" alt="QR Code" class="Responsive image rounded mx-auto d-block" width="200px" />';


                    ?>

                </td>
                <th>
                    <a href="/generate_client.php?uuid=<?=$row['id']?>">
                        Download config
                    </a>
                </th>
                <td>
                    <a href="/client.php?uuid=<?=$row['id']?>"><?=$row['email']?></a>
                </td>
                <td>
                    <?=$row['id']?>
                </td>
                <td>
                    <?=$row['add']?>
                </td>
                <td>
                    <?=$row['port']?>
                </td>
                <td>
                    <?=$row['host']?>
                </td>
                <td>
                    <input type="text" value="<?=$row['vmess']?>">
                </td>

                <?php if($showAccess):?>
                <td>
                    <a href="/remove.php?uuid=<?=$row['id']?>" onclick="return confirm('Are you sure you want to delete?')">delete</a>
                </td>

                <td>
                    <a href="/regenerate_uuid.php?uuid=<?=$row['id']?>" onclick="return confirm('Are you sure you want to regenerate?')">Re UUID</a>
                </td>

                <?php endif;?>

            </tr>


        <?php endforeach;?>
    </table>

</div>

</body>
</html>
