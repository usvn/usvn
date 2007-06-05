<?php
/**
 * Group management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This group has been realised as part of
 * end of studies group.
 *
 * $Id$
 */

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'AdminadminController.php';

class GroupadminController extends AdminadminController
{
	public static function getGroupData($data)
	{
		if (!isset($data["groups_name"]) || !isset($data["groups_description"])) {
			return array();
		}

		$group = array();
		$group["groups_name"] = $data["groups_name"];
		$group["groups_description"] = $data["groups_description"];
		return $group;
	}

	public function indexAction()
    {
    	$table = new USVN_Db_Table_Groups();
		$this->view->groups = $table->fetchAll(null, "groups_name");
    }

	public function newAction()
	{
		$table = new USVN_Db_Table_Groups();
		$this->view->group = $table->createRow();
	}

	public function createAction()
	{
		$data = $this->getGroupData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/group/new");
		}
		$table = new USVN_Db_Table_Groups();
		$group = $table->createRow($data);
		try {
			$group->save();
			$this->_redirect("/admin/group/");
		}
		catch (Exception $e) {
			$this->view>group = $group;
			$this->view>message = $e->getMessage();
			$this->render('new');
		}
	}

	public function editAction()
	{
		$table = new USVN_Db_Table_Groups();
		$this->view>group = $table->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		if ($this->view>group === null) {
			$this->_redirect("/admin/group/");
		}
	}

	public function updateAction()
	{
		$data = $this->getGroupData($_POST);
		if (empty($data)) {
			$this->_redirect("/admin/group/new");
		}
		$table = new USVN_Db_Table_Groups();
		$group = $table->fetchRow(array("groups_name = ?" => $this->getRequest()->getParam('name')));
		if ($group === null) {
			$this->_redirect("/admin/group/");
		}
		$group->setFromArray($data);
		try {
			$group->save();
			$this->_redirect("/admin/group/");
		}
		catch (Exception $e) {
			$this->view>group = $group;
			$this->view>message = $e->getMessage();
			$this->render('edit');
		}
	}

	public function deleteAction()
	{
		$table = new USVN_Db_Table_Groups();
		$group = $table->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		if ($group === null) {
			$this->_redirect("/admin/group/");
		}
		$group->delete();
		$this->_redirect("/admin/group/");
	}
}
