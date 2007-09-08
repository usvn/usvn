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
 * @package      Zend_Gdata_App
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license      http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/App/Entry.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_EntryTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->enryText = file_get_contents(
                'Zend/Gdata/App/_files/EntrySample1.xml',
                true);
        $this->enry = new Zend_Gdata_App_Entry();
    }
      
    public function testEmptyEntryShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->enry->extensionElements));
        $this->assertTrue(count($this->enry->extensionElements) == 0);
    }
      
    public function testEmptyEntryToAndFromStringShouldMatch() {
        $enryXml = $this->enry->saveXML();
        $newEntry = new Zend_Gdata_App_Entry();
        $newEntry->transferFromXML($enryXml);
        $newEntryXml = $newEntry->saveXML();
        $this->assertTrue($enryXml == $newEntryXml);
    }

    public function testConvertEntryToAndFromString() {
        $this->enry->transferFromXML($this->enryText);
        $enryXml = $this->enry->saveXML();
        $newEntry = new Zend_Gdata_App_Entry();
        $newEntry->transferFromXML($enryXml);
/*
        $this->assertEquals(1, count($newEntry->entry));
        $this->assertEquals('dive into mark', $newEntry->title->text);
        $this->assertEquals('text', $newEntry->title->type);
        $this->assertEquals('2005-07-31T12:29:29Z', $newEntry->updated->text);
        $this->assertEquals('tag:example.org,2003:3', $newEntry->id->text);
        $this->assertEquals(2, count($newEntry->link));
        $this->assertEquals('http://example.org/', 
                $newEntry->getAlternateLink()->href); 
        $this->assertEquals('en', 
                $newEntry->getAlternateLink()->hrefLang); 
        $this->assertEquals('text/html', 
                $newEntry->getAlternateLink()->type); 
        $this->assertEquals('http://example.org/enry.atom', 
                $newEntry->getSelfLink()->href); 
        $this->assertEquals('application/atom+xml', 
                $newEntry->getSelfLink()->type); 
        $this->assertEquals('Copyright (c) 2003, Mark Pilgrim', 
                $newEntry->rights->text); 
        $entry = $newEntry->entry[0];
        $this->assertEquals('Atom draft-07 snapshot', $entry->title->text);
        $this->assertEquals('tag:example.org,2003:3.2397', 
                $entry->id->text);
        $this->assertEquals('2005-07-31T12:29:29Z', $entry->updated->text);
        $this->assertEquals('2003-12-13T08:29:29-04:00', 
                $entry->published->text);
        $this->assertEquals('Mark Pilgrim', 
                $entry->author[0]->name->text);
        $this->assertEquals('http://example.org/', 
                $entry->author[0]->uri->text);
        $this->assertEquals(2, count($entry->contributor)); 
        $this->assertEquals('Sam Ruby', 
                $entry->contributor[0]->name->text); 
        $this->assertEquals('Joe Gregorio', 
                $entry->contributor[1]->name->text); 
        $this->assertEquals('xhtml', $entry->content->type);
*/
    }


}
