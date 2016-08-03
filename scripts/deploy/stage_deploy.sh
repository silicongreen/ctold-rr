#!/bin/bash

if [ $2 -eq 0 ] ; then
  #minify js/css except *.min*, and checking JS for errors
  #cd $1/  && /usr/bin/phing
  
  echo "starting PHP lint checks.."
  find $1/v2 -type f -name *.php -exec php -l {} \; | grep "Errors parsing" && echo - errors found  && exit 1
  #else
  echo no errors found
  
fi


echo "Running composer..."
  cd $1/
  rm -rf ./vendor/*
  composer update || exit 1 
  
echo "Syncing to web root... "
rsync -aP  $1/ /var/www/cysoco/ --exclude /storage --exclude /scripts  --stats

echo "Clearing views_c"

rm -rf /var/www/cysoco/admin_settings/views_c/
mkdir -p /var/www/cysoco/admin_settings/views_c/
#chown -R  www-data: /var/www/cysoco/admin_settings/views_c/
#chmod 777 /var/www/cysoco/admin_settings/views_c/

rm -rf /var/www/cysoco/cache_manager/views_c/
mkdir -p /var/www/cysoco/cache_manager/views_c/
#chown -R www-data: /var/www/cysoco/cache_manager/views_c/
#chmod 777 /var/www/cysoco/cache_manager/views_c/


rm -rf /var/www/cysoco/staff_watched/views_c/
mkdir -p /var/www/cysoco/staff_watched/views_c/
#chown -R www-data: /var/www/cysoco/staff_watched/views_c/
#chmod 777 /var/www/cysoco/staff_watched/views_c/

rm -rf /var/www/cysoco/staffcomlogin/views_c/
mkdir -p /var/www/cysoco/staffcomlogin/views_c/
#chown -R www-data: /var/www/cysoco/staffcomlogin/views_c/
#chmod 777 /var/www/cysoco/staffcomlogin/views_c/

rm -rf /var/www/cysoco/v2/views_c/
mkdir -p /var/www/cysoco/v2/views_c/
#chown -R www-data: /var/www/cysoco/v2/views_c/
#chmod 777 /var/www/cysoco/v2/views_c/

#/etc/init.d/php5-fpm restart

#echo "restarting supervisor:"
#/etc/init.d/supervisor stop
#sleep 5
#/etc/init.d/supervisor start

#echo "Fixing permissions on  /tmp/staging/"
#chown -R www-data:  /tmp/staging/ 
#chmod -R 777  /tmp/staging/




