#!/usr/bin/env php
<?php

function myPassThru(array $args, $infile=null)
{
	$command = '';
	foreach ($args as $arg) {
		if (empty($command))
			$command .= escapeshellcmd($arg);
		else
			$command .= ' ' . escapeshellarg($arg);
	}
	if (!empty($infile))
		$command .= ' <' . escapeshellarg($infile);
	echo "$command\n";
	passthru($command);
}

function askParameter($message, $defaultValue = null)
{
	echo $message . "?\n";
	if (!empty($defaultValue))
		echo '[' . $defaultValue . ']: ';
	$value = trim(fgets(STDIN, 128), "\n");
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

	// myPassThru(array("mysql",
	// 	'-h', $values['dbHost'],
	// 	'-u', $values['dbUser'],
	// 	'-p', $values['dbPwd'],
	// 	'-D', $values['dbDB']), $sampleDir . '/SQL/mysql.sql');
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
	mysql_close($conn) or die(mysql_error());
}
