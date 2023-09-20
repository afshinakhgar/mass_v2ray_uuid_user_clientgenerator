<?php
require 'vendor/autoload.php'; // Make sure to require the autoload file from the library

include  'settings.php';
include  'inc/func.php';

$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    die ("Not authorized");
}


function generateVMessConfig($uuids) {
    $clients = [];

    foreach ($uuids as $uuid) {
        $clients[] = [
            "id" => $uuid,
            "security" => null,
            "email"=> @$_GET['email']
        ];
    }

    $config = [
        "log" => [
            "access" => "/var/log/v2ray/access.log",
            "error" => "/var/log/v2ray/error.log",
            "loglevel" => "warning"
        ],
        "inbound" => [
            "port" => 9999,
            "listen" => "0.0.0.0",
            "protocol" => "vmess",
            "settings" => [
                "auth" => null,
                "udp" => false,
                "ip" => null,
                "clients" => $clients
            ],
            "streamSettings" => [
                "network" => "tcp",
                "security" => "",
                "tcpSettings" => [
                    "connectionReuse" => true,
                    "header" => [
                        "type" => "http",
                        "request" => [
                            "version" => "1.1",
                            "method" => "GET",
                            "path" => ["/"],
                            "headers" => [
                                "Host" => ["$host"],
                                "User-Agent" => [
                                    "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.75 Safari/537.36",
                                    "Mozilla/5.0 (iPhone; CPU iPhone OS 10_0_2 like Mac OS X) AppleWebKit/601.1 (KHTML, like Gecko) CriOS/53.0.2785.109 Mobile/14A456 Safari/601.1.46"
                                ],
                                "Accept-Encoding" => ["gzip, deflate"],
                                "Connection" => ["keep-alive"],
                                "Pragma" => "no-cache"
                            ]
                        ],
                        "response" => [
                            "version" => "1.1",
                            "status" => "200",
                            "reason" => "OK",
                            "headers" => [
                                "Content-Type" => ["application/octet-stream", "video/mpeg"],
                                "Transfer-Encoding" => ["chunked"],
                                "Connection" => ["keep-alive"],
                                "Pragma" => "no-cache"
                            ]
                        ]
                    ]
                ],
                "kcpSettings" => null,
                "wsSettings" => null
            ]
        ],
        "outbound" => [
            "tag" => null,
            "protocol" => "freedom",
            "settings" => null,
            "streamSettings" => null,
            "mux" => null
        ],
        "inboundDetour" => null,
        "outboundDetour" => [
            [
                "protocol" => "blackhole",
                "settings" => null,
                "tag" => "blocked"
            ]
        ],
        "dns" => null,
        "routing" => [
            "strategy" => "rules",
            "settings" => [
                "rules" => [
                    [
                        "type" => "field",
                        "domain" => ["regexp:.*\\.ir$"],
                        "outboundTag" => "direct"
                    ],
                    [
                        "type" => "field",
                        "ip" => ["geoip:private", "geoip:ir"],
                        "outboundTag" => "direct"
                    ]
                ]
            ]
        ]
    ];

    return json_encode(($config), JSON_PRETTY_PRINT);
}

$uuids = [];

// Add new UUIDs here
$uuids[] = generateUUID();


$config = generateVMessConfig($uuids);
$dir = "/usr/local/etc/v2ray";
//$dir = "/Users/afshinakhgar/Projects/afshin";

$filename = $dir."/config.json";
//$filename = "config.json";
file_put_contents($filename, $config);
echo "Generated multi-user VMess configuration.\nSaved to: $filename\n";

?>
