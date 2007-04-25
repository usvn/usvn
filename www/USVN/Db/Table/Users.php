<?php
/**
 * Model for users table
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
 * $Id$
 */

/**
 * Model for users table
 *
 * Extends USVN_Db_Table for magic configuration and methods
 *
 */
class USVN_Db_Table_Users extends USVN_Db_Table {
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
	protected $_fieldPrefix = "users_";

	/**
	 * The table name derived from the class name (underscore format).
	 *
	 * @var array
	 */
	protected $_name = "users";

	/**
	 * Name of the Row object to instantiate when needed.
	 *
	 * @var string
	 */
	protected $_rowClass = "USVN_Db_Table_Row_User";

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
	protected $_referenceMap = array();

	/**
	 * Simple array of class names of tables that are "children" of the current
	 * table, in other words tables that contain a foreign key to this one.
	 * Array elements are not table names; they are class names of classes that
	 * extend Zend_Db_Table_Abstract.
	 *
	 * @var array
	 */
	protected $_dependentTables = array("USVN_Db_Table_UsersToGroups");

	/**
	 * Expected entries like anonymous user
	 *
	 * @var array
	 */
	public $exceptedEntries = array('users_login' => 'anonymous');

	/**
	 * Check if the login is valid or not
	 *
	 * @throws USVN_Exception
	 * @param string
	 * @todo check on the default's login ?
	 * @todo regexp on the login ?
	 */
	public function checkLogin($login)
	{
		if (empty($login) || preg_match('/^\s+$/', $login)) {
			throw new USVN_Exception(T_('Login empty.'));
		}
		if (!preg_match('/\w+/', $login)) {
			throw new USVN_Exception(T_('Login invalid.'));
		}
	}

	/**
	 * Check if the password is valid or not
	 *
	 * @throws USVN_Exception
	 * @param string
	 */
	public function checkPassword($password)
	{
		if (empty($password) || preg_match('/^\s+$/', $password)) {
			throw new USVN_Exception(T_('Password empty.'));
		}
		if (strlen($password) < 8) {
			throw new USVN_Exception(T_('Invalid password (at least 8 Characters).'));
		}
	}

	/**
	 * Check if the Email address is valid or not
	 *
	 * @throws USVN_Exception
	 * @param string
	 */
	public function checkEmailAddress($email)
	{
		if (strlen($email)) {
			$validator = new Zend_Validate_EmailAddress();
			if (!$validator->isValid($email)) {
				throw new USVN_Exception(T_('Invalid email address.'));
			}
		}
	}

	/**
	* Do all checks
	*
	* @param array $data informations about the user to save
	*/
	private function check($data)
	{
		$this->checkLogin($data['users_login']);
		$this->checkPassword($data['users_password']);
		if (isset($data['users_email'])) {
			$this->checkEmailAddress($data['users_email']);
		}
	}

	/**
	 * Inserts a new row
	 *
	 * @param array Column-value pairs.
	 * @return integer The last insert ID.
	 */
	public function insert(array $data)
	{
		$this->check($data);
		return parent::insert($data);
	}

	/**
	 * Updates existing rows.
	 *
	 * @param array Column-value pairs.
	 * @param string An SQL WHERE clause.
	 * @return int The number of rows updated.
	 */
	public function update(array $data, $where)
	{
		$this->checkLogin($data['users_login']);
		//just for update's case
		if (isset($data['users_password'])) {
			$this->checkPassword($data['users_password']);
		}
		if (isset($data['users_email'])) {
			$this->checkEmailAddress($data['users_email']);
		}
		return parent::update($data, $where);
	}

	/**
	 * To know if the user already exists or not
	 *
	 * @param string
	 * @return boolean
	 */
	public function isAUser($login)
	{
		$user = $this->fetchRow(array('users_login = ?' => $login));
		if ($user === NULL) {
			return false;
		}
		return true;
	}
}
