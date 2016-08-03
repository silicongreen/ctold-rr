$ar_databases = ['activerecord_unittest', 'activerecord_unittest2']
$as_vagrant = 'sudo -u vagrant -H bash -l -c'
$home = '/home/vagrant'

$ruby_version  = '1.8.7'
$rails_version = '2.3.5'
$gem_version   = '1.3.7'
$rake_version  = '0.8.7'
$mysql_version = '2.8.1'
$dcau_version  = '0.5.1'
$i18n_version  = '0.4.2'
$prawn_version = '0.6.3'
$noko_version  = '1.5.6'
$fcsv_version  = '1.5.3'
$oauth_version = '0.6.1'

Exec { path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/" ] }

class { 'apt': always_apt_update => true }

class system::update {
  $packages = [ "build-essential" ]
  package { $packages:
    ensure  => present,
    require => Class['apt::update'],
  }
}

class git::install {
  $packages = [ "git" ]
  package { $packages:
    ensure  => latest,
    require => Class['apt::update'],
  }
}

class php::install {
  apt::ppa { "ppa:ondrej/php5": }
  apt::key { "ondrej": key => "E5267A6C" }

  $packages = [ "php5-fpm", "php5-cli", "php5-dev", "php5-gd", "php5-curl", "php5-mcrypt", "php5-sqlite", "php5-mysql", "php5-mysqlnd", "php5-memcached", "php5-memcache", "php5-intl",  "php5-tidy", "php5-imap", "php5-xdebug", "memcached"]
  package { $packages:
    ensure  => latest,
    require => Class['apt::update'],
  }
}

exec { 'apt-get update':
  command => "/usr/bin/apt-get update -y",
  onlyif  => "/bin/bash -c 'exit $(( $(( $(date +%s) - $(stat -c %Y /var/lib/apt/lists/$( ls /var/lib/apt/lists/ -tr1|tail -1 )) )) <= 604800 ))'"
}

include system::update
include git::install
include php::install

# php-fpm configuration
exec { 'php-fpm update':
  command => '/bin/echo "security.limit_extensions = .php .html" >> /etc/php5/fpm/php-fpm.conf; ',
  require => [
    Class['php::install']
  ],
}
exec { 'php-fpm update fpm':
  command => '/bin/echo "date.timezone = UTC" >> /etc/php5/fpm/php.ini',
  require => [
    Class['php::install']
  ],
}

exec { 'php-cli update cli':
  command => '/bin/echo "date.timezone = UTC" >> /etc/php5/cli/php.ini',
  require => [
    Class['php::install']
  ],
}


include nginx
include nginx::fcgi

nginx::fcgi::site {"website":
  root            => "/var/www/champs21.com/website",
  fastcgi_pass    => "unix:/var/run/php5-fpm.sock",
  server_name     => ["champs21.dev", "www.champs21.dev"],
  template        => "website"
}

nginx::fcgi::site {"api":
  root            => "/var/www/champs21.com/src/api/",
  fastcgi_pass    => "unix:/var/run/php5-fpm.sock",
  server_name     => ["api.champs21.dev"],
  template        => "api"
}

nginx::fcgi::site {"market":
  root            => "/var/www/champs21.com/market",
  fastcgi_pass    => "unix:/var/run/php5-fpm.sock",
  server_name     => ["market.champs21.dev"],
  template        => "market"
}

nginx::fcgi::site {"paid":
  root            => "/var/www/champs21.com",
  fastcgi_pass    => "unix:/var/run/php5-fpm.sock",
  server_name     => ["school.champs21.dev"],
  template        => "dashboard"
}

class { 'mysql::server':
  config_hash => { 'root_password' => '079366' }
}

mysql::db { 'dbname':
  user     => 'dbuser',
  password => 'dbpass',
  host     => 'localhost',
  grant    => ['all'],
}

# MongoDB
class { 'mongodb':
  enable_10gen => true
}

# sudo pecl install mongo

# standard staff
package { 'vim':  ensure => installed }
package { 'mc':   ensure => latest }
package { 'wget': ensure => latest }
package { 'curl': ensure => latest }
package { 'htop':  ensure => latest }
