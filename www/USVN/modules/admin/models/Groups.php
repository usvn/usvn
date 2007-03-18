<?php

class USVN_modules_admin_models_Groups extends USVN_Db_Table {
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
	 * Table of users (cache)
	 *
	 * @var array
	 */
    //private $_users = array();

    /**
     * Add group in database
     * @param string $name user's name
     * @return bool action performed or not
     */
    public function add($name)
    {
		$row = array('name' => $name);

		$last_insert_id = $this->insert($row);
    }

    public function edit()
    {

    }

    public function deleteGroup($id)
    {
		$db = $this->getAdapter();
		$where = $db->quoteInto('id = ?', $id);
		$rows_affected = $this->delete($where);
    }

    public function listAll()
    {
    	$table = new USVN_modules_default_models_Group();
		$groups = $table->fetchAll();
		return $groups;
    }
}
