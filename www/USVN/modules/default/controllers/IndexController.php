<?php
/**
 * Main controller
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package default
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class IndexController extends Zend_Controller_Action {
	/**
	 * Zend_Controller_Request_Abstract object wrapping the request environment
	 * @var Zend_Controller_Request_Http
	 */
	protected $_request;

	/**
	 * A simple view object
	 * @var Zend_View
	 */
	protected $_view = null;

	/**
	* Mime type render by the controller
	*
	* @var string
	*/
	protected $_mimetype = 'text/html';

	/**
	 * Init method.
	 *
	 * Call during construction of the controller to perform some default initialization.
	 *
	 */
	public function init() {
		parent::init();

		$this->_view = $this->getInvokeArg('view');
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
		$this->_view->project = $this->_request->getParam('project', '__NONE__');

		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		if ($module === null) {
			$module = "default";
		}
		$dir = realpath(USVN_DIRECTORY . "/modules/$module/views/$controller");
		if ($dir === false || !is_dir($dir)) {
			throw new Zend_Controller_Exception("Controller's views directory not found. Controller is $controller and module is $module.");
		}
		$this->_view->setScriptPath($dir);
		$this->_view->assign('project', $this->getRequest()->getParam('project'));
		$this->_view->assign('module', $this->getRequest()->getParam('module'));
		$this->_view->assign('controller', $this->getRequest()->getParam('controller'));
		$this->_view->assign('action', $this->getRequest()->getParam('action'));
		
		if ($this->_checkAccess(Zend_Auth::getInstance()->getIdentity()))
		{
			// The current use is allowed
		} else {
			// The current use is not allowed
			throw new Zend_Controller_Exception("You are not allowed to access to the controller $controller and module is $module.");
		}
		
	}

	/**
	 * Check if the current identity can access this module/controller/action
	 *
	 * @param Array $identity
	 */
	protected function _checkAccess($identity)
	{
		$module = $this->getRequest()->getModuleName();
		$controller = $this->getRequest()->getControllerName();
		$project = $this->getRequest()->getParam('project');
		$action = $this->getRequest()->getParam('action');
		$user = $identity['username'];
		if (!$project) {
			$project = "__NONE__";
		}
		
		if (!$identity['username']) {
			$user = "anonymous";
		}
		$access = new USVN_Db_Table_Access();
		return $access->access($user, $project, $module, $controller, $action);
	}

	/**
	 * Default action for every controller.
	 *
	 */
	public function indexAction() {
		$this->_render();
	}

	/**
	 * A simple wrapper to render a template.
	 *
	 * It actually get the Response object, set the Content-Type and
	 * render our $template.
	 *
	 * If $template is null, it will automaticaly detect our current action and try to
	 * render the $action template.
	 *
	 * @param string $template
	 */
	protected function _render($template = null)
	{
		if ($template === null) {
			$template = $this->getRequest()->getActionName() . ".html";
		}
		$this->getResponse()
			->setHeader('Content-Type', $this->_mimetype)
			->appendBody($this->_view->render($template));
	}
}
