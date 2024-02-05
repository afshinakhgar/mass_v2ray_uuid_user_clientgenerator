#!/bin/bash
# install server 

bash <(curl -L https://raw.githubusercontent.com/afshinakhgar/server_automate/master/server.sh) main



# Install v2ray
sudo apt install -y curl unzip
sudo bash -c "$(curl -sSL https://raw.githubusercontent.com/v2fly/fhs-install-v2ray/master/install-release.sh)"
sudo systemctl status v2ray
sudo systemctl restart v2ray
sudo systemctl enable v2ray



# Download and extract the repository
cd /var/www/main/public
wget https://github.com/afshinakhgar/mass_v2ray_uuid_user_clientgenerator/archive/master.zip



cd /var/www/main/public
ls


sudo unzip /var/www/public/master.zip -d /var/www/main/public

# Rename the extracted folder to a more manageable name
sudo mv /var/www/main/public/mass_v2ray_uuid_user_clientgenerator-master /var/www/main/public

# Change directory to /var/www/main/public/repo
cd /var/www/main/public

# Update Composer
composer install

# Change the permissions of the public folder to 775
sudo chmod -R 775 /var/www/main/public

# Change the permissions of the client_connection folder to 777
sudo chmod -R 777 /var/www/main/public/client_connection


mv /var/www/main/public/config.json /usr/local/etc/v2ray/config.json
sudo chmod -R 777 /usr/local/etc/v2ray/config.json

# Configure firewall
sudo ufw status
sudo ufw allow 9999
