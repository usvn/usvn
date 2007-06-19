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
class BrowserajaxController extends USVN_Controller
{
    protected $_mimetype = 'text/xml';

	public function RightManagementAction()
	{
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

	private function getTopLink($path)
	{
		$str = "<h2>";
		$str .= '<a href=\'javascript:ajax(3, "/");\'>root</a>&nbsp;/&nbsp;';
		$list = array();
		while ($path != '/') {
			array_push($list, $path);
			$path = dirname($path);
		}
		$list = array_reverse($list);
		foreach ($list as $path) {
			$str .= '<a href=\'javascript:ajax(3, "' . $path . '");\'>' . basename($path) . '</a>&nbsp;/&nbsp;';
		}
		$str .= "</h2>";
		$str .= "<h3><a href='javascript:ajax(1, \"{$path}\");'>Modifier les droits sur {$path}</a></h3>";
		return $str;
	}

	/**
	* Get list files of subversion directory.
	*/
	private function getListFile($path)
	{
		$path = realpath($path);
		$SVN = new USVN_SVN($this->_request->getParam('project'));
		$tab = $SVN->listFile($path);
		$txthtml = $this->getTopLink($path) . "
<br />
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
			$txthtml .= "<tr><td>" .$this->view->img('CrystalClear/16x16/filesystems/folder_blue.png', T_('Folder'));
			$txthtml .= "<a href='javascript:ajax(3, " . "\"". $pathbefore . "\"" . ");'>..</a></td><td></td></tr>";
		}
		foreach ($tab as &$tabl) {
			$txthtml .= "<tr>";
            $dir = false;
			if ($tabl['isDirectory'] == 1) {
                $dir = true;
				$tabl['isDirectory'] = $this->view->img('CrystalClear/16x16/filesystems/folder_blue.png', T_('Folder'));
            }
			else{
				$tabl['isDirectory'] = $this->view->img('CrystalClear/16x16/mimetypes/document.png', T_('File'));
            }
			$txthtml .= "<td>".$tabl['isDirectory'];
			if ($dir) {
				$txthtml .= "<a href='javascript:ajax(3, "."\"".$tabl['path']."\"".");'>".$tabl['name']."</a></td>";
			} else {
				$txthtml .= "<a>".$tabl['name']."</a></td>";
			}
			$txthtml .= "<td><a href='javascript:ajax(1, "."\"".$tabl['path']."\"".");'>" .$this->view->img('CrystalClear/16x16/apps/kwalletmanager.png', T_('Rights')) . "</a></td></tr>";
		}
        $txthtml .= "
            </tbody>
</table>
";
		echo "<txthtml><![CDATA[".$txthtml."]]></txthtml>\n";
		$this->view->browser = $tab;
	}

	/**
	* If files exist dump rights
	*/
	private function dumpRights()
	{
		$text = "";
		$table_project = new USVN_Db_Table_Projects();
		$res_project = $table_project->findByName($this->_request->getParam('project'));

		$acces_rights = new USVN_FilesAccessRights($res_project->projects_id);

		$table_files = new USVN_Db_Table_FilesRights();
		$table_groupstoproject = new USVN_Db_Table_GroupsToProjects();
		$res_groupstoproject = $table_groupstoproject->findByProjectId($res_project->projects_id);
		$table_groups = new USVN_Db_Table_Groups();
		$text = "<table class='usvn_table'><tr><td>".T_('Group')."</td><td>".T_('Read')."</td><td>".T_('Write')."</td></tr>";
		$i = 0;
		$disabled = "";
		$identity = Zend_Auth::getInstance()->getIdentity();
		if (!$res_project->userIsAdmin($identity["username"])) {
			$disabled = " disabled";
		}
		foreach ($res_groupstoproject as $groups)
		{
			$access = $acces_rights->findByPath($groups->groups_id, $_GET['name']);
			$res_groups = $table_groups->findByGroupsId($groups->groups_id);
			$grp_name = $res_groups->groups_name;
			$text .= "<tr><td><label id=Lb".$i.">".$grp_name."</label></td>";
			if ($access['read'] == 1)
				$text .= "<td><input id='checkRead".$grp_name."' type='checkbox' checked onclick='javascript:fctRead("."\"".$grp_name."\"".");' $disabled/></td>";
			else
				$text .= "<td><input id='checkRead".$grp_name."' type='checkbox' onclick='javascript:fctRead("."\"".$grp_name."\"".");' $disabled/></td>";
			if ($access['write'] == 1)
				$text .= "<td><input id='checkWrite".$grp_name."' type='checkbox' checked onclick='javascript:fctWrite("."\"".$grp_name."\"".");' $disabled/></td></tr>";
			else
				$text .= "<td><input id='checkWrite".$grp_name."' type='checkbox' onclick='javascript:fctWrite("."\"".$grp_name."\"".");' $disabled/></td></tr>";
			$i++;
		}
		$text .= "</table>";
		$text .= "<br /><table><tr><td><input type='button' value='Ok' onclick=\"javascript:ajax(2, '" . $_GET['name'] . "');\" $disabled/></td><td>";
		$text .= "<input type='button' value='Cancel' onclick='javascript:cancel();'/></td></tr></table><label id='labelError'></label>";
		echo "<nbgroup>".$i."</nbgroup>\n";
		echo "<groups><![CDATA[".$text."]]></groups>\n";
	}

	/**
	* If files exist update rights if not insert rights
	*/
	private function updateOrInsertRights()
	{
		try
		{
			$table_project = new USVN_Db_Table_Projects();
			$res_project = $table_project->findByName($this->_request->getParam('project'));
			$acces_rights = new USVN_FilesAccessRights($res_project->projects_id);

			$user = $this->getRequest()->getParam('user');
			if (!$res_project->userIsAdmin($user) && !$user->is_admin) {
				$this->_redirect("/");
			}

			$msg = "Ok";

			$tabgroup = split("-", $_GET['group']);
			$tabrights = split("-", $_GET['rights']);
			$j = 0;
			foreach ($tabgroup as $group)
			{
				$table_group = new USVN_Db_Table_Groups();
				$res_groups = $table_group->findByGroupsName($group);
				$acces_rights->setRightByPath(
					$res_groups->groups_id,
					$_GET['name'],
					($tabrights[$j] == 'true' ? true : false),
					($tabrights[$j + 1] == 'true' ? true : false)
				);
				$j += 2;
			}
		}
		catch (Exception $e)
		{
			$msg = nl2br($e->getMessage());
		}
		echo "<msg>" . $msg . "</msg>\n";
	}
}

