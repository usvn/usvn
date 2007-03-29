<?php

class USVN_modules_default_models_UsersToProjects extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 *
	 * @var string
	 */
	protected $_primary = array("users_id", "projects_id");

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
    protected $_name = "to_have";


    /**
     * Associative array map of declarative referential integrity rules.
     * This array has one entry per foreign key in the current table.
     * Each key is a mnemonic name for one reference rule.
     *
     * Each value is also an associative array, with the following keys:
     * - columns       = array of names of column(s) in the child table.
     * - refTableClass = class name of the parent table.
     * - refColumns    = array of names of column(s) in the parent table,
     *                   in the same order as those in the 'columns' entry.
     * - onDelete      = "cascade" means that a delete in the parent table also
     *                   causes a delete of referencing rows in the child table.
     * - onUpdate      = "cascade" means that an update of primary key values in
     *                   the parent table also causes an update of referencing
     *                   rows in the child table.
     *
     * @var array
     */
    protected $_referenceMap = array(
    	"Project" => array(
    			"columns"       => array("projects_id"),
    			"refTableClass" => "USVN_modules_default_models_Projects",
    			"refColumns"    => array("projects_id"),
    		),
    	"Users" => array(
    			"columns"         => array("users_id"),
    			"refTableClass"   => "USVN_modules_default_models_Users",
    			"refColumns"      => array("users_id"),
    		),
    	);

}
