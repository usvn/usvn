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
class USVN_modules_admin_Menu extends USVN_AbstractMenu
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
		return array(
			array(
				"title" => T_("Administration"),
				"link"=> "admin",
				"module" => "admin",
				"controller" => "index",
				"action" => ""
			)
		);
	}

	/**
	* Get menu entries in sub menu.
	*
	* @param Zend_Controller_Request_Abstract Request
	* @param mixed|null Identity from Zend_Auth
	* @return array
	*/
	public static function getSubMenu($request, $identity)
	{
		return array(
			array(
				"title" => T_("Users"),
				"link"=> "admin/user/",
				"module" => "admin",
				"controller" => "user",
				"action" => ""
			),
			array(
				"title" => T_("Groups"),
				"link"=> "admin/group/",
				"module" => "group",
				"controller" => "index",
				"action" => ""
			),
			array(
				"title" => T_("Projects"),
				"link"=> "admin/project/",
				"module" => "admin",
				"controller" => "project",
				"action" => ""
			),
			array(
				"title" => T_("Configuration"),
				"link"=> "admin/config/",
				"module" => "admin",
				"controller" => "config",
				"action" => ""
			)
		);
	}

	/**
	* Get menu entries in sub sub menu.
	* By example Menu is Admin
	* Sub menu is User
	* Sub sub menu is New user
	*
	* @param Zend_Controller_Request_Abstract Request
	* @param mixed|null Identity from Zend_Auth
	* @return array
	*/
	public static function getSubSubMenu($request, $identity)
	{
		switch ($request->getParam('controller')) {
			case 'user':
				return USVN_modules_admin_Menu::_userMenu();
			case 'group':
				return USVN_modules_admin_Menu::_groupMenu();
			case 'project':
				return USVN_modules_admin_Menu::_projectMenu();
		}
		return array();
	}

	private static function _projectMenu()
	{
		return array(
			array(
				"title" => T_("Add new project"),
				"link"=> "admin/project/new/",
				"module" => "admin",
				"controller" => "project",
				"action" => "new"
			)
		);
	}

	private static function _groupMenu()
	{
		return array(
			array(
				"title" => T_("Add new group"),
				"link"=> "admin/group/new/",
				"module" => "admin",
				"controller" => "group",
				"action" => "new"
			)
		);
	}

	private static function _userMenu()
	{
		return array(
			array(
				"title" => T_("Add new user"),
				"link"=> "admin/user/new/",
				"module" => "admin",
				"controller" => "user",
				"action" => "new"
			),
			array(
				"title" => T_("Import htpasswd"),
				"link"=> "admin/user/import/",
				"module" => "admin",
				"controller" => "user",
				"action" => "import"
			)
			,
			array(
				"title" => T_('Edit my profile'),
				"link"=> "admin/user/editProfile/",
				"module" => "admin",
				"controller" => "user",
				"action" => "editProfile"
			)
		);
	}
}
