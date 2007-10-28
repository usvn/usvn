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

	public function preDispatch()
	{
		parent::preDispatch();
		if ($_GET['name'] == 'nop') {
			$_GET['name'] = "/";
		}
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		echo "<files>\n";
	}
	
	public function postDispatch()
	{
		echo "</files>";
		parent::postDispatch();
	}
	
	private function getTopLink($path)
	{
		$str = "<h2>";
		$str .= '<a href=\'javascript:getListFile("/");\'>root</a>&nbsp;/&nbsp;';
		$list = array();
		$path = preg_replace("#/+#", '/', $path);
		while ($path != '/' && $path != '\\') {
			array_push($list, $path);
			$path = dirname($path);
		}
		$list = array_reverse($list);
		foreach ($list as $path) {
			$str .= '<a href=\'javascript:getListFile("' . urlencode($path) . '");\'>' . basename($path) . '</a>&nbsp;/&nbsp;';
		}
		$str .= "</h2>";
		$str .= "<h3><a href='javascript:dumpRights(\"{$path}\");'>";
		$str .= sprintf(T_("Apply rights on %s"), $path);
		$str .= "</a></h3>";
		return $str;
	}

	/**
	 * Get list files of subversion directory.
	 */
	public function GetListFileAction()
	{
		$path = $_GET['name'];
		$SVN = new USVN_SVN($this->_request->getParam('project'));
		$tab = $SVN->listFile($path);
		ob_start();
		echo $this->getTopLink($path);
		echo "<br />";

		$user = $this->getRequest()->getParam('user');
		/* @var $user USVN_Db_Table_Row_User */
		$table = new USVN_Db_Table_Projects();
		$project = $table->findByName($this->getRequest()->getParam('project'));

		$read = false;
		$acces_rights = new USVN_FilesAccessRights($project->id);
		$groups = $user->getAllGroupsFor($project);
		foreach ($groups as $group) {
			$access = $acces_rights->findByPath($group->id, $path);
			if ($access['read']) {
				$read = true;
				break;
			}
		}

		if (!$read && ($user->is_admin || $project->userIsAdmin($user))) {
			$read = true;
		}

		if ($read) {
			echo '<table class="usvn_table">';
			echo '<thead>';
			echo '<tr><th>', T_('Name'), '</th><th>', T_('Action'), '</th></tr>';
			echo '</thead>';
			echo '<tbody>';
			if ($path != "/" && $path != "//") {
				if (dirname($path) == "\\") {
					$pathbefore = "/";
				} else {
					$pathbefore = dirname($path) . "/";
				}
				echo "<tr><td>" .$this->view->img('CrystalClear/16x16/filesystems/folder_blue.png', T_('Folder'));
				echo "<a href='javascript:getListFile(" . "\"". $pathbefore . "\"" . ");'>..</a></td><td></td></tr>";
			}
			foreach ($tab as &$tabl) {
				$tabl['path'] = urlencode($tabl['path']);
				echo "<tr>";
				$dir = false;
				if ($tabl['isDirectory'] == 1) {
					$dir = true;
					$tabl['isDirectory'] = $this->view->img('CrystalClear/16x16/filesystems/folder_blue.png', T_('Folder'));
				}
				else{
					$tabl['isDirectory'] = $this->view->img('CrystalClear/16x16/mimetypes/document.png', T_('File'));
				}
				echo "<td>{$tabl['isDirectory']}";
				if ($dir) {
					echo "<a href='javascript:getListFile(\"{$tabl['path']}\");'>{$tabl['name']}</a></td>";
				} else {
					echo "<a>{$tabl['name']}</a></td>";
				}
				echo "<td><a href='javascript:dumpRights(\"{$tabl['path']}\");'>" .$this->view->img('CrystalClear/16x16/apps/kwalletmanager.png', T_('Rights')) . "</a></td></tr>";
			}
			echo "</tbody></table>";
		} else {
			echo T_("You don't have read access to this folder");
		}
		$txthtml = ob_get_clean();
		echo "<txthtml><![CDATA[";
		echo $txthtml;
		echo "]]></txthtml>\n";
		$this->view->browser = $tab;
	}

	/**
	 * If files exist dump rights
	 */
	public function DumpRightsAction()
	{
		$text = "";
		$table_project = new USVN_Db_Table_Projects();
		$res_project = $table_project->findByName($this->_request->getParam('project'));

		$acces_rights = new USVN_FilesAccessRights($res_project->projects_id);

		$table_files = new USVN_Db_Table_FilesRights();
		$table_groupstoproject = new USVN_Db_Table_GroupsToProjects();
		$res_groupstoproject = $table_groupstoproject->findByProjectId($res_project->projects_id);
		$table_groups = new USVN_Db_Table_Groups();
		$text = "<table class='usvn_table' width=22% cellpadding=3><tr><td>".T_('Group')."</td><td>".T_('Read')."</td><td>".T_('Write')."</td></tr>";
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
			$text .= "<tr><td weight=10%><label id=Lb".$i.">".$grp_name."</label></td>";
			if ($access['read'] == 1)
			$text .= "<td width=3% align=center><input id='checkRead".$grp_name."' type='checkbox' checked onclick='javascript:fctRead("."\"".$grp_name."\"".");' $disabled/></td>";
			else
			$text .= "<td width=3% align=center><input id='checkRead".$grp_name."' type='checkbox' onclick='javascript:fctRead("."\"".$grp_name."\"".");' $disabled/></td>";
			if ($access['write'] == 1)
			$text .= "<td width=3% align=center><input id='checkWrite".$grp_name."' type='checkbox' checked onclick='javascript:fctWrite("."\"".$grp_name."\"".");' $disabled/></td></tr>";
			else
			$text .= "<td width=3% align=center><input id='checkWrite".$grp_name."' type='checkbox' onclick='javascript:fctWrite("."\"".$grp_name."\"".");' $disabled/></td></tr>";
			$i++;
		}
		$text .= "</table>";
		$text .= "<br /><table><tr><td><input type='button' value='Ok' onclick=\"javascript:updateOrInsertRights('" . $_GET['name'] . "');\" $disabled/></td><td>";
		$text .= "<input type='button' value='Cancel' onclick='javascript:cancel();'/></td></tr></table><label id='labelError'></label>";
		echo "<basename>".basename($_GET['name'])."</basename>";
		echo "<nbgroup>".$i."</nbgroup>\n";
		echo "<groups><![CDATA[".$text."]]></groups>\n";
	}

	/**
	 * If files exist update rights if not insert rights
	 */
	public function UpdateOrInsertRightsAction()
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

			$tabgroup = split(",", $_GET['group']);
			$tabrights = split(",", $_GET['rights']);
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

