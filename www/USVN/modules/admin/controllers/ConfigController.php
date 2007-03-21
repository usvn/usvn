<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_ConfigController extends admin_IndexController
{
	public function saveAction()
	{
		$this->_redirect('admin/config/');
	}
}
