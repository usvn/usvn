<?php

require_once 'USVN/modules/default/controllers/IndexController.php';

class admin_IndexController extends IndexController
{
	protected function save($className, $name, $callback = null)
	{
		$table = new $className();
		/* @var $table USVN_Db_Table */

		$info = $table->info();

		//truc pas logique, a voir
		$primary = array_intersect_key($_POST, array_keys($info['primary']));

		if ($primary) {
			$obj = $table->find($primary);
		} else {
			$obj = $table->fetchNew();
		}

		try {
			if ($callback !== null && method_exists($className, $callback)) {
				call_user_func(array($className, $callback));
			}
			//unset($_POST[$primaryKey]);
			$obj->setFromArray($_POST);
			$obj->save();
			if ($this->getRequest()->getActionName() == 'new') {
				$this->saveAfterNew($obj, $name);
			}
		} catch (Exception $e) {
			$this->_view->exception = $e;
			$this->_render("exception.html");
			$this->_view->clearVars();
		}
		$this->_view->$name = $obj;
	}

	/**
	 *
	 *
	 * @param USVN_Db_Table_Row $obj
	 */
	protected function saveAfterNew($obj, $name)
	{
		$this->_redirect("/admin/{$name}/edit/id/{$obj->id}/");
	}

	/**
	 *
	 *
	 */
	protected function delete($className, $name)
	{
		$table = new $className();
		$db = $table->getAdapter();
		$info = $table->info();

		$id = $this->getRequest()->getParam('id');
		$id = isset($_POST['id']) ? $_POST['id'] : $id;
		$where = $db->quoteInto("{$info['primary'][0]} = ?", $id);
		try {
			$rows_affected = $table->delete($where);
			$this->_redirect("/admin/{$name}/");
		} catch (Exception $e) {
			$this->_view->exception = $e;
			$this->_render("exception.html");
			$this->_view->clearVars();
		}
	}
}
