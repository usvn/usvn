<?php

class USVN_modules_default_models_Properties extends USVN_Db_Table {
	/**
	 * The primary key column (underscore format).
	 *
	 * Without our prefixe...
	 * 
	 * @var array
	 */
	protected $_primary = array("projects_id", "revisions_num");
	
	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
    protected $_name = "properties";

    

}
