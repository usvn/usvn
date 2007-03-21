<?php

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_ProjectController extends admin_IndexController {
	public function indexAction()
	{
		$table = new USVN_modules_default_models_Projects();
		$this->_view->projects = $table->fetchAll(null, "projects_name");
		$this->_render();
	}

	public function newAction()
	{
		if (!empty($_POST)) {
			$this->save("USVN_modules_default_models_Projects", "project");
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST)) {
			$this->save("USVN_modules_default_models_Projects", "project");
		}
		$this->_render('form.html');
	}

	protected function save($className, $name)
	{
		$table = new $className();
		/* @var $table USVN_Db_Table */

		$info = $table->info();

		$primary = array_intersect_key($_POST, array_keys($info['primary']));

		if ($primary) {
			$obj = $table->find($primary);
		} else {
			$obj = $table->fetchNew();
		}
		unset($_POST[$primaryKey]);
		$obj->setFromArray($_POST);

		try {
			$obj->save();
			if ($this->getRequest()->getActionName() == 'new') {
				$this->saveAfterNew($obj);
			}
		} catch (Exception $e) {
			$this->_view->exception = $e;
			$this->_render('exception.html');
			$this->_view->clearVars();
		}
		$this->_view->$name = $obj;
	}

	/**
	 *
	 *
	 * @param USVN_Db_Table_Row $obj
	 */
	protected function saveAfterNew($obj)
	{
		$this->_redirect("/admin/project/edit/{$obj->name}/");
	}

	public function deleteAction()
	{

	}
}
