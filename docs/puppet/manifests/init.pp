class {'env':
	utils        => ['git','subversion'],
	link_in_home => ['workspace=/vagrant'],
}
class {'vim':
	tabstop  => 4,
	opt_misc => ['number','nowrap'],
}
class {'php':
	modules => ['apc','xdebug','mysqlnd'],
}
class {'apache':
	default_mods  => true,
	default_vhost => false,
	mpm_module    => 'prefork',
}
apache::mod {'php5':}
apache::mod {'rewrite':}
apache::mod {'dav':}
apache::mod {'dav_fs':}
apache::mod {'dav_svn':}
apache::mod {'authz_svn':}
apache::vhost {'app':
	priority => '00',
	port     => '80',
	override => 'All',
	docroot  => '/vagrant/src/public',
}
class {'phpmyadmin':
	root_password => 'root',
	vhost_port    => '81',
}
