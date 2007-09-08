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
	/**
	* @string Repository path
	*/
	public function __construct($path)
	{
		touch($path . DIRECTORY_SEPARATOR . "post-commit");
		touch($path . DIRECTORY_SEPARATOR . "post-lock");
		touch($path . DIRECTORY_SEPARATOR . "post-revprop-change");
		touch($path . DIRECTORY_SEPARATOR . "post-unlock");
		touch($path . DIRECTORY_SEPARATOR . "pre-commit");
		touch($path . DIRECTORY_SEPARATOR . "pre-lock");
		touch($path . DIRECTORY_SEPARATOR . "pre-revprop-change");
		touch($path . DIRECTORY_SEPARATOR . "pre-unlock");
		touch($path . DIRECTORY_SEPARATOR . "start-commit");
	}
}