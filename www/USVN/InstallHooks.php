<?php
/**
 * Install hooks into a subversion repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.8
 * @package usvn
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
	private $hooks_without_stdin = array("post-commit",
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
		foreach ($this->hooks_without_stdin as $hook) {
			file_put_contents($path . DIRECTORY_SEPARATOR . $hook,
<<<EOF
#!/bin/sh
cd $USVN_path
php hooks/unix/$hook "\$@"
exit \$?

EOF
);
		}
	}
}