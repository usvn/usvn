<?php
/**
 * Display project homepage.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 * @subpackage project
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
    define("PHPUnit_MAIN_METHOD", "ProjectControllerTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class ProjectControllerTest extends USVN_Test_Controller {
	protected $controller_name = "project";
	protected $controller_class = "ProjectController";

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("ProjectControllerTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function setUp()
    {
    	parent::setUp();
    	$config = Zend_Registry::get('config');
    	$config->subversion->url = "http://localhost/test/svn/";
    	$config->save();
    }
    
	public function test_index()
	{		
		$project = USVN_Project::createProject(array('projects_name' => 'normal',  'projects_start_date' => '1984-12-03 00:00:00'), "john", true, true, true, true);
		$this->request->setParam('project', 'normal');
		$this->runAction('index');
		$this->assertContains("http://localhost/test/svn/normal/trunk", $this->getBody());
	}

	public function test_indexEmptyProject()
	{		
		$project = USVN_Project::createProject(array('projects_name' => 'empty',  'projects_start_date' => '1984-12-03 00:00:00'), "john", true, true, true, false);
		$this->request->setParam('project', 'empty');
		$this->runAction('index');
		$this->assertContains("http://localhost/test/svn/empty", $this->getBody());
		$this->assertNotContains("http://localhost/test/svn/empty/trunk", $this->getBody());
	}
	
}

// Call ProjectControllerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "ProjectControllerTest::main") {
    ProjectControllerTest::main();
}
?>
