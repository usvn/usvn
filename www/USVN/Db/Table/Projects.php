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
class USVN_Db_Table_Projects extends USVN_Db_TableAuthz {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = "projects_id";

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
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_FilesRights", "USVN_Db_Table_UsersToProjects", "USVN_Db_Table_GroupsToProjects");

	/**
	 * Return the project by his name
	 *
	 * @param string $name
	 * @return USVN_Db_Table_Row
	 */
	public function findByName($name)
	{
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter */
		$where = $db->quoteInto("projects_name = ?", $name);
		return $this->fetchRow($where, "projects_name");
	}

	/**
	 * Overload insert's method to check some data before insert
	 *
	 * @param array $data
	 * @return integer the last insert ID.
	 */
	public function insert(array $data)
	{
		if (!$data['projects_start_date']) {
			$data['projects_start_date'] = date("Y-m-d H:i:s");
		}

		return parent::insert($data);
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
		if ($project === null) {
			return false;
		}
		return true;
	}

	
	
	/**
	 * Add a row in users_to_project
	 *
	 * @param int $user
	 * @param int $project
	 */
	public function AddUserToProject($user, $project)
	{
		if ($_POST['admin'] == true) {
			$create = new USVN_Db_Table_UsersToProjects();
			$add = $create->createRow(array('users_id' => $user->users_id, 'projects_id' => $project->projects_id));
			$add->save();
		} else {
			$delete = new USVN_Db_Table_UsersToProjects();
			$delete->delete(array('users_id = ?' => $user->users_id), array('projects_id = ?', $project->projects_id));
		}
	}
	
	/**
     * Fetches all groups and joins with users
     *
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
     */
	public function fetchAllAndFilesRightsAndGroups()
	{
		// selection tool
		$select = $this->_db->select();

		// the FROM clause
		$select->from($this->_name, array("projects_name"));

		// the JOIN clause
		$files_rights           = self::$prefix . "files_rights";
		$groups_to_files_rights = self::$prefix . "groups_to_files_rights";
		$groups                 = self::$prefix . "groups";

		$select->joinLeft($files_rights, "$files_rights.projects_id = {$this->_name}.projects_id", array("files_rights_path"));
		$select->joinLeft($groups_to_files_rights, "$files_rights.files_rights_id = $groups_to_files_rights.files_rights_id", array("files_rights_is_readable", "files_rights_is_writable"));
		$select->joinLeft($groups, "$groups.groups_id = $groups_to_files_rights.groups_id", array("groups_name"));

		// the ORDER clause
		$select->order("projects_name");
		$select->order("files_rights_path");
		$select->order("groups_name");

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
	 * Select all projects related to user $user
	 *
	 * @param USVN_Db_Table_Row_User $user
     * @return Zend_Db_Table_Rowset_Abstract The row results per the Zend_Db_Adapter fetch mode.
	 */
	public function fetchAllAssignedTo(USVN_Db_Table_Row_User $user)
	{
		// selection tool
		$select = $this->_db->select();
		/* @var $select Zend_Db_Select */

		// the FROM clause
		$select->from($this->_name);

		$groups_to_projects = self::$prefix . "groups_to_projects";
		$users_to_groups    = self::$prefix . "users_to_groups";
		$users              = self::$prefix . "users";

		$select->join($groups_to_projects, "{$groups_to_projects}.projects_id = {$this->_name}.projects_id", array());
		$select->join($users_to_groups, "{$users_to_groups}.groups_id = {$groups_to_projects}.groups_id", array());
		$select->join($users, "{$users}.users_id = {$users_to_groups}.users_id", array());

		$select->where($this->_db->quoteInto("{$users}.users_id = ?", $user->id));

		// the GROUP clause
		$select->group("{$this->_name}.projects_id");

		// the ORDER clause
		$select->order("projects_name");

		// return the results
		$stmt = $this->_db->query($select);
		$data = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

		// selection tool
		$select = $this->_db->select();
		/* @var $select Zend_Db_Select */

		// the FROM clause
		$select->from($this->_name);

		$users_to_projects = self::$prefix . "users_to_projects";
		$users              = self::$prefix . "users";

		$select->join($users_to_projects, "{$users_to_projects}.projects_id = {$this->_name}.projects_id", array());
		$select->join($users, "{$users}.users_id = {$users_to_projects}.users_id", array());

		$select->where($this->_db->quoteInto("{$users}.users_id = ?", $user->id));

		// the ORDER clause
		$select->order("projects_name");

		// return the results
		$stmt = $this->_db->query($select);
		$data2 = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

		//merge results
		$merged_data = array_merge($data, $data2);

		//make results unique
		$result = array();
		foreach ($merged_data as $elem) {
			$flag = 1;
			foreach ($result as $elem2) {
				if ($elem['projects_name'] == $elem2['projects_name']) {
					$flag = 0;
					break;
				}
			}
			if ($flag) {
				$result[] = $elem;
			}
		}

		// return the results
		$data  = array(
		'table'    => $this,
		'data'     => $result,
		'rowClass' => $this->_rowClass,
		'stored'   => true
		);

		Zend_Loader::loadClass($this->_rowsetClass);
		return new $this->_rowsetClass($data);
	}
}
