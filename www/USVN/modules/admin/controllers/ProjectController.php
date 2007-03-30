<?php
/**
 * Project management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage project
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once 'USVN/modules/admin/controllers/IndexController.php';

class admin_ProjectController extends admin_IndexController
{
	public function indexAction()
	{
		$table = new USVN_Db_Table_Projects();
		$this->_view->projects = $table->fetchAll(null, "projects_name");
		$this->_render('index.html');
	}

	public function newAction()
	{
		if (!empty($_POST)) {
			$this->save("USVN_Db_Table_Projects", "project");
		}
		$this->_render('form.html');
	}

	public function editAction()
	{
		if (!empty($_POST)) {
			$this->save("USVN_Db_Table_Projects", "project");
		} else {
			$projectTable = new USVN_Db_Table_Projects();
			$this->_view->project = $projectTable->fetchRow(array('projects_id = ?' => $this->getRequest()->getParam('id')));
		}
		$this->_render('form.html');
	}

	public function deleteAction()
	{
		if (!empty($_POST)) {
			$this->delete("USVN_Db_Table_Projects", "project");
		} else {
			$projectTable = new USVN_Db_Table_Projects();
			$this->_view->project = $projectTable->fetchRow(array('projects_id = ?' => $this->getRequest()->getParam('id')));
		}
		$this->_render('form.html');
	}
}
