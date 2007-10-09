<?php
/**
 * Send an email after each commit
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.8
 * @package usvn
 * @subpackage hooks
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

 class DisallowEmptyLogMessage extends USVN_AbstractHook
 {
	/**
	* Pre commit hook
	*
	* @string the path to this repository
	* @string subversion transaction
	* @return string|0 Return 0 if no problem else return error message
	*/
	public function preCommit($repos , $txn)
	{
		$message = USVN_SVNUtils::svnLookTransaction("log", $repos, $txn);
		$message = trim($message);
		if (strlen($message) == 0) {
			return T_("Empty log messages are allowed.");
		}
		return 0;
	}
 }