<?php
/**
 * Model for files_rights table
 * Extends USVN_Db_Table for magic configuration and methods
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
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
 * $Id: Users.php 400 2007-05-13 15:15:38Z billar_m $
 */
class USVN_Db_Table_FilesRights extends USVN_Db_TableAuthz {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = "files_rights_id";

	/**
	 * The field's prefix for this table.
	 *
	 * @var string
	 */
	protected $_fieldPrefix = "files_rights_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "files_rights";

	/**
	 * Associative array map of declarative referential integrity rules.
	 * This array has one entry per foreign key in the current table.
	 * Each key is a mnemonic name for one reference rule.
	 *
	 * @var array
	 */
	protected $_referenceMap = array(
		"Projects" => array(
			"columns"		=> "projects_id",
			"refColumns"	=> "projects_id",
			"refTableClass" => "USVN_Db_Table_Projects",
			"onDelete"		=> self::CASCADE,
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
	protected $_dependentTables = array("USVN_Db_Table_GroupsToFilesRights");

    private function _cleanData(array $data)
    {
		if (isset($data['files_rights_path'])) {
            $data['files_rights_path'] = rtrim($data['files_rights_path'], '/');
            if (strlen($data['files_rights_path']) == 0) {
                $data['files_rights_path'] = '/';
            }
        }
        return $data;
    }

	/**
	 * Overload insert's method to check some data before insert
	 *
	 * @param array $data
	 * @return integer the last insert ID.
	 */
	public function insert(array $data)
	{
		return parent::insert($this->_cleanData($data));
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
		return parent::update($this->_cleanData($data), $where);
	}

	/**
	 * Return the rights by his path
	 *
	 * @param integer Project id
	 * @param string $name
	 * @return USVN_Db_Table_Row
	 */
	public function findByPath($project, $path)
	{
        $path = rtrim($path, '/');
        if (strlen($path) == 0) {
            $path = '/';
        }
		$db = $this->getAdapter();
		/* @var $db Zend_Db_Adapter_Pdo_Mysql */
		return $this->fetchRow(array(
				"files_rights_path = ?" => $path,
				"projects_id = ?" => $project,
			), "files_rights_path");
	}
}
