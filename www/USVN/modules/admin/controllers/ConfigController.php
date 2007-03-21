<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';


class admin_ConfigController extends admin_IndexController
{
	public function saveAction()
	{
		$config = new USVN_Config(USVN_CONFIG_FILE, USVN_CONFIG_SECTION);
		$config->translation->locale  = $_POST['language'];
		$config->save();
		$this->_redirect('admin/config/');
	}
}
