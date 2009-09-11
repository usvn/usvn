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
	 * Add user in a group
	 *
	 * @param mixed Group
	 */
	public function addGroup($group)
	{
		if (is_numeric($group)) {
			$groups = new USVN_Db_Table_Groups();
			$group = $groups->find($group)->current();
		}
		$group->addUser($this);
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
		$where = $user_groups->getAdapter()->quoteInto('users_id = ?', $this->id);
		$user_groups->delete($where);
	}

	/**
	 * Check if an user is in the group
	 *
	 * @param USVN_Db_Table_Row_User User
	 * @return boolean
	 */
	public function isInGroup($group)
	{
		if ($this->_cleanData  === array()) {
			return false;
		}
		$user_groups = new USVN_Db_Table_UsersToGroups();
		$res = $user_groups->fetchRow(
		array(
		"groups_id = ?" => $group->groups_id,
		"users_id = ?" 	=> $this->id
		)
		);
		if ($res === NULL) {
			return false;
		}
		return true;
	}

	/**
	 * Return list of groups where user is member
	 *
	 * @return Zend_Db_Table_Rowset
	 */
	public function listGroups()
	{
		$groupTable = new USVN_Db_Table_Groups();
		$select = $groupTable->select();
		$select->order('groups_name');
		return $this->findManyToManyRowset('USVN_Db_Table_Groups',
		 'USVN_Db_Table_UsersToGroups',
		  null, null, $select);
	}

	/**
	 * Get all groups associated with $project for this user
	 *
	 * Project can be the project name as a string or a project row.
	 *
	 * @param string|USVN_Db_Table_Row_Project $project
	 * @return Zend_Db_Table_Rowset
	 */
	public function getAllGroupsFor($project)
	{
		if (! is_object($project)) {
			$projects = new USVN_Db_Table_Projects();
			$project = $projects->findByName($project);
		}
		$groups = new USVN_Db_Table_Groups();
		return $groups->fetchAllForUserAndProject($this, $project);
	}

	/**
	 * Check if the login is valid or not
	 *
	 * @throws USVN_Exception
	 * @param string
	 * @todo check on the default's login ?
	 * @todo regexp on the login ?
	 */
	protected function checkLogin($login)
	{
		if (empty($login) || preg_match('/^\s+$/', $login)) {
			throw new USVN_Exception(T_('Login empty.'));
		}
		if (!preg_match('/^[0-9a-zA-Z_\-]+$/', $login)) {
			throw new USVN_Exception(T_('Login invalid.  The login can only include alpha-numeric characters and \'-\' or \'_\'.'));
		}
	}

	/**
	 * Check if the Email address is valid or not
	 *
	 * @throws USVN_Exception
	 * @param string
	 */
	protected function checkEmailAddress($email)
	{
		if (strlen($email)) {
			$validator = new Zend_Validate_EmailAddress();
			if (!$validator->isValid($email)) {
				throw new USVN_Exception(T_('Invalid email address.'));
			}
		}
	}

	/**
	 * Check if the password is valid or not
	 *
	 * @throws USVN_Exception
	 * @param string
	 */
	protected function checkPassword($password)
	{
		if (empty($password) || preg_match('/^\s+$/', $password)) {
			throw new USVN_Exception(T_('Password empty.'));
		}
		/**
		 * This is not really cool when adding users from LDAP...
		 */
		// if (strlen($password) < 8) {
		// 	throw new USVN_Exception(T_('Password incorrect (need more 8 characters).'));
		// }
	}

	/**
	 * Crypt user password
	 *
	 * @return void
	 * @throws USVN_Exception, Zend_Exception
	 */
	protected function _insert()
	{
		$this->checkLogin($this->_data['users_login']);
		$this->checkEmailAddress($this->_data['users_email']);
		$this->checkPassword($this->_data['users_password']);
		$this->_data['users_password'] = USVN_Crypt::crypt($this->_data['users_password']);
	}

	/**
	 * Crypt user password if changed
	 * Check login if changed
	 *
	 * @return void
	 * @throws USVN_Exception, Zend_Exception
	 */
	protected function _update()
	{
		$this->checkEmailAddress($this->_data['users_email']);
		if ($this->_data['users_login'] != $this->_cleanData['users_login']) {
			$user = $this->getTable()->fetchRow(array("users_login = ?" => $this->_data['users_login']));
			if ($user !== null) {
				throw new USVN_Exception(sprintf(T_("Login %s already exist."), $user->login));
			}
			$this->checkLogin($this->_data['users_login']);
		}
		if ($this->_data['users_password'] != $this->_cleanData['users_password']) {
			$this->checkPassword($this->_data['users_password']);
			$this->_data['users_password'] = USVN_Crypt::crypt($this->_data['users_password']);
		}
	}
}
