#!/usr/bin/env php
<?php
function  scandirPHP($dir)
{
	 if (is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file != '.'
				&& $file != '..'
				&& $file != '.svn'
				&& $file != 'apidocs'
				&& $file != 'phing'
				&& $file != 'scripts'
				&& $file != 'tmp'
				&& $file != 'gettext') {
					if (preg_match ('/.*\.php$/', $file)) {
						$content = file_get_contents($dir."/".$file);
						if (!preg_match ('/\$LastChangedBy\$/', $content)
							|| !preg_match ('/<http://www.epitech.net>/', $content)) {
							echo "$dir/$file\n";
						}
					}
					if (is_dir($dir."/".$file)) {
						scandirPHP($dir."/".$file);
					}
				}
			}
			closedir($dh);
		}
	 }
}

scandirPHP(".");
?>