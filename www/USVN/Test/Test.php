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

abstract class USVN_Test_Test extends PHPUnit_Framework_TestCase {
    private $_path;

    protected function setUp() {
        error_reporting(E_ALL | E_STRICT);
        $this->_path = getcwd();
		USVN_Translation::initTranslation('en_US', 'www/locale');
		USVN_DirectoryUtils::removeDirectory('tests/');
		mkdir("tests");
		mkdir("tests/tmp");
		mkdir("tests/tmp/svn");
		$configArray = array('subversion' => array('path' => "tests/tmp"));
		$config = new Zend_Config($configArray);
		Zend_Registry::set('config', $config);
    }

    protected function tearDown() {
        chdir($this->_path);
    }
}

?>
