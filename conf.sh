#!/bin/bash

# نصب V2Ray و تنظیمات بهینه برای جلوگیری از نشت DNS
echo "Installing V2Ray with secure configurations..."

# نصب V2Ray
bash <(curl -sSL https://raw.githubusercontent.com/afshinakhgar/mass_v2ray_uuid_user_clientgenerator/master/install.sh)

# دریافت IP عمومی سرور
SERVER_IP=$(curl -s ifconfig.me)
echo "Detected server IP: $SERVER_IP"

# تنظیمات پیشرفته برای جلوگیری از نشت DNS
CONFIG_FILE="/etc/v2ray/config.json"

if [ -f "$CONFIG_FILE" ]; then
    echo "Updating V2Ray configuration for secure DNS..."
    
    # بکاپ گرفتن از کانفیگ موجود
    cp "$CONFIG_FILE" "$CONFIG_FILE.bak"
    
    # تغییرات در کانفیگ V2Ray
    cat > "$CONFIG_FILE" << EOF
{
    "inbounds": [
        {
            "port": 1080,
            "protocol": "socks",
            "settings": {
                "auth": "noauth",
                "udp": true,
                "ip": "127.0.0.1"
            }
        }
    ],
    "outbounds": [
        {
            "protocol": "vmess",
            "settings": {
                "vnext": [
                    {
                        "address": "$SERVER_IP",
                        "port": 9999,
                        "users": [
                            {
                                "id": "02749482-a9cd-4813-a4a9-f3d440f4ef5f",
                                "alterId": 64,
                                "security": "auto"
                            }
                        ]
                    }
                ]
            },
            "streamSettings": {
                "network": "ws",
                "wsSettings": {
                    "path": "/v2ray"
                }
            }
        },
        {
            "protocol": "dns",
            "settings": {}
        }
    ],
    "dns": {
        "servers": [
            "1.1.1.1",
            "1.0.0.1",
            "8.8.8.8",
            "8.8.4.4"
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
