#!/usr/bin/env php
<?php

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
	templateToFile($htaccessFile, $sampleDir . '/htaccess', $values);

	echo "Database\n";
	$values['dbAdapter'] = askParameter('Database Adapter', 'MYSQLI');
	$values['dbHost']    = askParameter('Database Host', 'localhost');
	$values['dbDB']      = askParameter('Database Name', 'usvn');
	$values['dbUser']    = askParameter('Database Login', 'usvn');
	$values['dbPwd']     = askParameter('Database Password');
	
	echo "Repositories\n";
	$values['reposRoot']  = askParameter('Repository Path');
	$values['reposHTPwd'] = askParameter('Repository htpasswd file path');
	$values['reposAuthz'] = askParameter('Repository authz file path');
	$values['reposURL']   = askParameter('Repository external url');
	templateToFile($configFile, $sampleDir . '/config.ini', $values);
}
