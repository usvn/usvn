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
    define("PHPUnit_MAIN_METHOD", "SystemadminControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class SystemadminControllerTest extends USVN_Test_AdminController {
	protected $controller_name = "systemreportadmin";
	protected $controller_class = "SystemreportadminController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("SystemadminControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	public function test_index()
	{
		$this->runAction('index');
	}
}

// Call SystemreportControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "SystemadminControllerTest::main") {
    SystemadminControllerTest::main();
}
?>
