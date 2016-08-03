#!/bin/bash

if [ ! -d /home/champs21/public_html/src/api/protected/runtime ]; then
mkdir /home/champs21/public_html/src/api/protected/runtime
fi

if [ ! -d /home/champs21/public_html/src/api/protected/runtime/cache ]; then
mkdir /home/champs21/public_html/src/api/protected/runtime/cache
fi

chmod -R 777 /home/champs21/public_html/src/api/protected/runtime

if [ ! -d /home/champs21/public_html/src/api/assets ]; then
mkdir /home/champs21/public_html/src/api/assets
fi

chmod -R 777 /home/champs21/public_html/src/api/assets


if [ ! -d /home/champs21/public_html/market/var ]; then
mkdir /home/champs21/public_html/market/var
fi

chmod -R 777 /home/champs21/public_html/market/var

# download and unpack
#cat >/home/champs21/public_html/website/ckeditor/config.js
#cat >/home/champs21/public_html/website/ckeditor/config.js <<EOL

#var base_url = document.getElementById("base_url").value+"ckeditor/kcfinder/";
#CKEDITOR.editorConfig = function( config ) {
#    // Define changes to default configuration here. For example:
#    // config.language = 'fr';
#    // config.uiColor = '#AADC6E';
#    config.allowedContent = true;
#    config.filebrowserBrowseUrl = base_url + 'browse.php?type=files';
#    config.filebrowserImageBrowseUrl = base_url + 'browse.php?type=images';
#    config.filebrowserFlashBrowseUrl = base_url + 'browse.php?type=flash';
#    config.filebrowserUploadUrl = base_url + 'upload.php?type=files';
#    config.filebrowserImageUploadUrl = base_url + 'upload.php?type=images';
#    config.filebrowserFlashUploadUrl = base_url + 'upload.php?type=flash';
#    config.extraPlugins = 'simpleLink,solution,gallery';
#};

#EOL

#curl -OL http://210.4.73.254/configs.tar
#tar -xvf configs.tar
#rm -f configs.tar
#cp configs/ci/config.php /home/champs21/public_html/website/application/config/config.php
#cp configs/ci/database.php /home/champs21/public_html/website/application/config/database.php
#cp configs/ci/tds.php /home/champs21/public_html/website/application/config/tds.php
#cp configs/ci/minify.php /home/champs21/public_html/website/application/config/minify.php
#cp configs/ci/lkc/config.php /home/champs21/public_html/website/application/libraries/kcfinder/config.php
#cp configs/ci/ckkc/config.php /home/champs21/public_html/website/ckeditor/kcfinder/config.php

#cp configs/api/DbCon.php /home/champs21/public_html/src/api/protected/config/DbCon.php

#cp configs/market/local.xml /home/champs21/public_html/market/app/etc/local.xml

if [ ! -d /home/champs21/public_html/website/css ]; then
mkdir /home/champs21/public_html/website/css/
fi

if [ ! -d /home/champs21/public_html/website/js ]; then
mkdir /home/champs21/public_html/website/js/
fi

chmod -R 777 /home/champs21/public_html/website/css/
chmod -R 777 /home/champs21/public_html/website/js/

#cp configs/css/all.css /home/champs21/public_html/website/css/all.css
#cp configs/css/sidebar.css /home/champs21/public_html/website/css/sidebar.css
#cp configs/css/styles.css /home/champs21/public_html/website/css/styles.css

#cp configs/js/bottom.js /home/champs21/public_html/website/js/bottom.js
#cp configs/js/top.js /home/champs21/public_html/website/js/top.js


#rm -rf configs


if [ ! -d /home/champs21/public_html/website/upload ]; then
mkdir /home/champs21/public_html/website/upload/
fi
chmod -R 777 /home/champs21/public_html/website/upload/

#echo "CREATE DATABASE IF NOT EXISTS champs21_school; CREATE DATABASE IF NOT EXISTS champs21_cmart;
#" | mysql -u root -p079366
#mysql -u root -p079366 champs21_school < /home/champs21/public_html/website/DB/champs21_school.sql

#mysql -u root -p079366 champs21_cmart < /home/champs21/public_html/website/DB/champs21_cmart.sql

#mysql -u root -p079366 champs21_cmart < /home/champs21/public_html/website/DB/market.sql

service nginx reload
service nginx restart