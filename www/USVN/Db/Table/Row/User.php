<?php
/**
 * A rown into user table
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Row
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id $
 */
class USVN_Db_Table_Row_User extends USVN_Db_Table_Row
{
	/**
	* Add an group to a user
	*
	* @param mixed Group
	*/
	public function addGroup($group)
	{
		if (is_object($group)) {
			$group_id = $group->id;
		} elseif (is_numeric($group)) {
			$group_id = intval($group);
		}
		if ($this->id && $group_id) {
			$user_groups = new USVN_Db_Table_UsersToGroups();
			$user_groups->insert(
				array(
					"groups_id" => $group_id,
					"users_id" 	=> $this->id
				)
			);
		}
	}

	/**
	* Delete an group to a group
	*
	* @param mixed User
	*/
	public function deleteGroup($group)
	{
		if (is_object($group)) {
			$group_id = $group->id;
		} elseif (is_numeric($group)) {
			$group_id = intval($group);
		}
		if ($group_id) {
			$user_groups = new USVN_Db_Table_UsersToGroups();
			$where = $user_groups->getAdapter()->quoteInto('groups_id = ?', $group_id);
			$user_groups->delete($where);
		}
	}

	/**
	 * Delete all groups from usersToGroups
	 *
	 */
	public function deleteAllGroups()
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$user_groups->delete("");
	}

	/**
	* Check if an user is in the group
	*
	* @param USVN_Db_Table_Row_User User
	* @return boolean
	*/
	public function isInGroup($group)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$res = $user_groups->fetchRow(
			array(
				"groups_id = ?" => $group->id,
				"users_id = ?" 	=> $this->id
			)
		);
		if ($res === NULL) {
			return false;
		}
		return true;
	}
}
