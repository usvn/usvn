<?php
/**
 * Base class for test database
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package test
 * @subpackage db
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id: DB.php 1536 2008-11-01 16:08:37Z duponc_j $
 */
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'library/USVN/autoload.php';
//require_once 'www/install/Install.php'; //Comment until fix tests

class USVN_Test_DB extends USVN_Test_Test {
	protected $db;

    protected function setUp() {
		parent::setUp();
		$params = array ('host'     => 'localhost',
			 				'username' => 'usvn-test',
			 				'password' => 'usvn-test',
			 				'dbname'   => 'usvn-test');
		if (getenv('DB') == "PDO_SQLITE" || getenv('DB') === false) {
			$this->_clean();
			Install::installDb('tests/db.ini', dirname(__FILE__) . '/../../SQL/', 'localhost', 'usvn-test', 'usvn-test', 'tests/usvn.db', 'usvn_', 'PDO_SQLITE', false);
			$params['dbname'] = "tests/usvn.db";
			$this->db = Zend_Db::factory('PDO_SQLITE', $params);
			file_put_contents('tests/test.ini', '
database.adapterName = "PDO_SQLITE"
database.prefix = "usvn_"
database.options.host = "localhost"
database.options.username = "usvn-test"
database.options.password = "usvn-test"
database.options.dbname = "' . getcwd() . '/tests/usvn.db"
subversion.passwd = "' . getcwd() . '/tests/htpasswd"
', FILE_APPEND);
		}
		else {
			$this->db = Zend_Db::factory(getenv('DB'), $params);
			$this->_clean();
			Install::installDb('tests/db.ini', dirname(__FILE__) . '/../../SQL/', 'localhost', 'usvn-test', 'usvn-test', 'usvn-test', 'usvn_', getenv('DB'), false);
			file_put_contents('tests/test.ini', '
database.adapterName = "' . getenv('DB') . '"
database.prefix = "usvn_"
database.options.host = "localhost"
database.options.username = "usvn-test"
database.options.password = "usvn-test"
database.options.dbname = "usvn-test"
subversion.passwd = "' . getcwd() . '/tests/htpasswd"
', FILE_APPEND);
		}
		Zend_Db_Table::setDefaultAdapter($this->db);
		USVN_Db_Table::$prefix = "usvn_";
		$config = new USVN_Config_Ini('tests/test.ini', 'general');
		Zend_Registry::set('config', $config);
    }

	protected function _clean()
	{
		if (getenv('DB') == "PDO_SQLITE" || getenv('DB') === false) {
			if (file_exists("tests/usvn.db")) {
				@unlink("tests/usvn.db");
			}
		}
		else {
			USVN_Db_Utils::deleteAllTables($this->db);
		}
	}

    protected function tearDown() {
        $this->_clean();
        $this->db->closeConnection();
        $this->db = null;
		parent::tearDown();
    }

    public function __destruct() {
        if ($this->db != null) {
            $this->db->closeConnection();
        }
    }

	/**
	 * Create and save a group
	 *
	 * @return USVN_Db_Table_Row_Group
	 */
    public function createGroup($name)
    {
		$table = new USVN_Db_Table_Groups();
		try {
			$group = $table->createRow(
				array(
					"groups_name"        => "$name",
					"groups_description" => "$name's description"
				)
			);
			$group->save();
			return $group;
		}
		catch (Exception $e) {
			$this->fail($name . " : " . $e->getMessage());
		}
    }

	/**
	 * Create and save a user
	 *
	 * @return USVN_Db_Table_Row_User
	 */
    protected function createUser($login, $password = "test")
    {
		$table = new USVN_Db_Table_Users();
		try {
			$user = $table->insert(
				array(
					"users_login"       => $login,
					"users_password"    => USVN_Crypt::crypt($password),
					'users_firstname' 	=> 'firstname',
					'users_lastname' 	=> 'lastname',
					'users_email' 		=> 'email@email.fr',
				)
			);
			$user = $table->find($user)->current();
			return $user;
		}
		catch (Exception $e) {
			$this->fail($login . " : " . $e->getMessage());
		}
    }


	/**
	 * Generate and save a project
	 *
	 * @param string $name
	 * @return USVN_Db_Table_Row_Project
	 */
	protected function createProject($name)
	{
		$table = new USVN_Db_Table_Projects();
		try {
			$obj = $table->fetchNew();
			$obj->setFromArray(array('projects_name' => $name));
			$obj->save();
			return $obj;
		}
		catch (Exception $e) {
			$this->fail($name . " : " . $e->getMessage());
		}
	}
}
