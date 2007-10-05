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

 class HookNotifByMail extends USVN_AbstractHook
 {
 	/**
	* Post commit hook
	*
	* @string the path to this repository
	* @int the number of the revision just committed
	*/
	public function postCommit($repos , $rev)
	{
		$project_name = USVN_SVNUtils::getProjectName($repos);
		$mail = new Zend_Mail();
		$mail->setSubject("[$project_name] " . T_("Revision") . " $rev");
		$mail->addTo('noplay@localhost', 'Some Recipient');
		$mail->setFrom('nobody@usvn.info', 'No body');
		$mail->setBodyText(T_("Project") .": $project_name\n" . T_("Revision") . ": $rev\n");
		$mail->send();
	}
 }