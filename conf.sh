      #!/bin/bash

echo "Installing V2Ray with secure configurations..."

#bash <(curl -sSL https://raw.githubusercontent.com/afshinakhgar/mass_v2ray_uuid_user_clientgenerator/master/install.sh)

SERVER_IP=$(curl -s https://canhazip.com)
echo "Detected server IP: $SERVER_IP"

    CONFIG_FILE="/usr/local/etc/v2ray/config.json"

if [ ! -f "$CONFIG_FILE" ]; then
    CONFIG_FILE="/usr/local/etc/v2ray/config.json"
fi

if [ -f "$CONFIG_FILE" ]; then
    echo "Updating V2Ray configuration for secure DNS..."
    
    cp "$CONFIG_FILE" "$CONFIG_FILE.bak"
    
    cat > "$CONFIG_FILE" << EOF
{
    "log": {
        "access": "/var/log/v2ray/access.log",
        "error": "/var/log/v2ray/error.log",
        "loglevel": "warning"
    },
    "inbound": {
        "port": 9999,
        "listen": "0.0.0.0",
        "protocol": "vmess",
        "settings": {
            "clients": [
                {
                    "id": "9c0d7cd7-e900-40e6-9203-858e3b3b6723",
                    "security": "none",
                    "email": "admin@sabacore.ir"
                }
            ]
        },
        "streamSettings": {
            "network": "tcp",
            "tcpSettings": {
                "header": {
                    "type": "http",
                    "request": {
                        "path": ["/"]
                    }
                }
            }
        }
    },
    "outbound": {
        "protocol": "freedom"
    },
    "outboundDetour": [
        {
            "protocol": "blackhole",
            "tag": "blocked"
        }
    ],
    "dns": {
        "servers": [
            "1.1.1.1",
            "1.0.0.1",
            "8.8.8.8",
            "8.8.4.4"
        ]
    },
    "routing": {
        "domainStrategy": "IPIfNonMatch",
        "strategy": "rules",
        "rules": [
            {
                "type": "field",
                "outboundTag": "freedom",
                "domain": ["geosite:google"]
            },
            {
                "type": "field",
              "domain": ["regexp:^.*\\.ir$"]
            },
            {
                "type": "field",
                "ip": ["geoip:private", "geoip:ir"]
            },
            {
                "type": "field",
                "network": "udp",
                "port": 53,
                "outboundTag": "blocked"
            }
        ]
    }
}
EOF
    
    echo "Restarting V2Ray to apply changes..."
    systemctl restart v2ray
    echo "V2Ray configured successfully with secure DNS settings!"
else
    echo "Configuration file not found. Please check your V2Ray installation."
fi
