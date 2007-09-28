<?php
/**
 * Install hooks into a subversion repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.8
 * @package hooks
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_InstallHooks
{
	/**
	* @string Repository path
	*/
	public function __construct($path)
	{
		$USVN_path = getcwd();
		if (!file_exists($USVN_path . 'hooks/start-commit.php')) { //Usefull only for test because test are run outside of www
			$USVN_path .= DIRECTORY_SEPARATOR . 'www';
		}
		foreach (USVN_SVNUtils::$hooks as $hook) {
			$file = $path . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR . $hook;
			file_put_contents($file,
<<<EOF
#!/bin/sh
cd $USVN_path
php hooks-cmd/$hook.php "\$@"
exit \$?

EOF
);
			chmod($file, 0700);
		}
	}
}