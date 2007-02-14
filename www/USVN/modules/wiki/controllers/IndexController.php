<?php

class wiki_IndexController extends IndexController
{
	public function preDispatch()
	{
		parent::preDispatch();
		$request = $this->_request;
		$request->setPathInfo(rtrim($request->getPathInfo(), '/'));

		$path = $request->getPathInfo();
		$len = strlen($request->getModuleName());
		$len += strpos($path, $request->getModuleName());
		$path = substr($path, $len);
		if (!$path) {
			$path = '/';
		}
		$path = urldecode($path);

		$this->_view->path = $path;
		$request->setParam('path', $path);

		$actions = array("delete", "edit");
		foreach ($actions as $action) {
			if (isset($_GET[$action])) {
				$request->setActionName($action);
				$request->setParam('action', $action);
				$request->setDispatched(false);
				unset($_GET[$action]);
				break;
			}
		}
	}

	public function viewAction()
	{
		$this->_render('view.html');
	}

	public function editAction()
	{
		$this->viewAction();
	}

	public function deleteAction()
	{
		$this->viewAction();
	}
}
