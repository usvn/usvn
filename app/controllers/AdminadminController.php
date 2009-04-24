<?php
/**
 * Main controller of the admin module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class AdminadminController extends USVN_Controller
{
	/**
	 * Pre-dispatch routines
	 *
	 * Called before action method. If using class with
	 * {@link Zend_Controller_Front}, it may modify the
	 * {@link $_request Request object} and reset its dispatched flag in order
	 * to skip processing the current action.
	 *
	 * @return void
	 */
	public function preDispatch()
	{
		parent::preDispatch();
		$user = $this->getRequest()->getParam('user');
		if (!$user || !$user->is_admin)
			$this->_redirect("/");
		$this->view->submenu = array(
		array('label' => 'Admin'),
		array('label' => 'Users',         'route' => 'admin', 'url' => array('controller' => 'user')),
		array('label' => 'Groups',        'route' => 'admin', 'url' => array('controller' => 'group')),
		array('label' => 'Projects',      'route' => 'admin', 'url' => array('controller' => 'project')),
		array('label' => 'Configuration', 'route' => 'admin', 'url' => array('controller' => 'config')),
		array('label' => 'System report', 'route' => 'admin', 'url' => array('controller' => 'systemreport'))
		);
	}

	public function indexAction()
	{
		$this->view->config = Zend_Registry::get('config');
		$this->view->available_version = USVN_Update::getUSVNAvailableVersion();
	}
}
