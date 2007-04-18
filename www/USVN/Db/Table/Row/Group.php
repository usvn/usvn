<?php
/**
 * A rown into groups table
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
class USVN_Db_Table_Row_Group extends USVN_Db_Table_Row
{
	/**
	* Add an user to a group
	*
	* @param USVN_Db_Table_Row_User User
	*/
	public function addUser($user)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$user_groups->insert(
			array(
				"groups_id" => $this->id,
				"users_id" => $user->id
			)
		);
	}

	/**
	* Check if an user is in the group
	*
	* @param USVN_Db_Table_Row_User User
	* @return boolean
	*/
	public function userIsMember($user)
	{
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$res = $user_groups->fetchRow(
			array(
				"groups_id = ?" => $this->id,
				"users_id = ?" => $user->id
			)
		);
		if ($res === NULL) {
			return false;
		}
		return true;
	}
}
