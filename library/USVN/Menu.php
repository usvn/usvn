<?php
/**
 * Menu loader
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: Menu.php 1342 2007-11-15 18:44:35Z dolean_j $
 */
class USVN_Menu
{
	private $_topMenu = array();
	private $_subMenu = array();
	private $_subSubMenu = array();
	private $_request;
	private $_user;
	private $_project;
	private $_access;
	private $_identity;
	private $_subMenuMethod;

	/**
	 * @param string Path of menu directory
	 * @param Zend_Controller_Request_Abstract Request
	 * @param mixed|null Identity from Zend_Auth
	 */
	public function __construct($menu_path, $request, $identity)
	{
		$this->_identity = $identity;
		$this->_request = $request;
		if ($identity === null) {
			$this->_user = "anonymous";
		}
		else {
			$this->_user = $identity['username'];
		}
		if (is_dir($menu_path)) {
			$dir = new DirectoryIterator($menu_path);
			foreach ($dir as $file) {
				$file = $file->getFilename();
				if (is_file($menu_path . DIRECTORY_SEPARATOR . $file)) {
					if (!preg_match('/Test.php$/', $file)) {
						$this->_loadController(substr($file, 0, -4), $menu_path);
					}
				}
			}
		}
		else {
			throw new USVN_Exception(T_("Can't open file %s."), $menu_path);
		}
	}

	private function _loadController($controller, $module_dir)
	{
		$class = "menus_{$controller}";
		$menu = new $class($this->_request, $this->_identity);
		$menus = array();
		if ($this->_request->getParam('area') == 'admin') {
			$menus = $menu->getAdminSubMenu($this->_request, $this->_identity);
		}
		else if ($this->_request->getParam('area') == 'project') {
			$menus = $menu->getProjectSubMenu($this->_request, $this->_identity);
		}
		else if ($this->_request->getParam('area') == 'group') {
			$menus = $menu->getGroupSubMenu($this->_request, $this->_identity);
		}
		foreach ($menus as $m) {
			array_push($this->_subMenu, $m);
		}

		if ($controller == $this->_request->getControllerName()) {
			$this->_subSubMenu = $menu->getSubSubMenu($this->_request, $this->_identity);
		}
	}

	/**
	* Get menu entries in top menu. Example:  Admin
	*
	* @return array with menu entry see USVN_modules_admin_Menu for exemple
	* @see USVN_modules_admin_Menu
	*/
	public function getTopMenu()
	{
		$menu = array();
		if ($this->_identity === null) {
			array_push($menu,
			array(
			"title" => T_("Sign in"),
			"link"=> "login/",
			"controller" => "login",
			"action" => ""
			)
			);
		} else {
			array_push($menu,
				array(
					"title" => T_("Homepage"),
					"link"=> "",
					"controller" => "index",
					"action" => ""
				)
			);
			if ($this->_request->getParam('area') === "project") {
				array_push($menu,
					array(
						"title" => str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->_request->getParam('project')),
						"link"=> "project/{$this->_request->getParam('project')}",
					)
				);
			}
			else if ($this->_request->getParam('area') === "group") {
				array_push($menu,
					array(
						"title" => str_replace(USVN_URL_SEP, USVN_DIRECTORY_SEPARATOR, $this->_request->getParam('group')),
						"link"=> "group/{$this->_request->getParam('group')}",
					)
				);
			}
			array_push($menu,
				array(
					"title" => T_('Edit my profile'),
					"link"=> "profile/",
				)
			);
			if ($this->_request->getParam('user')->is_admin) {
				array_push($menu,
					array(
						"title" => T_("Admin"),
						"link"=> "admin/",
						"controller" => "admin",
						"action" => ""
					)
				);
			}
			array_push($menu,
				array(
					"title" => T_("Logout"),
					"link"=> "logout/",
					"controller" => "login",
					"action" => "logout"
				)
			);
		}
		return $menu;
	}

	/**
	* Get menu entries in sub menu. Example: User
	*
	* @return array with menu entry see USVN_modules_admin_Menu for exemple
	* @see USVN_modules_admin_Menu
	*/
	public function getSubMenu()
	{
		return $this->_subMenu;
	}

	/**
	* Get menu entries in top menu. Example: Add user
	*
	* @return array with menu entry see USVN_modules_admin_Menu for exemple
	* @see USVN_modules_admin_Menu
	*/
	public function getSubSubMenu()
	{
		return $this->_subSubMenu;
	}
}
