<?php
/**
 * Model for projects table
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
 * Model for projects table
 *
 * Extends USVN_Db_Table for magic configuration and methods
 *
 */
class USVN_Db_Table_Projects extends USVN_Db_Table {
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
	protected $_fieldPrefix = "projects_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "projects";

	/**
	 * Name of the Row object to instantiate when needed.
	 *
	 * @var string
	 */
	protected $_rowClass = "USVN_Db_Table_Row_Project";

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
	"Workgroups" => array(
	"columns"	=> array("projects_id"),
	"refTable"   => "USVN_Db_Table_Workgroups",
	"refColumns" => array("workgroups_id", "groups_id", "projects_id"),
	)
	);

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_Workgroups");

	/**
	 * Excepted entries like __NONE__
	 *
	 * @var array
	 */
	public $exceptedEntries = array('projects_name' => '__NONE__');

	/**
	 * Return the project by his name
	 *
	 * @param string $name
	 * @return USVN_Db_Table_Row
	 */
	public function findByName($name)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Pdo_Mysql */
		$where = $db->quoteInto("projects_name = ?", $name);
		return $this->fetchRow($where, "projects_name");
	}

	/**
	 * Check if the project's name is valid or not
	 *
	 * @throws exception
	 * @todo regexp on project's name ?
	 * @todo check on the default's name ?
	 * @todo other rules to define ?
	 * @param string $name project's name
	 * @param throw USVN_Exception
	 */
	public function checkProjectName($name)
	{
		if (empty($name) || preg_match('/^\s+$/', $name)) {
			throw new USVN_Exception(T_('The project\'s name is empty.'));
		}
		if (!preg_match('/\w+/', $name)) {
			throw new USVN_Exception(T_('The project\'s name is invalid.'));
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
		$this->checkProjectName($data['projects_name']);
		$this->getAdapter()->beginTransaction();
		$id = parent::insert($data);
		$config = Zend_Registry::get('config');
		try {
			USVN_SVNUtils::createSVN($config->subversion->path . DIRECTORY_SEPARATOR . $data['projects_name']);
		}
		catch (Exception $e) {
			$this->getAdapter()->rollback();
			throw $e;
		}
		$this->getAdapter()->commit();
		return $id;
	}

	/**
	 * Overload update's method to check some data before update
	 *
	 * @todo check on project start date ?
	 * @todo check on project's description ? (length)
	 * @param array $datagetAdapter->
	 * @param string where SQL where
	 * @return integer The number of rows updated.
	 */
	public function update(array $data, $where)
	{
		$this->checkProjectName($data['projects_name']);
		return parent::update($data, $where);
	}

	/**
	 * To know if the project already exists or not
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function isAProject($name)
	{
		$project = $this->fetchRow(array('projects_name = ?' => $name));
		if ($project) {
			return true;
		}
		return false;
	}
}
