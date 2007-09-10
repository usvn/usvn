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
	private $hooks = array("post-commit",
		"post-lock",
		"post-revprop-change",
		"post-unlock",
		"pre-commit",
		"pre-lock",
		"pre-revprop-change",
		"pre-unlock",
		"start-commit");

	/**
	* @string Repository path
	*/
	public function __construct($path)
	{
		$USVN_path = getcwd();
		foreach ($this->hooks as $hook) {
			$file = $path . DIRECTORY_SEPARATOR . 'hooks' . DIRECTORY_SEPARATOR . $hook;
			file_put_contents($file,
<<<EOF
#!/bin/sh
cd $USVN_path
php hooks/$hook.php "\$@"
exit \$?

EOF
);
			chmod($file, 0700);
		}
	}
}