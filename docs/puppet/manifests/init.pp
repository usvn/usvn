##
 # Development environment provided by external modules of Puppet Forge.
##
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
	apache::vhost {'usvn':
		priority => '00',
		port     => '80',
		override => 'All',
		docroot  => '/vagrant/src/public',
	}
	apache::vhost {'svn':
		priority => '01',
        port     => '81',
        override => 'All',
		docroot  => '/vagrant/src/public',
		custom_fragment => '<Location /svn>
	ErrorDocument 404 default
	DAV svn
	Require valid-user
	SVNParentPath /var/lib/usvn/svn
	SVNListParentPath off
	AuthType Basic
	AuthName "USVN"
	AuthUserFile /var/lib/usvn/htpasswd
	AuthzSVNAccessFile /var/lib/usvn/authz
</Location>',
	}
	class {'phpmyadmin':
		root_password => 'root',
		vhost_port    => '82',
	}
}

##
 # Development environment provided by local rules.
##
class local
{
	$usvn = '/var/lib/usvn'
	file {$usvn:
		ensure => directory,
		owner  => 'www-data',
		group  => 'www-data',
		mode   => 0755,
	}
	file {'/usvn':
		ensure => link,
		target => $usvn,
	}
}

class {'external':}
class {'local':}
