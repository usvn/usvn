<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_UserController extends IndexController
{
	public function indexAction()
	{
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

	public function addAction()
	{
		$user = new USVN_modules_admin_models_Users();

		$user->add(
						$this->getRequest()->getParam('login'),
						crypt($this->getRequest()->getParam('password')),
						$this->getRequest()->getParam('lastname'),
						$this->getRequest()->getParam('firstname'),
						$this->getRequest()->getParam('email')
					);
	}

	public function editAction()
	{

	}

	public function deleteAction()
	{
		$user = new USVN_modules_admin_models_Users();

		$user->deleteUser($this->getRequest()->getParam('id'));
	}

	public function noRouteAction()
    {
        $this->_redirect('/');
    }

    public function listAction()
    {
    	$user = new USVN_modules_admin_models_Users();

    	$listUser = $user->listAll();
    }
}