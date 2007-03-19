<?php
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class USVN_Test_DB extends PHPUnit_Framework_TestCase {
	protected $db;

    public function setUp() {
		$params = array ('host'     => 'localhost',
                 'username' => 'usvn-test',
                 'password' => 'usvn-test',
                 'dbname'   => 'usvn-test');

		$this->db = Zend_Db::factory('PDO_MYSQL', $params);
		Zend_Db_Table::setDefaultAdapter($this->db);
		USVN_Db_Table::$prefix = "usvn_";
		USVN_Db_Utils::deleteAllTables($this->db);
		USVN_Db_Utils::loadFile($this->db, "SQL/SVNDB.sql");
    }

    public function tearDown() {
		USVN_Db_Utils::deleteAllTables($this->db);
    }
}

?>
