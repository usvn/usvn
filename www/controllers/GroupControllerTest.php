<?php
/**
 * Display group homepage.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.7
 * @package usvn
 * @subpackage group
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
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

class GroupControllerTest extends USVN_Test_Controller {
	protected $controller_name = "group";
	protected $controller_class = "GroupController";
	private $groups;

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
		$this->groups = new USVN_Db_Table_Groups();
		$g1 = $this->groups->insert(
			array(
				"groups_id" => 42,
				"groups_name" => "Telephone",
				"groups_description" => "test"
			)
		);
		$g2 = $this->groups->insert(
			array(
				"groups_id" => 43,
				"groups_name" => "Indochine",
				"groups_description" => "Bob morane"
			)
		);
	}

	public function test_DisplayGroupNotAdminNotGroupMember()
	{
		try {
			$this->request->setParam('group', 'Indochine');
			$this->runAction('index');
		}
		catch (USVN_Exception $e) {
			return;
		}
		$this->fail();
	}


	public function test_DisplayGroupAdmin()
	{
		$authAdapter = new USVN_Auth_Adapter_Db('god', 'ingodwetrust');
		Zend_Auth::getInstance()->authenticate($authAdapter);

		$this->request->setParam('group', 'Indochine');
		$this->runAction('index');
		$this->assertContains('Indochine', $this->getBody());
		$this->assertContains('Bob morane', $this->getBody());
	}

	public function test_DisplayGroupGroupMember()
	{
		$this->groups->find(43)->current()->addUser($this->user);
		$this->request->setParam('group', 'Indochine');
		$this->runAction('index');
		$this->assertContains('Indochine', $this->getBody());
		$this->assertContains('Bob morane', $this->getBody());
	}
}

// Call GroupControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "GroupControllerTest::main") {
    GroupControllerTest::main();
}
?>
