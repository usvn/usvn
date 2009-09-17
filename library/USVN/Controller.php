<?php
/**
 * Main controller
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: Controller.php 1342 2007-11-15 18:44:35Z dolean_j $
 */

class USVN_Controller extends Zend_Controller_Action
{
	/**
	 * Zend_Controller_Request_Abstract object wrapping the request environment
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request;

	/**
	 * Mime type render by the controller
	 * @var string
	 */
	protected $_mimetype = 'text/html';

	/**
	 * Init method.
	 * Call during construction of the controller to perform some default initialization.
	 */
	public function init()
	{
		parent::init();
		$this->getResponse()->setHeader('Content-Type', $this->_mimetype);
		$this->_request->setParam('view', $this->_helper->viewRenderer);
		$this->_helper->viewRenderer->setViewScriptPathSpec(":action.phtml");
		$this->view->addHelperPath(USVN_HELPERS_DIR, 'USVN_View_Helper');
		$this->_helper->layout->setLayout('default');
		$this->_request->setParam('project', $this->_request->getParam('project', '__NONE__'));
		$this->_request->setParam('area',    $this->_request->getParam('area',    '__NONE__'));
		if ($this->_mimetype != 'text/html')
			$this->_helper->viewRenderer->setNoRender();
	}

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
		$request = $this->getRequest();
		$controller = $request->getControllerName();
		
		$dir = realpath(USVN_VIEWS_DIR . '/' . $controller);
		if ($dir === false || !is_dir($dir))
			throw new Zend_Controller_Exception('Controller\'s views directory not found. Controller is $controller.');
		$this->view->setScriptPath($dir);
		$this->view->assign('project', str_replace(USVN_URL_SEP, '/', $request->getParam('project')));
		$this->view->assign('controller', $request->getParam('controller'));
		$area = $request->getParam('area');
		if ($area == '__NONE__') {
			$area = $request->getParam('controller');
		} elseif (in_array($area, array('project', 'group'))) {
			$area = 'index';
		}
		$this->view->assign('area', $area);
		$this->view->assign('action', $request->getParam('action'));
		
		$identity = Zend_Auth::getInstance()->getIdentity();
		if ($identity === null)
		{
			// TODO:
			// It is ugly to have "magic strings" instead of an array saying
			// which controllers do not need to be logged in...
			if ($controller != "login" && $controller != "rss")
			{
				$this->_redirect("/login/");
			}
			return;
		}
		
		$table = new USVN_Db_Table_Users();
		$user = $table->fetchRow(array("users_login = ?" => $identity['username']));
		if ($user === null && $controller != "login"  && $controller != "rss")
		{
			$this->_redirect("/logout/");
		}
		$request->setParam('user', $user);
	}

	/**
	 * Redirect to another URL
	 *
	 * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
	 *
	 * @param string $url
	 * @param array $options Options to be used when redirecting
	 * @return void
	 */
	protected function _redirect($url, array $options = array())
	{
		if (!defined("PHPUnit_MAIN_METHOD"))
			return parent::_redirect($url, $options);
		else
			throw new USVN_Test_Exception_Redirect($url, $options);
	}
}
