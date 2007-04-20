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
	public static function getTopMenu()
	{
		return array("Admin" => "admin/");
	}

	public static function generateMenuPath($request)
	{
		$base_url = $request->getBaseUrl();
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getActionName();

		$menuPath = "|<a href='" . $base_url . "/'>Home</a>";
		$menuPath .= $module != 'default' ? " /<a href='{$base_url}/{$module}/'>{$module}</a>" : '';
		$menuPath .= $controller != 'index' ? " /<a href='{$base_url}/{$module}/{$controller}'>{$controller}</a>" : '';
		$menuPath .= $action != 'index' ? " /<a href='{$base_url}/{$module}/{$controller}/{$action}'>{$action}</a>" : '';
		return $menuPath;
	}
}
