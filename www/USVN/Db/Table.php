<?php
/**
 * Base class for SQL table interface.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package db
 * @subpackage table
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
abstract class USVN_Db_Table extends Zend_Db_Table {
	/**
	 * The table prefix.
	 *
	 * Must be the same for all tables and is configurable via .ini (database.prefixe)
	 *
	 * @var string
	 * @access static
	 */
	static public $prefix = "";

	/**
	 * The field's prefix for this table.
	 *
	 * All fields must have the same prefixe.
	 * Configurable via your class declaration.
	 *
	 * @var string
	 * @see Users
	 */
	protected $_fieldPrefix = "";

	/**
	 * Name of the Row object to instantiate when needed.
	 *
	 * @var string
	 */
	protected $_rowClass = "USVN_Db_Table_Row";

	/**
	 * The primary key column (underscore format).
	 *
	 * Without your prefixe...
	 *
	 * @var string
	 * @see Users
	 */
	protected $_primary = 'id';



	/**
	 * Populate static properties for this table module.
	 *
	 * @return void
	 * @throws Zend_Db_Table_Exception
	 */
	protected function _setup($config = array())
	{
		// get the table name
		if (isset($config['name'])) {
			$this->_name = $config['name'];
		}
		if (! $this->_name) {
			$this->_name = self::$_inflector->underscore(get_class($this));
		}

		$this->_name = self::$prefix . $this->_name;

		if ($this->_fieldPrefix) {
			if (!is_array($this->_primary)) {
				$this->_primary = $this->_fieldPrefix . $this->_primary;
			} else {
				foreach ($this->_primary as &$field) {
					$field = $this->_fieldPrefix . $field;
				}
			}
		}

		parent::_setup($config);
	}

	/**
	 * Returns table information.
	 *
	 * @return array
	 */
	public function info()
	{
		$info = parent::info();
		$info = array_merge($info, array("fieldPrefix" => $this->_fieldPrefix));
		return $info;
	}

}
