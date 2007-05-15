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

class browser_IndexController extends IndexController
{
	public function indexAction()
    {
		$this->getListFile();
		$this->_render('index.html');
    }
	
	/**
	* Get list files of subversion directory.
	*/
	public function getListFile()
	{
		$SVN = new USVN_SVN($this->_request->getParam('project'));
		$tab = $SVN->listFile("");
		foreach ($tab as &$tabl)
		{
			if ($tabl['isDirectory'] == 1) 
				$tabl['isDirectory'] = "<img src='../../../../../../dossier.gif'>"; 
			else
				$tabl['isDirectory'] = "<img src='../../../../../../file.gif'>";
		}
		$this->_view->browser = $tab;
	}
	
	/**
	* Return a fake xml to dump rights or to update or insert rights
	*/
	public function RightManagementAction()
	{
		header('Content-Type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>";
		echo "<exemple>";
		if (isset($_GET['pg']) && $_GET['pg'] == 1)
			$this->dumpRights();
		else
			$this->updateOrInsertRights();
		echo "</exemple>";
	}
	
	/**
	* If files exist dump rights 
	*/
	function dumpRights()
	{
		$prefix = Zend_Registry::get('config')->database->prefix;
		$table_files = new USVN_Db_Table_FilesRights();
		$db = $table_files->getAdapter();
		$select_files = $db->select()->from($prefix.'files_rights', 
			array('files_rights_is_readable', 'files_rights_is_writable', 'files_rights_id'))->where('files_rights_path = ?', $_GET['name']);
		$res_files = $db->query($select_files)->fetchAll();
		if (isset($res_files[0]['files_rights_is_readable']))
		{
			echo "<isreadable>".$res_files[0]['files_rights_is_readable']."</isreadable>\n";
			echo "<iswritable>".$res_files[0]['files_rights_is_writable']."</iswritable>\n";
			$select_groupstofiles =$db->select()->from($prefix.'groups_to_files_rights', 'groups_id')->where('files_rights_id = ?', $res_files[0]['files_rights_id']);
			$res_groupstofiles = $db->query($select_groupstofiles)->fetchAll();
			echo "<group>".$res_groupstofiles[0]['groups_id']."</group>\n";
		}
		else
			echo "<isreadable>nop</isreadable>\n<iswritable>nop</iswritable>\n<group>nop</group>\n";
	}

	/**
	* If files exist update rights if not insert rights 
	*/
	function updateOrInsertRights()
	{
		try
		{
			$prefix = Zend_Registry::get('config')->database->prefix;
			$table_files = new USVN_Db_Table_FilesRights();
			$db = $table_files->getAdapter();
			$select_project = $db->select()->from($prefix.'projects', 'projects_id')->where('projects_name = ?', $this->_request->getParam('project'));
			$res_project = $db->query($select_project)->fetchAll();
			$select_files = $db->select()->from($prefix.'files_rights', array('files_rights_path', 'files_rights_id'))->where('files_rights_path = ?', $_GET['name']);
			$res_files = $db->query($select_files)->fetchAll();
			$msg = "Ok";
			$table_groupstofiles = new USVN_Db_Table_GroupsToFilesRights();
			if (isset($res_files[0]['files_rights_path']))
			{
				$data_files = array('projects_id' 	   		   => $res_project[0]['projects_id'],
						     		'files_rights_is_readable' => ($_GET['checkRead'] == 'true' ? 1 : 0),
							 		'files_rights_is_writable' => ($_GET['checkWrite'] == 'true' ? 1 : 0),
							 		'files_rights_path' 	   => $_GET['name']);
				$where = $db->quoteInto('files_rights_path = ?', $res_files[0]['files_rights_path']);
				$id = $table_files->update($data_files, $where);		
				$data_groupsfiles = array('groups_id'	 	   => $_GET['group']);
				$where = $table_groupstofiles->getAdapter()->quoteInto('files_rights_id = ?', $res_files[0]['files_rights_id']);					
				$table_groupstofiles->update($data_groupsfiles, $where);							   
			}
			else
			{
				$id = $table_files->insert(array('projects_id' 	   			=> $res_project[0]['projects_id'],
						     					 'files_rights_is_readable' => ($_GET['checkRead'] == 'true' ? 1 : 0),
							 					 'files_rights_is_writable' => ($_GET['checkWrite'] == 'true' ? 1 : 0),
							 					 'files_rights_path' 	   	=> $_GET['name']));
				$table_groupstofiles->insert(array('files_rights_id' 		=> $id,
						       	 				   'groups_id'	 			=> $_GET['group']));
			}
		}
		catch (Exception $e) {
			$msg = $e->getMessage();
		}
		echo "<msg>".$msg."</msg>\n";
	}
}
