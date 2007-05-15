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
		$this->_render('index.html');
    }
	
	/**
	* Get list files of subversion directory.
	*/
	public function getListFile($path)
	{
		$SVN = new USVN_SVN($this->_request->getParam('project'));
		$tab = $SVN->listFile($path);
		$txthtml = "<table class='sortable' id='users'><thead><tr><th></th><th>".T_('Name')."</th></tr></thead><tbody>";
		if ($path != "/" && $path != "//")
		{
			if (dirname($path) == "\\")
				$pathbefore = "/";
			else
				$pathbefore = dirname($path)."/";
			$txthtml .= "<tr><td><img src='../../../../../../dossier.gif'></td>";
			$txthtml .= "<td onmouseover='ajax(1, "."\"".$pathbefore."\"".");' onMouseOut='hiddenBulle();'>";
			$txthtml .= "<a href='javascript:ajax(3, "."\"".$pathbefore."\"".");'>..</a></td></tr>";
			//echo "<pathbefore><![CDATA[<a href='javascript:ajax(3, "."\"".$pathbefore."\"".");'>".$pathbefore."</a>"."]]></pathbefore>";
		}
		/*else
			echo "<pathbefore>a</pathbefore>";*/
		foreach ($tab as &$tabl)
		{
			$txthtml .= "<tr>";
			if ($tabl['isDirectory'] == 1) 
				$tabl['isDirectory'] = "<img src='../../../../../../dossier.gif'>"; 
			else
				$tabl['isDirectory'] = "<img src='../../../../../../file.gif'>";
			$txthtml .= "<td>".$tabl['isDirectory']."</td>";
			$txthtml .= "<td onmouseover='ajax(1, "."\"".$tabl['path']."\"".");' onMouseOut='hiddenBulle();'>";
			$txthtml .= "<a href='javascript:ajax(3, "."\"".$tabl['path']."\"".");'>".$tabl['name']."</a></td></tr>";
		}
		$txthtml .= "</tbody></table><br />";
		echo "<txthtml><![CDATA[".$txthtml."]]></txthtml>\n";
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
		else if (isset($_GET['pg']) && $_GET['pg'] == 2)
			$this->updateOrInsertRights();
		else
		{
			if ($_GET['name'] == 'nop')
				$this->getListFile("/");
			else
				$this->getListFile($_GET['name']);
		}
		echo "</exemple>";
	}
	
	/**
	* If files exist dump rights 
	*/
	function dumpRights()
	{
		$table_files = new USVN_Db_Table_FilesRights();
		$res_files = $table_files->findByPath($_GET['name']);
		if ($res_files != null)
		{
			$table_groupsfiles = new USVN_Db_Table_GroupsToFilesRights();
			$res_groupstofiles = $table_groupsfiles->findByIdRights($res_files->files_rights_id);
			echo "<isreadable>".$res_groupstofiles->files_rights_is_readable."</isreadable>\n";
			echo "<iswritable>".$res_groupstofiles->files_rights_is_writable."</iswritable>\n";
			echo "<group>".$res_groupstofiles->groups_id."</group>\n";
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
			$table_project = new USVN_Db_Table_Projects();
			$res_project = $table_project->findByName($this->_request->getParam('project'));
			$table_files = new USVN_Db_Table_FilesRights();
			$res_files = $table_files->findByPath($_GET['name']);
			$msg = "Ok";
			$table_groupstofiles = new USVN_Db_Table_GroupsToFilesRights();
			if ($res_files != null)
			{
				$data_files = array('projects_id' 	   		   => $res_project->projects_id,
							 		'files_rights_path' 	   => $_GET['name']);
				$db = $table_project->getAdapter();
				$where = $db->quoteInto('files_rights_path = ?', $res_files->files_rights_path);
				$id = $table_files->update($data_files, $where);		
				$data_groupsfiles = array('groups_id'	 	   => $_GET['group'],
										  'files_rights_is_readable' => ($_GET['checkRead'] == 'true' ? 1 : 0),
							 			  'files_rights_is_writable' => ($_GET['checkWrite'] == 'true' ? 1 : 0));
				$where = $table_groupstofiles->getAdapter()->quoteInto('files_rights_id = ?', $res_files->files_rights_id);					
				$table_groupstofiles->update($data_groupsfiles, $where);						   
			}
			else
			{
				$id = $table_files->insert(array('projects_id' 	   			=> $res_project->id,
							 					 'files_rights_path' 	   	=> $_GET['name']));
				$table_groupstofiles->insert(array('files_rights_id' 		=> $id,
												   'files_rights_is_readable' => ($_GET['checkRead'] == 'true' ? 1 : 0),
							 					   'files_rights_is_writable' => ($_GET['checkWrite'] == 'true' ? 1 : 0),
						       	 				   'groups_id'	 			  => $_GET['group']));
			}
		}
		catch (Exception $e) {
			$msg = $e->getMessage();
		}
		echo "<msg>".$msg."</msg>\n";
	}
}
