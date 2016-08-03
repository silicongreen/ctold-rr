#!/bin/bash

hosts='5.9.63.27'


find $1 -type f -exec chmod 664 {} \;
find $1 -type d -exec chmod 775 {} \;

if [ -f $1/composer.json ] ; then 
  echo "Running composer..."
  cd $1/
  rm -rf ./vendor/*
  export APPLICATION_ENV=testing
  composer update || exit 1 
fi

echo "Syncing to testing..."

for host in $hosts; do
    rsync -aqP  $1/ root@$host:/var/www/cysoco/ --exclude /storage --exclude /scripts --stats
    ssh root@$host rm -rf /var/www/cysoco/admin_settings/views_c/
    ssh root@$host mkdir -p /var/www/cysoco/admin_settings/views_c/
  
    ssh root@$host rm -rf /var/www/cysoco/cache_manager/views_c/
    ssh root@$host mkdir -p /var/www/cysoco/cache_manager/views_c/
  
    ssh root@$host rm -rf /var/www/cysoco/staff_watched/views_c/
    ssh root@$host mkdir -p /var/www/cysoco/staff_watched/views_c/
  
    ssh root@$host rm -rf /var/www/cysoco/staffcomlogin/views_c/
    ssh root@$host mkdir -p /var/www/cysoco/staffcomlogin/views_c/
  
    ssh root@$host rm -rf /var/www/cysoco/v2/views_c/
    ssh root@$host mkdir -p /var/www/cysoco/v2/views_c/
    echo "...Done."
    echo "Restarting php and supervisor"
    ssh root@$host /etc/init.d/php5-fpm restart
    ssh root@$host  /etc/init.d/supervisor stop
    sleep 5
    ssh root@$host /etc/init.d/supervisor start

    
    
done
