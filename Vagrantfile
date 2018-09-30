# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "ubuntu/xenial64"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  config.vm.network :forwarded_port, guest: 80, host: 8080

  # Hostname
  config.vm.host_name = "usvn.dev"

  config.vm.synced_folder "./", "/var/sites/usvn", id: "vagrant-root",
    owner: "vagrant",
    group: "www-data",
    mount_options: ["dmode=775,fmode=664"]

  config.vm.provision "shell", inline: "
  apt-get update
  DEBIAN_FRONTEND=noninteractive apt-get install -y apache2 php libapache2-mod-php mysql-server php-xml php-mysql subversion libapache2-svn zend-framework

cat > /etc/apache2/sites-available/usvn.conf <<EOF
Alias /usvn /var/sites/usvn/src/public
<Directory \"/var/sites/usvn/src/public\">
Options +SymLinksIfOwnerMatch
AllowOverride All
Require all granted
</Directory>
EOF

a2enmod rewrite
a2ensite usvn
/etc/init.d/apache2 restart
"

end
