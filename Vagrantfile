Vagrant.configure("2") do |config|
  config.ssh.shell = "bash -c 'BASH_ENV=/etc/profile exec bash'"
  config.vm.box = "precise64"
  config.vm.box_url = "http://files.vagrantup.com/precise64.box"

  config.vm.network :private_network, ip: "192.168.120.105"
  config.vm.hostname = "champs21.dev"

  # TODO: fix this hack that ensures 'apt-get update' runs before mysql
  # is provisioned with puppet
  config.vm.provision :shell, :inline => "apt-get update --fix-missing"
  # mounting current folder to /var/www
  config.vm.provision :shell, :inline => "rm -rf /var/www; mkdir /var/www; ln -fs /vagrant /var/www/champs21.com"

  # provision our sdev server with the necessary packages and services
  config.vm.provision :puppet do |puppet|
    puppet.manifests_path = "scripts/provision/puppet/manifests"
    puppet.module_path = "scripts/provision/puppet/modules"
    puppet.manifest_file  = "base.pp"
    puppet.options = ['--verbose']
  end

  # installer/updater for pecl mongo
  config.vm.provision :shell do |sh|
    sh.path = "scripts/provision/shell/install_phpmongo.sh"
  end

  # installing phpMyAdmin
  config.vm.provision :shell do |sh|
    sh.path = "scripts/provision/shell/install_phpmyadmin.sh"
  end


  # installing Champs21 School
  config.vm.provision :shell do |sh|
    sh.path = "scripts/provision/shell/install_champs21_school_free.sh"
  end

  # installing composer
  config.vm.provision :shell, :inline => "curl -sS https://getcomposer.org/installer | php; mv composer.phar /usr/local/bin/composer; cd /vagrant; COMPOSER_PROCESS_TIMEOUT=1200 composer -vvv install" 	

  # installing redis
  config.vm.provision :shell, :inline => "sudo apt-get install -y redis-server"

end
