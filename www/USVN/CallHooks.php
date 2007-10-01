<?php
/**
 * Call each hook during subversion operations
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

 class USVN_CallHooks
 {
	private $_hooks = array();

 	/**
	 * @param string Path of hook directory
	 */
	public function __construct($hook_path)
	{
		//At this time hooks are hardcode
		include_once($hook_path . DIRECTORY_SEPARATOR ."NotifByMail.php");
		$hook = new HookNotifByMail();
		array_push($this->_hooks, $hook);
	}

	/**
	* Post commit hook
	*
	* @string the path to this repository
	* @int the number of the revision just committed
	*/
	public function postCommit($repos , $rev)
	{
		foreach ($this->_hooks as $hook) {
			$hook->postCommit($repos, $rev);
		}
	}
 }