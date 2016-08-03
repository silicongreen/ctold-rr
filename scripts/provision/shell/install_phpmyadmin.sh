#!/bin/bash
# Get su permission
sudo su

#rm -R /var/www/phpmyadmin/

# download and unpack
curl -OL https://github.com/phpmyadmin/phpmyadmin/archive/RELEASE_4_0_5.tar.gz
tar -xzf RELEASE_4_0_5.tar.gz
rm -f RELEASE_4_0_5.tar.gz
mv phpmyadmin-RELEASE_4_0_5/ /var/www/phpmyadmin/

# adding config
cat >/var/www/phpmyadmin/config.inc.php <<EOL
<?php
\$cfg['blowfish_secret'] = 'o9dan9018n19023n';  // use here a value of your choice

\$i=0;
\$i++;
\$cfg['Servers'][\$i]['user']          = 'root';
\$cfg['Servers'][\$i]['password']      = '079366'; // use here your password
\$cfg['Servers'][\$i]['auth_type']     = 'config';
EOL

# adding nginx host
cat >/etc/nginx/sites-enabled/phpmyadmin <<EOL
server {

    listen 80 ;

    server_name  phpmyadmin.champs21.dev;

    access_log  /var/log/nginx/api_access.log;
    error_log   /var/log/nginx/api_error.log;

    root /var/www/phpmyadmin;


    index index.php;

    location ~ .*\.(php)\$ {
        if (!-e \$document_root\$document_uri){return 404;}

        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        fastcgi_param APPLICATION_ENV development;
        include /etc/nginx/includes/fastcgi_params.inc;
        fastcgi_cache off;
    }

}
EOL

service nginx restart