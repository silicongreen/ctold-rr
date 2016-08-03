#!/bin/bash


echo "Starting staging mongo export..."
mongoexport -h 192.168.1.6  -u admin -p iepeiYa0  --db td_stage  --collection timeuse -o /home/cysoco/export/export.json

echo "Starting mysql export..."
mysqldump cysoco_db  -r  /home/cysoco/export/cysoco_db.sql

#ssh 5.9.63.27 service nginx stop

scp /home/cysoco/export/* cysoco@5.9.63.28:/home/cysoco/import/

echo "Importing mongodb to testing..."
mongoimport -h 192.168.1.6 -u admin -p iepeiYa0 --db td_test --collection timeuse --file /home/cysoco/export/export.json

echo "Import mysqldump to testing..."
ssh cysoco@5.9.63.28 'mysql --default-character-set=utf8 cysoco_db < /home/cysoco/import/cysoco_db.sql'

#ssh 5.9.63.27 service nginx start        
                                         
rm -f /home/cysoco/export/cysoco_db.sql
rm -f  /home/cysoco/export/export.json   
