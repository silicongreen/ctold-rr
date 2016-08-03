#!/bin/bash

chown -R  cysoco: $1
echo "Syncing to web root..."

rsync -aP  $1/ /var/www/cysoco/ --exclude /storage --exclude /scripts --stats

find /var/www/cysoco -type f -exec chmod 664 {} \;
find /var/www/cysoco -type d -exec chmod 775 {} \;
