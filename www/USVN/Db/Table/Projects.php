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
     * Associative array map of declarative referential integrity rules.
     * This array has one entry per foreign key in the current table.
     * Each key is a mnemonic name for one reference rule.
     *
     * Each value is also an associative array, with the following keys:
     * - columns    = array of names of column(s) in the child table.
     * - refTable   = class name of the parent table.
     * - refColumns = array of names of column(s) in the parent table,
     *                in the same order as those in the 'columns' entry.
     * - onDelete   = "cascade" means that a delete in the parent table also
     *                causes a delete of referencing rows in the child table.
     * - onUpdate   = "cascade" means that an update of primary key values in
     *                the parent table also causes an update of referencing
     *                rows in the child table.
     *
     * @var array
     */
    protected $_referenceMap = array();

    /**
     * Simple array of class names of tables that are "children" of the current
     * table, in other words tables that contain a foreign key to this one.
     * Array elements are not table names; they are class names of classes that
     * extend Zend_Db_Table_Abstract.
     *
     * @var array
     */
    protected $_dependentTables = array("USVN_Db_Table_UsersToProjects");

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
}
