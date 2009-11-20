<?php
/**
 * Model for groups table
 *
 * Extends USVN_Db_Table for magic configuration and methods
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info/
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package USVN_Db
 * @subpackage Table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id $
 */
class USVN_Db_Table_Groups extends USVN_Db_TableAuthz {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = "groups_id";

	/**
	 * The field's prefix for this table.
	 *
	 * @var string
	 */
	protected $_fieldPrefix = "groups_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "groups";

	/**
	 * Name of the Row object to instantiate when needed.
	 *
	 * @var string
	 */
	protected $_rowClass = "USVN_Db_Table_Row_Group";

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_UsersToGroups", "USVN_Db_Table_GroupsToFilesRights", "USVN_Db_Table_GroupsToProjects");

	/**
	 * Check if the group's name is valid or not
	 *
	 * @throws USVN_Exception
	 * @todo check on the default's name ?
	 * @todo regexp on group's name ?
	 * @todo other rules to define ?
	 * @param string $name group's name
	 */
	public function checkGroupName($name)
	{
		if (empty($name) || preg_match('/^\s+$/', $name)) {
			throw new USVN_Exception(T_('The group\'s name is empty.'));
		}
		if (!preg_match('/^[0-9a-zA-Z_\.\+\-]+$/', $name)) {
			throw new USVN_Exception(T_('The group\'s name is invalid. The group\'s name can only include alpha-numeric characters and \'-\' or \'_\'.'));
		}
	}

	/**
	 * Overload insert's method to check some data before insert
	 *
	 * @param array $data
	 * @return integer the last insert ID.
	 */
	public function insert(array $data)
	{
		$this->checkGroupName($data['groups_name']);
		return parent::insert($data);
	}

	/**
	 * Overload update's method to check some data before update
	 *
	 * @param array $data
	 * @param string $where An SQL WHERE clause.
	 * @return integer The number of rows updated.
	 */
	public function update(array $data, $where)
	{
        if (isset($data['groups_name'])) {
		    $this->checkGroupName($data['groups_name']);
        }
		return parent::update($data, $where);
	}

	/**
	 * To know if the group already exists or not
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function isAGroup($name)
	{
		$group = $this->fetchRow(array('groups_name = ?' => $name));
		if ($group === NULL) {
			return false;
		}
		return true;
	}

	/**
	 * Fetches all groups and joins with users
	 *
	 * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
	 */
	public function fetchAllAndUsers()
	{
		// selection tool
		$select = $this->_db->select();

		// the FROM clause
		$select->from($this->_name, $this->_getCols());

		// the JOIN clause
		$users = self::$prefix . "users";
		$users_to_groups = self::$prefix . "users_to_groups";
		$select->joinLeft($users_to_groups, "$users_to_groups.groups_id = {$this->_name}.groups_id",  array());
		$select->joinLeft($users, "$users.users_id = $users_to_groups.users_id");

		// the ORDER clause
		$select->order("groups_name");
		$select->order("users_login");

		// return the results
		$stmt = $this->_db->query($select);
		$data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

		$data  = array(
			'table'    => $this,
			'data'     => $data,
			'rowClass' => $this->_rowClass,
			'stored'   => true
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);
	}

	/**
	 * Return the groups by groups_id
	 *
	 * @param string $id
	 * @return USVN_Db_Table_Row
	 */
	public function findByGroupsId($id)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Pdo_Mysql */
		$where = $db->quoteInto("groups_id = ?", $id);
		return $this->fetchRow($where, "groups_id");
	}

	/**
	 * Return the groups by groups_name
	 *
	 * @param string $name
	 * @return USVN_Db_Table_Row
	 */
	public function findByGroupsName($name)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Pdo_Mysql */
		$where = $db->quoteInto("groups_name = ?", $name);
		return $this->fetchRow($where, "groups_name");
	}

	/**
	 * Return all groups like group name
	 *
	 * @param string
	 * @return USVN_Db_Table_Row
	 */
	public function allGroupsLike($match_group)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Pdo_Mysql */
		$where = $db->quoteInto("groups_name like ?", $match_group."%");
		return $this->fetchAll($where, "groups_name");
	}

	public function allLeader($group_id)
	{
		// selection tool
		$select = $this->_db->select();

		// the FROM clause
		$select->from($this->_name, $this->_getCols());

		// the JOIN clause
		$users = self::$prefix . "users";
		$users_to_groups = self::$prefix . "users_to_groups";
		$select->joinLeft($users_to_groups, "$users_to_groups.users_id = $users.users_id",  array());

		$select->where("$users_to_groups.is_leader = 1 and $users_to_groups.groups_id = ?", $group_id);

		// return the results
		$stmt = $this->_db->query($select);
		$data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

		$data  = array(
			'table'    => $this,
			'data'     => $data,
			'rowClass' => $this->_rowClass,
			'stored'   => true
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);
	}

    /**
	 * Get all groups for a specific user associated with a project.
	 *
	 * @param USVN_Db_Table_Row_User $user
	 * @param USVN_Db_Table_Row_Project $project
	 * @return Zend_Db_Table_Rowset
	 */
	public function fetchAllForUserAndProject($user, $project)
	{
		$select = $this->getAdapter()->select();
		/* @var $select Zend_Db_Select */

		$select->from(self::$prefix . "groups as g");
		$select->join(self::$prefix . "groups_to_projects as g2p", "g2p.groups_id = g.groups_id", array());
		$select->join(self::$prefix . "users_to_groups as u2g",    "u2g.groups_id = g.groups_id", array());

		$select->where("u2g.users_id = ?",    $user->id);
		$select->where("g2p.projects_id = ?", $project->id);

		// return the results
		$stmt = $this->_db->query($select);
		$data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

		$data  = array(
		'table'    => $this,
		'data'     => $data,
		'rowClass' => $this->_rowClass,
		'stored'   => true
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);

	}
}
