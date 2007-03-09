<?php

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
			throw new Zend_Controller_Exception("Controller's views directory not found");
		}
		$this->_view->setScriptPath($dir);
		$this->_view->assign('project', $this->getRequest()->getParam('project'));
		$this->_view->assign('module', $this->getRequest()->getParam('module'));
		$this->_view->assign('controller', $this->getRequest()->getParam('controller'));
		$this->_view->assign('action', $this->getRequest()->getParam('action'));
		
		$this->_checkAccess(Zend_Auth::getInstance()->getIdentity());
	}

	/**
	 * Check if the current identity can access this module/controller/action
	 *
	 * @param Array $identity
	 */
	protected function _checkAccess($identity)
	{
	}
	
	/**
	 * Default action for evry controller.
	 *
	 */
	public function indexAction() {
		$this->_render();
	}

	/**
	 * A simple wrapper to render a template.
	 * 
	 * It actually get the Response object, set the Content-Type to 'text/html' and
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
			->setHeader('Content-Type', 'text/html')
			->appendBody($this->_view->render($template));
	}
}
