#!/bin/bash
# Get su permission
sudo su

# Install/Update Phalcon
echo
echo 'Building PHP extension for MongoDB'
echo

# Run build script
pecl install mongo

if [ ! -f '/etc/php5/mods-available/mongo.ini' ]; then
    # Ensure extension is loaded for php-cli and php-fpm
    echo 'extension=mongo.so' > /etc/php5/mods-available/mongo.ini
    ln -s /etc/php5/mods-available/mongo.ini /etc/php5/cli/conf.d/mongo.ini
    ln -s /etc/php5/mods-available/mongo.ini /etc/php5/fpm/conf.d/mongo.ini
fi

# Restart php5-fpm and nginx
echo
echo 'Restarting web services'
echo
service php5-fpm restart
service nginx restart

echo
echo 'PHP extension for MongoDB has been updated'
echo
