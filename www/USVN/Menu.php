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
 * $Id$
 */
class USVN_Menu
{
	private $_topMenu = array();
	private $_request;

	/**
	* @param string Path of module directory
	* @param Zend_Controller_Request_Abstract Request
	*/
	public function __construct($module_path, $request)
	{
		$this->_request = $request;
		if ($dh = opendir($module_path)) {
			while (($module = readdir($dh)) !== false) {
				if ($module{0} != '.') {
					$module_dir = $module_path . "/". $module . "/";
					if (is_dir($module_dir) && file_exists($module_dir . "/Menu.php")) {
						$this->_loadModule($module, $module_dir);
					}
				}
			}
			closedir($dh);
		}
		else {
			throw new USVN_Exception(T_("Can't open %s."), $module_path);
		}
	}

	private function _loadModule($module, $module_dir)
	{
		Zend_Loader::loadFile("Menu.php", $module_dir, true);
		$class = "USVN_modules_{$module}_Menu";
		$menu = new $class();
		$this->_topMenu = array_merge($this->_topMenu, $menu->getTopMenu($this->_request));
	}

	/**
	* Get menu entries in top menu.
	*
	* @return array with menu entry see USVN_modules_admin_Menu for exemple
	* @see USVN_modules_admin_Menu
	*/
	public function getTopMenu()
	{
		return $this->_topMenu;
	}
}
