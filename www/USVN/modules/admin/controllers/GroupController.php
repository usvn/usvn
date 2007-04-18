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
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_GroupController extends admin_IndexController
{
	public static function getGroupDataFromPost()
	{
		$group = array('groups_name'	=> $_POST['groups_name']);
		if (isset($_POST['groups_id']) && !empty($_POST['groups_id'])) {
			$group['groups_id'] = $_POST['groups_id'];
		}
		return $group;
	}

	public function indexAction()
    {
    	$table = new USVN_Db_Table_Groups();
		$this->_view->groups = $table->fetchAll(null, "groups_name");
		$this->_render('index.html');
    }

	public function newAction()
	{
		if (!empty($_POST)) {
			$data = admin_GroupController::getGroupDataFromPost();
			$this->save("USVN_Db_Table_Groups", "group", $data);
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST)) {
			$data = admin_GroupController::getGroupDataFromPost();
			$this->save("USVN_Db_Table_Groups", "group", $data);
		} else {
			$groupTable = new USVN_Db_Table_Groups();
			$this->_view->group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		}
		$this->_render('form.html');
		$this->_render('../user/attachment.html');
		$this->_render('../project/attachment.html');
	}

	public function deleteAction()
	{
		if (!empty($_POST)) {
			$data = admin_GroupController::getGroupDataFromPost();
			$this->delete("USVN_Db_Table_Groups", "group", $data);
		} else {
			$groupTable = new USVN_Db_Table_Groups();
			$this->_view->group = $groupTable->fetchRow(array('groups_name = ?' => $this->getRequest()->getParam('name')));
		}
		$this->_render('form.html');
		$this->_render('../user/attachment.html');
		$this->_render('../project/attachment.html');
	}

	public function noRouteAction()
    {
        $this->_redirect('/');
    }
}
