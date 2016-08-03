#!/bin/bash

KEY=/tmp/test_db.pem

TESTING_DB_HOST=www.champs21.com

cat >$KEY <<EOL
-----BEGIN RSA PRIVATE KEY-----
MIIEoAIBAAKCAQEA0AHd+wJ/bvh/kN7hqdZZIlNpTPpHqG7zuSTRVxYsu5qp3VBF
cLxCey6rwiJawoMLlIhWV2XxuJ6QAWIfAWAoqo68RWf4wE6A8Z6S8C5AchnWfFG8
XplNM9PEuBsVyNAC1OlPmYmHgtNYxuaCXLRc9E96P8FMK3Czf80xXTE7WlM0RMWB
4XfDh5y7Gn4zytbtPvHnDFlmqOlhztLYlNdoB/EZZFfXXbZk7c9cvB8KSxt1OQom
uqgdtSwn5dc943tONVvCYdBv4cqWwhEYqxd7YQWlVJKfCZvC8vHWXE4RGRrDTPCC
D9gIok3BW7HHeolMdjiyZ3BCk19Mw1Sd1MX0CwIBIwKCAQBBX6wqUT3+Thl2rHLO
+jlFTWo8w7Ae/kyZRhXoK4pm2NZM3ra9B/ejDqsCf9NhtCg1/vaQfyAVc6maCOUs
UWvd1RaZeHLAGKwuri4uOmwGmmf7L6GaEuxgvuYN+eJGbUK38Y4LruFyQmxbxMni
nxXmYh8bWgH/BinJFJp8X+4VEkTEs+3a6xxGpbHrB3GQGO06wtJ1VBBAJrXi8Onh
vZ3CCFHhQlLeM4dZlmKglG7eoAGqI4SP1VKRkk2ZvyO+X/lnCLzlPH20ioXH/9oz
vZll1geoEC9fbm46FLsPEgpRJ0HSQkPYXoREp3j22DgaAuiWramFsYS+aInfQVQD
stVTAoGBAO9OFEPLPRpNzeJM0Lo3eRnFEVSDcqckLPxi7DHX3ocEzomaiW1iC4Af
+Jv5W2NwG2gxixLYM0/VSePZOgBxWxmnqveORSXedbddWN5qZ2338A/C/V7XQigN
Ji85Xy7Q6h3mGDvlv51d42a/ibZRtq16uQ8+CW2C1WUo1lMtWye3AoGBAN6E0dTp
TpUTM//KlcpryLIcAFH8T6MVtZcOLe0dQHaxCBblzNatBvBTsm+sjkYWpdkv9ssw
E95tyvAMJcu3S+x0ZnHLnEWtnB5z/oP5JjyZbJR61h3UKMfP5shQEpLo4khVecti
YAw7bMJzeBjWDy9KnOTdGrpVml93C4UnSQ5NAoGAWOJ8jjWLuVAZRWz9EfdY3a+Y
uP2fnSqxn5J0/JICMiZbV65tjwco19FcVzC7i1WGhclCSNP1zTH235nTtwWIPLqe
lnar+B9tjT/tzvRSTWq4QF5eG+mNmdj/jeIcChMVIQxSJOBOfEdxuHMHQ7fzZQGy
c2AvYzCYZ2bwhUtcXzUCgYBycDFmLtf05U3xQ51+C4vJUDqt0jeVs2S0Feu7xeaj
cPzYk3Co60Vlp2MU3GZtMDgJS94CGLnC2WENvRrAiicL5BdtuSvMECRnbtqqRZ6i
0o+c0XVubRxJgN0Wj5SGEV5uV9g8tkAGSnJyoci8UNSb+nyS9V4z8YKXfw09KiWS
UwKBgE/Zaj6GzNwBnGi+OcISWqzJL6uP5S6UgjLgQUyCMVu13CXq+xDRJyydXAnE
BpV9RaJNOpYckiCV1AXxl57fDSJl4EWB+m4d1fwvaH2PuKKViUFwuolTM5vs6TcM
FXpd4sGw8E+zWnrgH7mATAfpxihKwUfp7e1i/qtth4p546z0
-----END RSA PRIVATE KEY-----
EOL

chmod 600 $KEY

# use a unique directory for the export (in case more than one developer runs provision)
RAND=$RANDOM
REMOTEDIR=export/$RAND

# dump mysql and mongo, archive, scp and remove
ssh -i $KEY root@$TESTING_DB_HOST -o StrictHostKeyChecking=no "mkdir -p $REMOTEDIR; mysqldump champs21_school > $REMOTEDIR/champs21_school.sql"
ssh -i $KEY root@$TESTING_DB_HOST "tar -czvf $REMOTEDIR/champs21_testing.tar.gz $REMOTEDIR/champs21_school.sql"
scp -i $KEY root@$TESTING_DB_HOST:$REMOTEDIR/champs21_testing.tar.gz /tmp/
ssh -i $KEY root@$TESTING_DB_HOST "rm -rf $REMOTEDIR"

# extract locally, import and remove
tar -xzvf /tmp/champs21_testing.tar.gz -C /tmp/
rm -f /tmp/champs21_testing.tar.gz
echo "DROP DATABASE IF EXISTS champs21_school; create database champs21_school;" | mysql -u root -p079366
mysql -u root -p079366 champs21_school < /tmp/export/$RAND/champs21_school.sql
rm -rf /tmp/root/
rm -rf $KEY

