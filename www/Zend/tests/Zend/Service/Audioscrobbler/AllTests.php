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
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 4892 2007-05-22 20:01:40Z darby $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Audioscrobbler_AllTests::main');
}


/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';

/**
 * PHPUnit_TextUI_TestRunner
 */
require_once 'PHPUnit/TextUI/TestRunner.php';

/**
 * @see Zend_Service_Audioscrobbler_ProfileTest
 */
require_once 'Zend/Service/Audioscrobbler/ProfileTest.php';

/**
 * @see Zend_Service_Audioscrobbler_ArtistTest
 */
require_once 'Zend/Service/Audioscrobbler/ArtistTest.php';

/**
 * @see Zend_Service_Audioscrobbler_AlbumDataTest
 */
require_once 'Zend/Service/Audioscrobbler/AlbumDataTest.php';

/**
 * @see Zend_Service_Audioscrobbler_TrackDataTest
 */
require_once 'Zend/Service/Audioscrobbler/TrackDataTest.php';

/**
 * @see Zend_Service_Audioscrobbler_TagDataTest
 */
require_once 'Zend/Service/Audioscrobbler/TagDataTest.php';

/**
 * @see Zend_Service_Audioscrobbler_GroupTest
 */
require_once 'Zend/Service/Audioscrobbler/GroupTest.php';


/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Audioscrobbler_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Audioscrobbler');

        $suite->addTestSuite('Zend_Service_Audioscrobbler_ProfileTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_ArtistTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_AlbumDataTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_TrackDataTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_TagDataTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_GroupTest');

        return $suite;
    }
}

