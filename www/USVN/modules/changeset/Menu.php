<?php
/**
 * Menu for default module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage menu
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_modules_changeset_Menu extends USVN_AbstractMenu
{
	/**
	* Get menu entries in top menu.
	*
	* @param Zend_Controller_Request_Abstract Request
	* @param mixed|null Identity from Zend_Auth
	* @return array
	*/
	public static function getTopMenu($request, $identity)
	{
		$project = $request->getParam("project");
		if ($project !== '__NONE__') {
			return array(
				array(
					"title" => T_("Changeset"),
					"link"=> "project/" . $project . "/changeset/",
					"module" => "changeset",
					"controller" => "index",
					"action" => ""
				)
			);
		}
		return array();
	}
}
