<?php
/**
 * Hooks management
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

class HooksadminController extends AdminadminController
{
	public function indexAction()
	{
		$table = new USVN_Db_Table_Hooks();
		$this->view->hooks = $table->fetchAll();
	}

	public function editAction()
	{
		$htable = new USVN_Db_Table_Hooks();
		$ptable = new USVN_Db_Table_Projects();
		$this->view->hook = $htable->find($this->getRequest()->getParam('id'))->current();
		$this->view->projects = $ptable->fetchAll();
		$hookedProjects = $this->view->hook->findManyToManyRowset('USVN_Db_Table_Projects', 'USVN_Db_Table_ProjectsToHooks');
		$this->view->hookedProjectsNames = array();
		foreach ($hookedProjects as $hookedProj)
		{
			$this->view->hookedProjectsNames[] = $hookedProj->name;
		}
	}

	public function updateAction()
	{
		// $data = $this->getUserData($_POST);
		if (empty($_POST)) {
			$this->_redirect("/admin/hooks/");
		}
		try {
			$htable = new USVN_Db_Table_Hooks();
			$hookId = $this->getRequest()->getParam('id');
			$hook = $htable->find($hookId)->current();
			if ($hook === NULL)
			{
				throw new USVN_Exception(sprintf(T_("Hook #%s does not exist."), $hookId));
			}
			// Update hook event if different
			if ($_POST['hooks_event'] != $hook->event)
			{
				if (!in_array($_POST['hooks_event'], USVN_SVNUtils::$hooks))
				{
					throw new USVN_Exception(sprintf(T_("%s is an invalid hook event")));
				}
				$hook->event = $_POST['hooks_event'];
				$hook->save();
			}
			// Delete all current hooks
			$pthtable = new USVN_Db_Table_ProjectsToHooks();
			$where = $pthtable->getAdapter()->quoteInto('hooks_id = ?', $hookId);
			$pthtable->delete($where);
			// Insert active hooks
			foreach ($_POST['projects'] as $projectId)
			{
				$data = array(
				    'hooks_id'    => $hookId,
				    'projects_id' => $projectId
				);
				$pthtable->insert($data);
			}
			$this->_redirect("/admin/hooks/");
		}
		catch (USVN_Exception $e) {
			$this->view->hook = $hook;
			$this->view->message = $e->getMessage();
			$ptable = new USVN_Db_Table_Projects();
			$this->view->projects = $ptable->fetchAll();
			$hookedProjects = $this->view->hook->findManyToManyRowset('USVN_Db_Table_Projects', 'USVN_Db_Table_ProjectsToHooks');
			$this->view->hookedProjectsNames = array();
			foreach ($hookedProjects as $hookedProj)
			{
				$this->view->hookedProjectsNames[] = $hookedProj->name;
			}
		}
	}

	public function newAction()
	{
		if (($hookfile = $this->getRequest()->getParam('hookfile')) != '') {
			$hookevent = $this->getRequest()->getParam('hookevent');
			if (!in_array($hookevent, USVN_SVNUtils::$hooks)) {
				throw new USVN_Exception(T_("This is an invalid hook event"));
			}
			$htable = new USVN_Db_Table_Hooks();
			$data = array('hooks_event' => $hookevent, 'hooks_path' => $hookfile);
			$htable->insert($data);
			$this->_redirect("/admin/hooks/");
		}
		$this->view->hooks = array();
		$config = new USVN_Config_Ini(USVN_CONFIG_FILE, 'general');
		if (is_dir($config->subversion->hooksPath) && ($dir = opendir($config->subversion->hooksPath))) {
			while (($file = readdir($dir)) !== false) {
				if (substr($file, 0, 1) != '.' && !preg_match('/^.*(#|~)$/', $file) && !is_dir($config->subversion->hooksPath.'/'.$file)) {
					$this->view->hooks[] = $file;
				}
			}
		}
		$table = new USVN_Db_Table_Hooks();
		foreach ($table->fetchAll() as $hook) {
			if (($key = array_search($hook->path, $this->view->hooks)) !== false) {
				unset($this->view->hooks[$key]);
			}
		}
		$this->view->hookevents = USVN_SVNUtils::$hooks;
	}
	
	public function deleteAction()
	{
		if (($id = $this->getRequest()->getParam('id'))) {
			$htable = new USVN_Db_Table_Hooks();
			if (($hook = $htable->find($id))) {
				$hook->current()->delete();
			}
		}
		$this->_redirect("/admin/hooks/");
	}
}

?>