<?php
include  'settings.php';

// Configuration JSON
$configJson = '{
    "inbounds": [
        {
            "listen": "127.0.0.1",
            "port": 10808,
            "protocol": "socks",
            "settings": {
                "auth": "noauth",
                "udp": true,
                "userLevel": 0
            },
            "sniffing": {
                "destOverride": [
                    "http",
                    "tls"
                ],
                "enabled": true
            },
            "tag": "socks"
        },
        {
            "listen": "127.0.0.1",
            "port": 10809,
            "protocol": "http",
            "settings": {
                "userLevel": 0
            },
            "tag": "http"
        }
    ],
    "outbounds": [
        {
            "protocol": "vmess",
            "settings": {
                "vnext": [
                    {
                        "address": "'.$server_addr.'",
                        "port": '.$port.',
                        "users": [
                            {
                                "alterId": 0,
                                "encryption": "",
                                "flow": "",
                                "id": "",
                                "level": 8,
                                "security": "auto"
                            }
                        ]
                    }
                ]
            },
            "streamSettings": {
                "network": "tcp",
                "security": "",
                "tcpSettings": {
                    "header": {
                        "request": {
                            "headers": {
                                "Connection": [
                                    "keep-alive"
                                ],
                                "Host": [
                                    "www.bing.com"
                                ],
                                "Pragma": "no-cache",
                                "Accept-Encoding": [
                                    "gzip, deflate"
                                ],
                                "User-Agent": [
                                    "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36",
                                    "Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_2 like Mac OS X) AppleWebKit/601.1 (KHTML, like Gecko) CriOS/53.0.2785.109 Mobile/14A456 Safari/601.1.46"
                                ]
                            },
                            "method": "GET",
                            "path": [
                                "/"
                            ],
                            "version": "1.1"
                        },
                        "type": "http"
                    }
                }
            },
            "tag": "proxy"
        }
    ]
}';

// Convert JSON to associative array
$configArray = json_decode($configJson, true);
// Generate UUID and assign it to the user ID
$uuid = $_GET['uuid']; // Generate a unique ID
$configArray['outbounds'][0]['settings']['vnext'][0]['users'][0]['id'] = $uuid;

// Assign the desired port and IP address
$port = $port; // Replace with the desired port
$ipAddress = '"'.$server_addr.'"'; // Replace with the desired IP address
$configArray['outbounds'][0]['settings']['vnext'][0]['port'] = $port;
$configArray['outbounds'][0]['settings']['vnext'][0]['address'] = $ipAddress;


// Convert the modified array back to JSON
$modifiedConfigJson = json_encode($configArray, JSON_PRETTY_PRINT);
// Generate a unique filename for the configuration file
$filename = 'client_connection/config_' . $uuid . '.json';

// Save the modified configuration to the file
file_put_contents($filename, stripslashes($modifiedConfigJson));

// Set the appropriate headers to force download the file
header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($filename));
readfile($filename);

// Delete the temporary file
unlink($filename);

?>
