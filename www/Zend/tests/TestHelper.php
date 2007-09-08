<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TestHelper.php 4528 2007-04-17 23:10:47Z darby $
 */

// Set error reporting to the level to which Zend Framework code must comply
error_reporting( E_ALL | E_STRICT );

// Determine the root, library, and tests directories of the framework distribution
$zfRoot    = dirname(dirname(__FILE__));
$zfLibrary = $zfRoot . DIRECTORY_SEPARATOR . 'library';
$zfTests   = $zfRoot . DIRECTORY_SEPARATOR . 'tests';

/*
Prepend the Zend Framework library/ and tests/ directories to the include_path. This allows the tests to run out of
the box and helps prevent loading other copies of the framework code and tests that would supersede this copy.
*/
set_include_path($zfLibrary . PATH_SEPARATOR
               . $zfTests   . PATH_SEPARATOR
               . get_include_path());

// Load the user-defined test configuration file, if it exists; otherwise, load the default configuration
if (is_readable($zfTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once 'TestConfiguration.php';
} else {
    require_once 'TestConfiguration.php.dist';
}

// Unset global variables no longer needed
unset($zfRoot, $zfLibrary, $zfTests);
