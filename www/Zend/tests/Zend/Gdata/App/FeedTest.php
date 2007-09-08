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
 * @feed     Zend
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license      http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/App/Feed.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_FeedTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->feedText = file_get_contents(
                'Zend/Gdata/App/_files/FeedSample1.xml',
                true);
        $this->feed = new Zend_Gdata_App_Feed();
    }
      
    public function testEmptyFeedShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->feed->extensionElements));
        $this->assertTrue(count($this->feed->extensionElements) == 0);
    }
      
    public function testEmptyFeedToAndFromStringShouldMatch() {
        $feedXml = $this->feed->saveXML();
        $newFeed = new Zend_Gdata_App_Feed();
        $newFeed->transferFromXML($feedXml);
        $newFeedXml = $newFeed->saveXML();
        $this->assertTrue($feedXml == $newFeedXml);
    }

    public function testConvertFeedToAndFromString() {
        $this->feed->transferFromXML($this->feedText);
        $feedXml = $this->feed->saveXML();
        $newFeed = new Zend_Gdata_App_Feed();
        $newFeed->transferFromXML($feedXml);
        $this->assertEquals(1, count($newFeed->entry));
        $this->assertEquals('dive into mark', $newFeed->title->text);
        $this->assertEquals('text', $newFeed->title->type);
        $this->assertEquals('2005-07-31T12:29:29Z', $newFeed->updated->text);
        $this->assertEquals('tag:example.org,2003:3', $newFeed->id->text);
        $this->assertEquals(2, count($newFeed->link));
        $this->assertEquals('http://example.org/', 
                $newFeed->getAlternateLink()->href); 
        $this->assertEquals('en', 
                $newFeed->getAlternateLink()->hrefLang); 
        $this->assertEquals('text/html', 
                $newFeed->getAlternateLink()->type); 
        $this->assertEquals('http://example.org/feed.atom', 
                $newFeed->getSelfLink()->href); 
        $this->assertEquals('application/atom+xml', 
                $newFeed->getSelfLink()->type); 
        $this->assertEquals('Copyright (c) 2003, Mark Pilgrim', 
                $newFeed->rights->text); 
        $entry = $newFeed->entry[0];
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
    }
    
    public function testCanAddIndividualEntries() {
        $this->feed->transferFromXML($this->feedText);
        $this->assertEquals(1, count($this->feed->entry));
        $oldTitle = $this->feed->entry[0]->title->text;
        $newEntry = new Zend_Gdata_App_Entry();
        $newEntry->setTitle(new Zend_Gdata_App_Extension_Title("Foo"));
        $this->feed->addEntry($newEntry);
        $this->assertEquals(2, count($this->feed->entry));
        $this->assertEquals($oldTitle, $this->feed->entry[0]->title->text);
        $this->assertEquals("Foo", $this->feed->entry[1]->title->text);
    }

}
