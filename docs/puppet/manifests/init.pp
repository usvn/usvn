class external
{
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
	apache::mod {'dav_svn':}
	apache::mod {'authz_svn':}
	apache::vhost {'app':
		priority => '00',
		port     => '80',
		override => 'All',
		docroot  => '/vagrant/src/public',
		custom_fragment => '<Location />
	ErrorDocument 404 default
	DAV svn
	Require valid-user
	SVNParentPath /usvn/files/svn
	SVNListParentPath off
	AuthType Basic
	AuthName "USVN - Lideran?a"
	AuthUserFile /usvn/files/htpasswd
	AuthzSVNAccessFile /usvn/files/authz
</Location>',
	}
	class {'phpmyadmin':
		root_password => 'root',
		vhost_port    => '81',
	}
}

class local
{
	$files = [
		'/usvn',
		'/usvn/files',
	]
	file {$files:
		ensure => directory,
		owner  => 'www-data',
		group  => 'www-data',
		mode   => 0777,
	}
}

class {'external':}
class {'local':}
