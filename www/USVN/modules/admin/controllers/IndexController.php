<?php
/**
 * Main controller of the admin module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage controller
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
require_once 'USVN/modules/default/controllers/IndexController.php';

class admin_IndexController extends IndexController
{
	protected function save($className, $name)
	{
		$table = new $className();
		/* @var $table USVN_Db_Table */

		$info = $table->info();

		//truc pas logique, a voir
		$primary = array();
		$count = 0;
		foreach ($info['primary'] as $key) {
			if (isset($_POST[$key]) && $_POST[$key]) {
				$primary[] = $_POST[$key];
				unset($_POST[$key]);
				$count++;
			}
		}

		if ($count && $count == count($info['primary'])) {
			$obj = call_user_func(array($table, "find"), $primary)->current();
		} elseif ($count) {
			throw new USVN_Exception(T_("Not enough primary key"));
		} else {
			$obj = $table->fetchNew();
		}

		try {
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
