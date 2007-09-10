<?php
/**
 * Project management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package completion
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class CompletionController extends USVN_Controller
{
    protected $_mimetype = "text/xml";

	public function completionAction()
	{
		echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$table = "<table width=100%>";
		$nb = 0;
		echo "<files>\n";
	
		if ($_GET['idx'] == 1)
		{
			if (isset($_GET['grp']) && $_GET['grp'] != "")
			{
				$table_groups = new USVN_Db_Table_Groups();
				$res_groups = $table_groups->findByGroupsName($_GET['grp']);
				$table_userstogroups = new USVN_Db_Table_UsersToGroups();
				$res_usersspe = $table_userstogroups->findByGroupId($res_groups->groups_id);
			}
			else
			{
				$table_project = new USVN_Db_Table_Projects();
				$res_project = $table_project->findByName($this->_request->getParam('project'));
				$table_userstoprojects = new USVN_Db_Table_UsersToProjects();
				$res_usersspe = $table_userstoprojects->findByProjectId($res_project->projects_id);
			}
			$table_users = new USVN_Db_Table_Users();
			$res_users = $table_users->allUsersLike($_GET['txt']);
			foreach ($res_users as $user)
			{
				$find = false;
				foreach($res_usersspe as $tmpuser)
					if ($tmpuser->users_id == $user->users_id)
						$find = true;
				if ($find == false)
				{
					$table .= "<tr id='user".$nb."' class='comp'>";
					$table .= "<td align=left onclick='javascript:dumpInput("."\"".$user->users_login."\"".","."\"".$_GET['input']."\"".", \"completion\")'>";
					$table .= "<label id='luser".$nb."'>".$user->users_login."</label>";
					$table .= "</td></tr>";
					$nb++;
				}
			}
		}
		if ($_GET['idx'] == 2)
		{
			$table_project = new USVN_Db_Table_Projects();
			$res_project = $table_project->findByName($this->_request->getParam('project'));
			$table_groupstoprojects = new USVN_Db_Table_GroupsToProjects();
			$res_groupstoprojects = $table_groupstoprojects->findByProjectId($res_project->projects_id);

			$table_groups = new USVN_Db_Table_Groups();
			$res_groups = $table_groups->allGroupsLike($_GET['txt']);
			foreach ($res_groups as $group)
			{
				$find = false;
				foreach($res_groupstoprojects as $tmpgrp)
					if ($tmpgrp->groups_id == $group->groups_id)
						$find = true;
				if ($find == false)
				{
					$table .= "<tr id='grp".$nb."' class='comp'>";
					$table .= "<td align=left onclick='javascript:dumpInput("."\"".$group->groups_name."\"".","."\"".$_GET['input']."\"".", \"completion1\")'>";
					$table .= "<label id='lgrp".$nb."'>".$group->groups_name."</label>";
					$table .= "</td></tr>";
					$nb++;
				}
			}
		}
		$table .= "</table>";
		echo "<nbcomp>".$nb."</nbcomp>\n";
		echo "<tableau><![CDATA[".$table."]]></tableau>\n";
		echo "</files>\n";
	}
}
