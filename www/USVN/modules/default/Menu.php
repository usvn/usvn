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
class USVN_modules_default_Menu extends USVN_AbstractMenu
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
		$menu = array();
		array_push($menu,
			array(
				"title" => T_("Homepage"),
				"link"=> "",
				"module" => "default",
				"controller" => "index",
				"action" => ""
			)
		);
		if ($identity === null) {
			array_push($menu,
				array(
					"title" => T_("Sign in"),
					"link"=> "login/",
					"module" => "default",
					"controller" => "login",
					"action" => ""
				)
			);
		}
		else {
			array_push($menu,
				array(
					"title" => T_("Logout"),
					"link"=> "logout/",
					"module" => "default",
					"controller" => "login",
					"action" => "logout"
				)
			);
		}
		return $menu;
	}
}
