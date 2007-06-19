#!/usr/bin/php
<?php
include_once('../www/USVN/DirectoryUtils.php');
include_once('../www/Zend/Exception.php');
include_once('../www/USVN/Exception.php');
function T_($str)
{
	return $str;
}

function removeTest($remove_path)
{
	if (($path = realpath($remove_path)) !== FALSE) {
		$dh = opendir($path);
		while (($file = readdir($dh)) !== false) {
			if ($file != '.' && $file != '..') {
				if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
					removeTest($path . DIRECTORY_SEPARATOR . $file);
				}
				else {
					if (preg_match('/.*Test\.php/', $file)) {
						unlink($path . DIRECTORY_SEPARATOR . $file);
					}
				}
			}
		}
		closedir($dh);
	}
}


if (!isset($argv[1])) {
	echo "Usage: release.php tag\n";
}
$version = $argv[1];
try {
	USVN_DirectoryUtils::removeDirectory("/tmp/usvn$version");
}
catch (Exception $e) {
}
mkdir("/tmp/usvn$version");
chdir("/tmp/usvn$version");
`svn export https://svn.usvn.info/usvn/tags/$version/www usvn`;
chdir("usvn");
USVN_DirectoryUtils::removeDirectory("bugs");
USVN_DirectoryUtils::removeDirectory("USVN/Test");
removeTest('.');
chdir('..');
`tar -czf usvn-$version.tgz usvn`;
`zip -r usvn-$version.zip usvn`
?>
