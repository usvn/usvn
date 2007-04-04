<?php
/**
 * Model for groups table
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

/**
 * Model for groups table
 *
 * Extends USVN_Db_Table for magic configuration and methods
 *
 */
class USVN_Db_Table_Groups extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = "id";

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
	 * Associative array map of declarative referential integrity rules.
	 * This array has one entry per foreign key in the current table.
	 * Each key is a mnemonic name for one reference rule.
	 *
	 * Each value is also an associative array, with the following keys:
	 * - columns	= array of names of column(s) in the child table.
	 * - refTable   = class name of the parent table.
	 * - refColumns = array of names of column(s) in the parent table,
	 *				in the same order as those in the 'columns' entry.
	 * - onDelete   = "cascade" means that a delete in the parent table also
	 *				causes a delete of referencing rows in the child table.
	 * - onUpdate   = "cascade" means that an update of primary key values in
	 *				the parent table also causes an update of referencing
	 *				rows in the child table.
	 *
	 * @var array
	 */
	protected $_referenceMap = array(
	"Users" => array(
	"columns"	=> array("users_id"),
	"refTable"   => "USVN_Db_Table_Users",
	"refColumns" => array("users_id"),
	),
	);

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_Users");


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
		if (empty($name)) {
			throw new USVN_Exception(T_('The group\'s name is empty.'));
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
	 * @return integer The number of rows updated.
	 */
	public function update(&$data, $where)
	{
		$this->checkGroupName($data['groups_name']);
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
		if ($group->name) {
			return true;
		}
		return false;
	}
}
