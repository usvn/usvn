<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_GroupController extends admin_IndexController
{
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
