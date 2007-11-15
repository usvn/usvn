<?php
/**
 * Base class for test USVN
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package test
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

define('USVN_URL_SEP', ':');

abstract class USVN_Test_Test extends PHPUnit_Framework_TestCase {
    private $_path;

    protected function setUp() {
        error_reporting(E_ALL | E_STRICT);
        date_default_timezone_set('UTC');
        $this->_path = getcwd();
		$this->setConsoleLocale();
		USVN_Translation::initTranslation('en_US', 'www/locale');
		USVN_DirectoryUtils::removeDirectory('tests/');
		mkdir("tests");
		mkdir("tests/tmp");
		mkdir("tests/tmp/svn");
		file_put_contents('tests/test.ini', '[general]
subversion.path = "tests/tmp"
subversion.passwd = "tests/tmp/htpasswd"
subversion.authz = "tests/tmp/authz"
version = "0.8.4"
translation.locale = "en_US"
');
		$config = new USVN_Config_Ini('tests/test.ini', 'general');
		Zend_Registry::set('config', $config);
    }

    protected function tearDown() {
        chdir($this->_path);
    }

	private function setConsoleLocale()
	{
		if (PHP_OS == "Linux") {
			USVN_ConsoleUtils::setLocale("en_US.utf8");
		}
		else {
			USVN_ConsoleUtils::setLocale("en_US.UTF-8");
		}
	}
}

?>
