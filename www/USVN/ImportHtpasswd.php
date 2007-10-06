<?php
/**
 * Import users from an htpasswd file to USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 * @subpackage user
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

class USVN_ImportHtpasswd
{
	private $_file;
	private $_users_password = array();

	/**
	 * @param Path of the file to import
	 * @throw USVN_Exception
	 */
	public function __construct($file)
	{
		$this->_file = $file;
		$this->importFile();
		$this->loadIntoDb();
	}

	private function importFile()
	{
		$f = @fopen($this->_file, "r");
		if (!$f) {
			throw new USVN_Exception(T_("File {$this->_file} not found or invalid access rights."));
		}
		while (!feof($f)) {
			$line = fgets($f);
			$line = trim($line);
			if (strlen($line)) {
				$e = explode(":", $line);
				if (count($e) != 2) {
					throw new USVN_Exception(T_("Invalid htpasswd file."));
				}
				if (isset($this->_users_password[$e[0]])) {
					throw new USVN_Exception(T_("User %s already exist into htpasswd file."), $e[0]);
				}
				$this->_users_password[$e[0]] = $e[1];
			}
		}
		fclose($f);
	}

	private function loadIntoDb()
	{
		$users = new USVN_Db_Table_Users();
		$users->getAdapter()->beginTransaction();
		foreach (array_keys($this->_users_password) as $user) {
			$data['users_login'] = $user;
			$data['users_password'] = $this->_users_password[$user];
			$where = $users->getAdapter()->quoteInto('users_login = ?', $user);
			$user_row = $users->fetchRow($where);
			try  {
				if ($user_row === null) {
					$users->insert($data);
				}
				else {
					$users->update($data, $where);
				}
			}
			catch (Exception $e) {
				$users->getAdapter()->rollBack();
				throw new USVN_Exception(T_("Can't add users %s. Import cancel."), $user);
			}
		}
		$users->getAdapter()->commit();
	}

	/**
	 * Get list of imported users and password
	 *
	 * @return array()
	 */
	public function getUserPasswordList()
	{
		return $this->_users_password;
	}
}
