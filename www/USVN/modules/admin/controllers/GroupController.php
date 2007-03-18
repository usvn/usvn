<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_GroupController extends admin_IndexController
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
		$group = new USVN_modules_admin_models_Groups();

		$group->add($this->getRequest()->getParams('name'));
	}

	public function editAction()
	{

	}

	public function deleteAction()
	{
		$group = new USVN_modules_admin_models_Groups();

		$group->deleteGroup($this->getRequest()->getParam('id'));
	}

	public function noRouteAction()
    {
        $this->_redirect('/');
    }

    public function listAction()
    {
    	$group = new USVN_modules_admin_models_Groups();

    	$listGroup = $group->listAll();
    }
}