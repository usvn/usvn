##
 # Development environment provided by external modules of Puppet Forge.
##
class external
{
	include openjdk
	class {'env':
		utils        => ['git','curl','wget','nmap','telnet'],
		link_in_home => ['workspace=/vagrant'],
		aliases      => ['phing=clear ; phing','phpunit=clear ; phpunit'],
	}
	class {'vim':
		tabstop  => 4,
		plugins  => ['puppet'],
		opt_misc => ['number','nowrap'],
	}
	class {'php':
		modules => ['apc','memcache','memcached','xdebug','mysql'],
		extra   => [
			's3cmd','composer','phing','phpunit','phpdoc',
			'phpcs','phpdepend','phpmd','phpcpd','phpdcd',
		],
	}
	class {'zf':
		version => 1,
		zftool  => true,
	}
	class {'apache':
		default_mods  => true,
		default_vhost => false,
		mpm_module    => 'prefork',
	}
	apache::mod {'php5':}
	apache::mod {'rewrite':}
	apache::vhost {'app':
		priority => '00',
		port     => '80',
		override => 'FileInfo',
		docroot  => '/vagrant/src/App/public',
		setenv   => ['APPLICATION_ENV development'],
	}
	apache::vhost {'static':
		priority => '01',
		port     => '81',
		docroot  => '/vagrant/src/Static',
	}
	apache::vhost {'test':
		priority => '02',
		port     => '82',
		docroot  => '/vagrant/src/Test',
	}
	class {'phpmyadmin':
		root_password => 'root',
		vhost_port    => '83',
	}
}

##
 # Development environment provided by local modules.
##
class local
{
	include s3cmd
	include memcached
	include ssh
}

class {'external':}
class {'local':}
