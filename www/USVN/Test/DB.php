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
 * $Id$
 */
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';
require_once 'www/install/Install.php';

class USVN_Test_DB extends USVN_Test_Test {
	protected $db;

    protected function setUp() {
		parent::setUp();
		$params = array ('host'     => 'localhost',
                 'username' => 'usvn-test',
                 'password' => 'usvn-test',
                 'dbname'   => 'usvn-test');

		$this->db = Zend_Db::factory('PDO_MYSQL', $params);
		Zend_Db_Table::setDefaultAdapter($this->db);
		USVN_Db_Table::$prefix = "usvn_";
		USVN_Db_Utils::deleteAllTables($this->db);
		Install::installDb('tests/db.ini', dirname(__FILE__) . '/../../SQL/', 'localhost', 'usvn-test', 'usvn-test', 'usvn-test', 'usvn_');
    }

    protected function tearDown() {
		USVN_Db_Utils::deleteAllTables($this->db);
		parent::tearDown();
    }
}

?>
