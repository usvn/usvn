<?php
/**
 * Group management controller's.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package admin
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This group has been realised as part of
 * end of studies group.
 *
 * $Id$
 */

// Call GroupControllerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "GroupControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class GroupControllerTest extends USVN_Test_AdminController {
	protected $controller_name = "groupadmin";
	protected $controller_class = "GroupadminController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("GroupControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

	protected function setUp()
	{
		parent::setUp();
		$groups = new USVN_Db_Table_Groups();
		$g1 = $groups->insert(
			array(
				"groups_id" => 42,
				"groups_name" => "Telephone",
				"groups_description" => "test"
			)
		);
		$g2 = $groups->insert(
			array(
				"groups_id" => 43,
				"groups_name" => "Indochine",
				"groups_description" => "Bob morane"
			)
		);
	}

	public function test_index()
	{
		$this->runAction('index');
		$this->assertEquals(2, count($this->controller->view->groups));
		$this->assertContains('Indochine', $this->getBody());
		$this->assertContains('Bob morane', $this->getBody());
	}

	public function test_delete()
	{
		$groups = new USVN_Db_Table_Groups();
		$this->assertNotNull($groups->fetchRow(array('groups_name = ?' => 'Indochine')));
		$this->request->setParam('name', 'Indochine');
		try {
			$this->runAction('delete');
		}
		catch (USVN_Test_Exception_Redirect $e) {
			$this->assertEquals('/admin/group/', $e->url);
		}
		$this->assertNull($groups->fetchRow(array('groups_name = ?' => 'Indochine')));
	}
}

// Call GroupControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "GroupControllerTest::main") {
    GroupControllerTest::main();
}
?>
