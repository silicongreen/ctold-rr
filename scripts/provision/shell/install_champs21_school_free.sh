#!/bin/bash
# Get su permission
sudo su

if [ ! -d /vagrant/src/api/protected/runtime ]; then
mkdir /vagrant/src/api/protected/runtime
fi

if [ ! -d /vagrant/src/api/protected/runtime/cache ]; then
mkdir /vagrant/src/api/protected/runtime/cache
fi

chmod -R 777 /vagrant/src/api/protected/runtime

#if [ ! -d /vagrant/src/api/assets ]; then
mkdir /vagrant/src/api/assets
#fi

chmod -R 777 /vagrant/src/api/assets


#if [ ! -d /vagrant/market/var ]; then
mkdir /vagrant/market/var
#fi
chmod -R 777 /vagrant/market/var

# download and unpack
cat >/vagrant/website/ckeditor/config.js
cat >/vagrant/website/ckeditor/config.js <<EOL

var base_url = document.getElementById("base_url").value+"ckeditor/kcfinder/";
CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';
    config.allowedContent = true;
    config.filebrowserBrowseUrl = base_url + 'browse.php?type=files';
    config.filebrowserImageBrowseUrl = base_url + 'browse.php?type=images';
    config.filebrowserFlashBrowseUrl = base_url + 'browse.php?type=flash';
    config.filebrowserUploadUrl = base_url + 'upload.php?type=files';
    config.filebrowserImageUploadUrl = base_url + 'upload.php?type=images';
    config.filebrowserFlashUploadUrl = base_url + 'upload.php?type=flash';
    config.extraPlugins = 'simpleLink,solution,gallery';
};

EOL

curl -OL http://210.4.73.254/configs.tar
tar -xvf configs.tar
rm -f configs.tar
cp configs/ci/config.php /vagrant/website/application/config/config.php
cp configs/ci/database.php /vagrant/website/application/config/database.php
cp configs/ci/tds.php /vagrant/website/application/config/tds.php
cp configs/ci/minify.php /vagrant/website/application/config/minify.php
cp configs/ci/lkc/config.php /vagrant/website/application/libraries/kcfinder/config.php
cp configs/ci/ckkc/config.php /vagrant/website/ckeditor/kcfinder/config.php

cp configs/api/DbCon.php /vagrant/src/api/protected/config/DbCon.php

cp configs/market/local.xml /vagrant/market/app/etc/local.xml

if [ ! -d /vagrant/website/css ]; then
mkdir /vagrant/website/css/
fi

if [ ! -d /vagrant/website/js ]; then
mkdir /vagrant/website/js/
fi

chmod -R 777 /vagrant/website/css/
chmod -R 777 /vagrant/website/js/

cp configs/css/all.css /vagrant/website/css/all.css
cp configs/css/sidebar.css /vagrant/website/css/sidebar.css
cp configs/css/styles.css /vagrant/website/css/styles.css

cp configs/js/bottom.js /vagrant/website/js/bottom.js
cp configs/js/top.js /vagrant/website/js/top.js


rm -rf configs


if [ ! -d /vagrant/website/upload ]; then
mkdir /vagrant/website/upload/
fi
chmod -R 777 /vagrant/website/upload/

echo "CREATE DATABASE IF NOT EXISTS champs21_school; CREATE DATABASE IF NOT EXISTS champs21_cmart;
" | mysql -u root -p079366
mysql -u root -p079366 champs21_school < /vagrant/website/DB/champs21_school.sql

mysql -u root -p079366 champs21_cmart < /vagrant/website/DB/champs21_cmart.sql

mysql -u root -p079366 champs21_cmart < /vagrant/website/DB/market.sql


service nginx reload
service nginx restart