<?php
/**
 * Install hooks into a subversion repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.8
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
// Call USVN_InstallHooksTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
	define("PHPUnit_MAIN_METHOD", "USVN_InstallHooksTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

class USVN_InstallHooksTest extends USVN_Test_DB {

	public static function main() {
		require_once "PHPUnit/TextUI/TestRunner.php";

		$suite  = new PHPUnit_Framework_TestSuite("USVN_InstallHooksTest");
		$result = PHPUnit_TextUI_TestRunner::run($suite);
	}

	public function setUp()
	{
		parent::setUp();
		USVN_SVNUtils::createsvndirectoryStruct('tests/mysvn');
	}

	public function test_hooksInstallation()
	{
		$install = new USVN_InstallHooks('tests/mysvn');
		$this->assertTrue(file_exists("tests/mysvn/hooks/post-commit"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/post-lock"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/post-revprop-change"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/post-unlock"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/pre-commit"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/pre-lock"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/pre-revprop-change"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/pre-unlock"));
		$this->assertTrue(file_exists("tests/mysvn/hooks/start-commit"));
		$this->assertTrue(is_executable("tests/mysvn/hooks/start-commit"));
		$this->assertEquals(
			"#!/bin/sh\n" .
			'cd ' . getcwd() . DIRECTORY_SEPARATOR . "www\n" .
			'php hooks/start-commit.php "$@"' . "\n" .
			'exit $?' . "\n"
			, file_get_contents("tests/mysvn/hooks/start-commit"));
	}
}

// Call USVN_InstallHooksTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "USVN_InstallHooksTest::main") {
	USVN_InstallHooksTest::main();
}
