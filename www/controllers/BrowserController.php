<?php
/**
 * Controller of browser module
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package browser
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class BrowserController extends USVN_Controller
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
		$txthtml = "
<table class=\"usvn_table\">
    <thead>
        <tr>
            <th>" . T_('Name') . "</th>
            <th>" . T_('Action') . "</th>
        </tr>
    </thead>
    <tbody>
";
		if ($path != "/" && $path != "//") {
			if (dirname($path) == "\\") {
				$pathbefore = "/";
			} else {
				$pathbefore = dirname($path) . "/";
			}
			$txthtml .= "<tr><td>" .$this->_view->img('CrystalClear/16x16/filesystems/folder_blue.png', T_('Folder'));
			$txthtml .= "<a href='javascript:ajax(3, " . "\"". $pathbefore . "\"" . ");'>..</a></td><td></td></tr>";
		}
		foreach ($tab as &$tabl) {
			$txthtml .= "<tr>";
            $dir = false;
			if ($tabl['isDirectory'] == 1) {
                $dir = true;
				$tabl['isDirectory'] = $this->_view->img('CrystalClear/16x16/filesystems/folder_blue.png', T_('Folder'));
            }
			else{
				$tabl['isDirectory'] = $this->_view->img('CrystalClear/16x16/mimetypes/document.png', T_('File'));
            }
			$txthtml .= "<td>".$tabl['isDirectory'];
			if ($dir) {
				$txthtml .= "<a href='javascript:ajax(3, "."\"".$tabl['path']."\"".");'>".$tabl['name']."</a></td>";
			} else {
				$txthtml .= "<a>".$tabl['name']."</a></td>";
			}
			$txthtml .= "<td><a href='javascript:ajax(1, "."\"".$tabl['path']."\"".");'>" .$this->_view->img('CrystalClear/16x16/apps/kwalletmanager.png', T_('Rights')) . "</a></td></tr>";
		}
        $txthtml .= "
            </tbody>
</table>
";
		echo "<txthtml><![CDATA[".$txthtml."]]></txthtml>\n";
		$this->_view->browser = $tab;
	}

	/**
	* Return a fake xml to dump rights or to update or insert rights
	*/
	public function RightManagementAction()
	{
		header('Content-Type: text/xml');
		echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
		echo "<files>\n";
		if (isset($_GET['pg']) && $_GET['pg'] == 1) {
			$this->dumpRights();
		} else if (isset($_GET['pg']) && $_GET['pg'] == 2) {
			$this->updateOrInsertRights();
		} else {
			if ($_GET['name'] == 'nop') {
				$this->getListFile("/");
			} else {
				$this->getListFile($_GET['name']);
			}
		}
		echo "</files>";
	}

	/**
	* If files exist dump rights
	*/
	function dumpRights()
	{
		$text = "";
		$table_project = new USVN_Db_Table_Projects();
		$table_files = new USVN_Db_Table_FilesRights();
		$res_project = $table_project->findByName($this->_request->getParam('project'));
		$table_groupstoproject = new USVN_Db_Table_GroupsToProjects();
		$res_groupstoproject = $table_groupstoproject->findByProjectId($res_project->projects_id);
		$table_groups = new USVN_Db_Table_Groups();
		$text = "<table class='usvn_table'><tr><td>".T_('Group')."</td><td>".T_('Read')."</td><td>".T_('Write')."</td></tr>";
		$i = 0;
		foreach ($res_groupstoproject as $groups)
		{
			$res_groups = $table_groups->findByGroupsId($groups->groups_id);
			$grp_name = $res_groups->groups_name;
			$text .= "<tr><td><label id=Lb".$i.">".$grp_name."</label></td>";
			$res_files = $table_files->findByPath($res_project->projects_id,$_GET['name']);
			$check = "<td><input id='checkRead".$grp_name."' type='checkbox' onclick='javascript:fctRead("."\"".$grp_name."\"".");'/></td>";
			$check .= "<td><input id='checkWrite".$grp_name."' type='checkbox' onclick='javascript:fctWrite("."\"".$grp_name."\"".");'/></td></tr>";
			if ($res_files != null)
			{
				$table_groupsfiles = new USVN_Db_Table_GroupsToFilesRights();
				$res_groupstofiles = $table_groupsfiles->findByIdRightsAndIdGroup($res_files->files_rights_id, $res_groups->groups_id);
				if ($res_groupstofiles != null)
				{
					if ($res_groupstofiles->files_rights_is_readable == 1)
						$text .= "<td><input id='checkRead".$grp_name."' type='checkbox' checked onclick='javascript:fctRead("."\"".$grp_name."\"".");'/></td>";
					else
						$text .= "<td><input id='checkRead".$grp_name."' type='checkbox' onclick='javascript:fctRead("."\"".$grp_name."\"".");'/></td>";
					if ($res_groupstofiles->files_rights_is_writable == 1)
						$text .= "<td><input id='checkWrite".$grp_name."' type='checkbox' checked onclick='javascript:fctWrite("."\"".$grp_name."\"".");'/></td></tr>";
					else
						$text .= "<td><input id='checkWrite".$grp_name."' type='checkbox' onclick='javascript:fctWrite("."\"".$grp_name."\"".");'/></td></tr>";
				}
				else {
					$text .= $check;
				}
			}
			else {
				$text .= $check;
			}
			$i++;
		}
		$text .= "</table>";
		echo "<nbgroup>".$i."</nbgroup>\n";
		echo "<groups><![CDATA[".$text."]]></groups>\n";
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
			$res_files = $table_files->findByPath($res_project->project_id, $_GET['name']);
			$msg = "Ok";
			$table_groupstofiles = new USVN_Db_Table_GroupsToFilesRights();
			$tabgroup = split("-", $_GET['group']);
			$tabrights = split("-", $_GET['rights']);
			$j = 0;
			if ($res_files != null)
			{
				foreach ($tabgroup as $group)
				{
					$table_group = new USVN_Db_Table_Groups();
					$res_groups = $table_group->findByGroupsName($group);
					$res_groupstofile = $table_groupstofiles->findByIdRightsAndIdGroup($res_files->files_rights_id, $res_groups->groups_id);
					if ($res_groupstofile != null)
					{
						$data_groupsfiles = array('files_rights_is_readable' => ($tabrights[$j] == 'true' ? 1 : 0),
									 			  'files_rights_is_writable' => ($tabrights[$j + 1] == 'true' ? 1 : 0));
						$where = $table_groupstofiles->getAdapter()->quoteInto('files_rights_id = ?', $res_files->files_rights_id);
						$where .= $table_groupstofiles->getAdapter()->quoteInto(' and groups_id = ?', $res_groups->groups_id);
						$table_groupstofiles->update($data_groupsfiles, $where);
					}
					else
					{
						$table_groupstofiles->insert(array('files_rights_id'		 => $res_files->files_rights_id,
														   'groups_id'	 	   		 => $res_groups->groups_id,
												  		   'files_rights_is_readable' => ($tabrights[$j] == 'true' ? 1 : 0),
									 			    	   'files_rights_is_writable' => ($tabrights[$j + 1] == 'true' ? 1 : 0)));
					}
					$j += 2;
				}
			}
			else
			{
				$id = $table_files->insert(array('projects_id' 	   			=> $res_project->id,
							 					 'files_rights_path' 	   	=> $_GET['name']));
				foreach ($tabgroup as $group)
				{
					$table_group = new USVN_Db_Table_Groups();
					$res_groups = $table_group->findByGroupsName($group);
					$table_groupstofiles->insert(array('files_rights_id' 		  => $id,
													   'files_rights_is_readable' => ($tabrights[$j] == 'true' ? 1 : 0),
							 						   'files_rights_is_writable' => ($tabrights[$j + 1] == 'true' ? 1 : 0),
						       	 					   'groups_id'	 			  => $res_groups->groups_id));
					$j += 2;
				}
			}
		}
		catch (Exception $e)
		{
			$msg .= $e->getMessage();
		}
		echo "<msg>" . $msg . "</msg>\n";
	}
}

