<?php
/**
 * Menu for admin module
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
class USVN_modules_admin_Menu extends USVN_modules_default_AbstractMenu
{
	/**
	* Get menu entries in top menu.
	*
	* @param Zend_Controller_Request_Abstract Request
	* @return array
	*/
	public static function getTopMenu($request)
	{
		return array(
			array(
				"title" => T_("Administration"),
				"link"=> "admin"
			)
		);
	}

	/**
	* Get menu entries in sub menu.
	*
	* @param Zend_Controller_Request_Abstract Request
	* @return array
	*/
	public static function getSubMenu($request)
	{
		return array(
			array(
				"title" => T_("Users"),
				"link"=> "admin/user/"
			),
			array(
				"title" => T_("Groups"),
				"link"=> "admin/group/"
			),
			array(
				"title" => T_("Projects"),
				"link"=> "admin/project/"
			),
			array(
				"title" => T_("Configuration"),
				"link"=> "admin/config/"
			)
		);
	}
}
