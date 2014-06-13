<?php
/**
 * User management.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage users
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_User
{
	/**
	 * User row object
	 *
	 * @var USVN_Db_Table_Row_User
	 */
	private $user = null;

	/**
	 * Indicate that a group creation is mandatory
	 *
	 * @var boolean
	 */
	private $createGroup = false;

	/**
	 * All groups id which user is affected
	 *
	 * Any non null value will delete all groups association and try to recreate it.
	 *
	 * @var array|null
	 */
	private $groups = null;

	/**
	 * Direct instantiation is forbiddem
	 *
	 */
	private function __construct() {}

	/**
	 * Create a new user
	 *
	 * @param array User attributes
	 * @param boolean true : create a homonym group's
	 * @param array|null Group's id which this user must be affected
	 * @return USVN_User
	 */
	static public function create($data, $createGroup, $groups = null)
	{
		$user = new USVN_User();
		$table = new USVN_Db_Table_Users();
		$user->user = $table->createRow($data);
		$user->createGroup = $createGroup;
		$user->groups = $groups;
		return $user;
	}

	/**
	 * Update an existing user
	 *
	 * @param USVN_Db_Table_User $login
	 * @param array $data
	 * @param array|null $groups
	 * @return USVN_User
	 */
	static public function update($user, $data, $groups = null)
	{
		$u = new USVN_User();
		$u->user = $user;
		$u->groups = $groups;
		return $u;
	}

	/**
	 * Save all information
	 *
	 */
	public function save()
	{
		$this->user->save();
		if ($this->createGroup) {
			$table = new USVN_Db_Table_Groups();
			$group = $table->createRow(array("groups_name" => $this->user->login));
			try {
				$group->save();
				$this->groups[] = $group->id;
			}
			catch (Exception $e) {
				$table = new USVN_Db_Table_Groups();
				$where = $table->getAdapter()->quoteInto('groups_name = ?', $this->user->login);
				$row = $table->fetchRow($where);
				if ($row != null) {
					$this->user->delete();
					throw new USVN_Exception(T_("This user can't be created. A group has the same name."));
				}
				else {
					throw $e;
				}
			}
		}


		if ($this->groups !== null) {
			$this->user->deleteAllGroups();
			foreach ($this->groups as $group) {
				$this->user->addGroup($group);
			}
		}
	}

	/**
	 * Get the user's row object
	 *
	 * @return USVN_Db_Table_Row_User
	 */
	public function getRowObject()
	{
		return $this->user;
	}
}
