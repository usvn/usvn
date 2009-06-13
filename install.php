#!/usr/bin/env php
<?php
# Apache 2 pour windows (VM bilan tech)
# http://apache.crihan.fr/dist/httpd/binaries/win32/apache_2.2.11-win32-x86-openssl-0.9.8i.msi

# Subversion setup
# http://subversion.tigris.org/files/documents/15/45953/Setup-Subversion-1.6.2.msi

# PHP Installer
# http://fr.php.net/distributions/php-5.2.9-2-win32-installer.msi

# MySQL
# http://mir2.ovh.net/ftp.mysql.com/Downloads/MySQL-5.1/mysql-5.1.35-win32.msi

function askParameter($message, $defaultValue = null)
{
	echo $message . "?\n";
	if (!empty($defaultValue))
		echo '[' . $defaultValue . ']: ';
	$value = trim(fgets(STDIN, 128), "\r\n");
	return (empty($value) ? $defaultValue : $value);
}

function templateToFile($destPath, $srcPath, $values)
{
	if (file_exists($destPath))
	{
		echo "file already exist: $destPath\n";
		return;
	}
	$search = array();
	$replace = array();
	foreach ($values as $key => $value)
	{
		$search[] = '${' . $key . '}';
		$replace[] = $value;
	}
	$content = file_get_contents($srcPath);
	$content = str_replace($search, $replace, $content);
	echo "\ncreating $destPath\n";
	// echo "####\n";
	// echo $content;
	file_put_contents($destPath, $content);
	// echo "####\n\n";
}


{
	$values = array();
	$sampleDir = "./install";
	$htaccessFile = "./public/.htaccess";
	$configFile = "./config/config.ini";
	$apacheConf = askParameter('apache conf', '/etc/apache2/sites-available/usvn');
	$values['urlBase'] = askParameter('base url', '/usvn');
	$values['dir'] = realpath(getcwd());

	templateToFile($apacheConf, $sampleDir . '/usvn.conf', $values);
	echo "enable/include this new file and restart apache\n";
	echo "check that the folowing modules are enabled:\n";
	echo " - mod_php5\n";
	echo " - mod_authz_svn\n";
	echo " - mod_dav_svn\n";
	echo " - mad_rewrite\n";
	templateToFile($htaccessFile, $sampleDir . '/htaccess', $values);

	echo "Database\n";
	$values['dbAdapter'] = askParameter('Database Adapter', 'MYSQLI');
	$values['dbHost']    = askParameter('Database Host', 'localhost');
	$values['dbUser']    = askParameter('Database Login', 'usvn');
	$values['dbPwd']     = askParameter('Database Password');
	$values['dbDB']      = askParameter('Database Name', $values['dbUser']);
	
	echo "Repositories\n";
	$values['reposRoot']  = askParameter('Repository Path');
	$values['reposHTPwd'] = askParameter('Repository htpasswd file path', $values['reposRoot'] . '/htpasswd');
	$values['reposAuthz'] = askParameter('Repository authz file path', $values['reposRoot'] . '/authz');
	$values['reposURL']   = askParameter('Repository external url');
	templateToFile($configFile, $sampleDir . '/config.ini', $values);

	echo "setup database\n";
	$conn = mysql_connect($values['dbHost'], $values['dbUser'], $values['dbPwd'], true) or die(mysql_error());
	mysql_select_db($values['dbDB'], $conn) or die(mysql_error());
	$requests = file_get_contents($sampleDir . '/SQL/mysql.sql');
	foreach (split(';', $requests) as $req) {
		$result = mysql_query($req, $conn) or die(mysql_error());
		$reqLines = split("\n", $req);
		foreach ($reqLines as $line) {
			echo "  " . $line . "\n";
		}
		echo "OK\n";
		echo "\n";
	}
	// $values['usvnUser']    = askParameter('USVN Login', 'admin');
	// $values['usvnPwd']     = askParameter('USVN Password');
	mysql_close($conn) or die(mysql_error());
}
