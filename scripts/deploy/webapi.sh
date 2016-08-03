#!/bin/bash

hosts='54.208.126.11'

if [ $2 -eq 0 ] ; then
  echo "starting PHP lint checks.."
  find $1/v2 -type f -name *.php -exec php -l {} \; | grep "Errors parsing" && echo - errors found  && exit 1
  #else
  echo no errors found
fi

chown -R  www-data: $1
find $1 -type f -exec chmod 664 {} \;
find $1 -type d -exec chmod 775 {} \;

if [ -f $1/composer.json ] ; then 
  echo "Running composer..."
  cd $1/
  rm -rf ./vendor/*
  composer update || exit 1 
fi


echo "Syncing to Apis web root..."
for host in $hosts; do
    rsync -aqP  $1/ $host:/var/www/cysoco/ --exclude /storage --exclude /scripts --stats
    ssh $host rm -rf /var/www/cysoco/admin_settings/views_c/
    ssh $host mkdir -p /var/www/cysoco/admin_settings/views_c/
    ssh $host chown -R  www-data: /var/www/cysoco/admin_settings/views_c/
    ssh $host chmod 777 /var/www/cysoco/admin_settings/views_c/

    ssh $host rm -rf /var/www/cysoco/cache_manager/views_c/
    ssh $host mkdir -p /var/www/cysoco/cache_manager/views_c/
    ssh $host chown -R www-data: /var/www/cysoco/cache_manager/views_c/
    ssh $host chmod 777 /var/www/cysoco/cache_manager/views_c/

    ssh $host rm -rf /var/www/cysoco/staff_watched/views_c/
    ssh $host mkdir -p /var/www/cysoco/staff_watched/views_c/
    ssh $host chown -R www-data: /var/www/cysoco/staff_watched/views_c/
    ssh $host chmod 777 /var/www/cysoco/staff_watched/views_c/

    ssh $host rm -rf /var/www/cysoco/staffcomlogin/views_c/
    ssh $host mkdir -p /var/www/cysoco/staffcomlogin/views_c/
    ssh $host chown -R www-data: /var/www/cysoco/staffcomlogin/views_c/
    ssh $host chmod 777 /var/www/cysoco/staffcomlogin/views_c/

    ssh $host rm -rf /var/www/cysoco/v2/views_c/
    ssh $host mkdir -p /var/www/cysoco/v2/views_c/
    ssh $host chown -R www-data: /var/www/cysoco/v2/views_c/
    ssh $host chmod 777 /var/www/cysoco/v2/views_c/
    echo "restarting php on $host"
    ssh $host /etc/init.d/php5-fpm restart
done

rm -fr /tmp/production/*
