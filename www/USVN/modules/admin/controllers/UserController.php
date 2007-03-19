<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_UserController extends admin_IndexController
{
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
