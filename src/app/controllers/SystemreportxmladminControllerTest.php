<?php
/**
 * Controller for system report admin page
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package admin
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

// Call SystemreportControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "SystemreportxmladminControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'library/USVN/autoload.php';

class SystemreportxmladminControllerTest extends USVN_Test_AdminController {
	protected $controller_name = "systemreportxmladmin";
	protected $controller_class = "SystemreportxmladminController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("SystemreportxmladminControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
	protected function setUp()
    {
    	parent::setUp();
		$config = Zend_Registry::get('config');
		$config->database = array("adapterName" => "mysql");
    }
    
    
	public function test_index()
	{
		$this->runAction('index');
		$this->assertContains('<informations>', $this->getBody());
	}
}

// Call SystemreportControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "SystemadminreportxmlControllerTest::main") {
    SystemadminControllerTest::main();
}
?>
