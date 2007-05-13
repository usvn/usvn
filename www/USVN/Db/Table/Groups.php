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
class USVN_Db_Table_Groups extends USVN_Db_Table {
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
	protected $_dependentTables = array("USVN_Db_Table_UsersToGroups",
										"USVN_Db_Table_Workgroups"
										);

	/**
	 * Excepted entries
	 *
	 * @var array
	 */
	public $exceptedEntries = array('groups_name' => 'Anonymous');

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
		if (!preg_match('/\w+/', $name)) {
			throw new USVN_Exception(T_('The group\'s name is invalid.'));
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
		$res = parent::insert($data);
		USVN_Authz::generate();
		return $res;
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
		$this->checkGroupName($data['groups_name']);
		$res = parent::update($data, $where);
		USVN_Authz::generate();
		return $res;
	}

	/**
	 * Called by parent table's class during delete() method.
	 *
	 * @param  string $parentTableClassname
	 * @param  array  $primaryKey
	 * @return int    Number of affected rows
	 */
	public function delete($where)
	{
		$res = parent::delete($where);
		USVN_Authz::generate();
		return $res;
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
}
