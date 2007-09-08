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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Spreadsheets_ListEntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->listEntry = new Zend_Gdata_Spreadsheets_ListEntry();
    }

    public function testToAndFromString()
    {
        $rowData = array();
        $rowData[] = new Zend_Gdata_Spreadsheets_Extension_Custom('column_1', 'value 1');
        $rowData[] = new Zend_Gdata_Spreadsheets_Extension_Custom('column_2', 'value 2');
        
        $this->listEntry->setCustom($rowData);
        $rowDataOut = $this->listEntry->getCustom();
        
        $this->assertTrue(count($rowDataOut) == 2);
        $this->assertTrue($rowDataOut[0]->getText() == 'value 1');
        $this->assertTrue($rowDataOut[0]->getColumnName() == 'column_1');
        $this->assertTrue($rowDataOut[1]->getText() == 'value 2');
        $this->assertTrue($rowDataOut[1]->getColumnName() == 'column_2');
        
        $newListEntry = new Zend_Gdata_Spreadsheets_ListEntry();
        $doc = new DOMDocument();
        $doc->loadXML($this->listEntry->saveXML());
        $newListEntry->transferFromDom($doc->documentElement);
        $rowDataFromXML = $newListEntry->getCustom();
        
        $this->assertTrue(count($rowDataFromXML) == 2);
        $this->assertTrue($rowDataFromXML[0]->getText() == 'value 1');
        $this->assertTrue($rowDataFromXML[0]->getColumnName() == 'column_1');
        $this->assertTrue($rowDataFromXML[1]->getText() == 'value 2');
        $this->assertTrue($rowDataFromXML[1]->getColumnName() == 'column_2');
        
 
    }

}
