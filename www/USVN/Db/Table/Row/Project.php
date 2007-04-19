<?php
/**
 * A rown into project table
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
class USVN_Db_Table_Row_Project extends USVN_Db_Table_Row
{
	/**
	* Add a group to a project
	*
	* @todo review the database schema
	* @param USVN_Db_Table_Row_Right Right
	* @param USVN_Db_Table_Row_Group Group
	* @param USVN_Db_Table_Row_File File
	*/
	public function addGroup($right, $group, $file)
	{
		$toAttribute = new USVN_Db_Table_ToAttribute();
		$toAttribute->insert(
			array(
				"rights_id"		=> $right->id,
				"groups_id" 	=> $group->id,
				"projects_id"	=> $this->id,
				"files_id"		=> $file->id
			)
		);
	}

	/**
	* Check if an group is in the project
	*
	* @param USVN_Db_Table_Row_Right Right
	* @param USVN_Db_Table_Row_Group Group
	* @param USVN_Db_Table_Row_File File
	* @todo review the database schema
	* @return boolean
	*/
	public function groupIsMember($right, $group, $file)
	{
		$toAttribute = new USVN_Db_Table_ToAttribute();
		$res = $toAttribute->fetchRow(
			array(
				"rights_id"		=> $right->id,
				"groups_id" 	=> $group->id,
				"projects_id"	=> $this->id,
				"files_id"		=> $file->id
			)
		);
		if ($res === NULL) {
			return false;
		}
		return true;
	}
}
