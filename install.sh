#!/bin/bash

# Download and extract the repository
wget https://github.com/afshinakhgar/mass_v2ray_uuid_user_clientgenerator/archive/master.zip -O /tmp/repo.zip
unzip /tmp/repo.zip -d /var/www/main/public

# Rename the extracted folder to a more manageable name
mv /var/www/main/public/mass_v2ray_uuid_user_clientgenerator-master /var/www/main/public/repo

# Change directory to /var/www/main/public/repo
cd /var/www/main/public/repo

# Update Composer
composer install

# Change the permissions of the public folder to 775
chmod -R 775 /var/www/main/public/repo

# Change the permissions of the client_connection folder to 777
chmod -R 777 /var/www/main/public/repo/client_connection
