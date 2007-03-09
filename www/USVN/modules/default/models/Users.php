<?php

class Users extends USVN_Db_Table {
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
	protected $_fieldPrefix = "user_";
}
