<?php
/**
 * Base class for modules menu
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
 * $Id: AbstractMenu.php 1188 2007-10-06 12:03:17Z crivis_s $
 */
abstract class USVN_AbstractMenu
{
	/**
	 * HTTP Request
	 *
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request;

	/**
	 * User identity
	 *
	 * @var mixed|null Identity from Zend_Auth
	 */
	protected $_identity;

	/**
	 * Public constructor
	 *
	 * Set internal properties.
	 *
	 * @param Zend_Controller_Request_Http $request
	 * @param mixed|null $identity
	 */
	public function __construct($request, $identity)
	{
		$this->_request = $request;
		$this->_identity = $identity;
	}

	/**
	 * Get menu entries in admin sub menu.
	 *
	 * @return array
	 */
	public function getAdminSubMenu()
	{
		return array();
	}

	/**
	 * Get menu entries in project sub menu.
	 *
	 * @return array
	 */
	public function getProjectSubMenu()
	{
		return array();
	}

	/**
	 * Get menu entries in group sub menu.
	 *
	 * @return array
	 */
	public function getGroupSubMenu()
	{
		return array();
	}

	/**
	 * Get menu entries in sub sub menu.
	 * By example Menu is Admin
	  * Sub menu is User
	 * Sub sub menu is New user
	 *
	 * @return array
	 */
	public function getSubSubMenu()
	{
		return array();
	}
}
