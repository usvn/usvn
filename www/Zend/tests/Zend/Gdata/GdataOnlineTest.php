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

require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/App/InvalidArgumentException.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_GdataOnlineTest extends PHPUnit_Framework_TestCase
{
    private $blog = null; // blog ID from config

    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $this->blog = constant('TESTS_ZEND_GDATA_BLOG_ID');
        $service = 'blogger';
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata($client);
    }

    public function testPostAndDeleteByEntry()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle('PHP test blog post');
        $entry->content = $this->gdata->newContent('Blog post content...');
        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $this->assertEquals('PHP test blog post', $insertedEntry->title->text);
        $this->assertEquals('Blog post content...',
                $insertedEntry->content->text);
        $this->assertTrue( 
                strpos($insertedEntry->getEditLink()->href, 'http') === 0);
        $this->gdata->delete($insertedEntry);
    }

    public function testPostAndDeleteByUrl()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle('PHP test blog post');
        $entry->content = $this->gdata->newContent('Blog post content...');
        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $this->assertTrue( 
                strpos($insertedEntry->getEditLink()->href, 'http') === 0);
        $this->gdata->delete($insertedEntry->getEditLink()->href);
    }

    public function testPostRetrieveEntryAndDelete()
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle(' PHP test blog post ');
        $this->assertTrue(isset($entry->title));
        $entry->content = $this->gdata->newContent('Blog post content...');

        /* testing getText and __toString */
        $this->assertEquals("PHP test blog post",
                $entry->title->getText());
        $this->assertEquals(" PHP test blog post ",
                $entry->title->getText(false)); 
        $this->assertEquals($entry->title->getText(),
            $entry->title->__toString());

        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $retrievedEntryQuery = $this->gdata->newQuery(
                $insertedEntry->getSelfLink()->href);
        $retrievedEntry = $this->gdata->getEntry($retrievedEntryQuery);
        $this->assertTrue( 
                strpos($retrievedEntry->getEditLink()->href, 'http') === 0);
        $this->gdata->delete($retrievedEntry);
    }

    public function testPostUpdateAndDeleteEntry() 
    {
        $postUrl = 'http://www.blogger.com/feeds/' . $this->blog .
                '/posts/default';
        $entry = $this->gdata->newEntry();
        $entry->title = $this->gdata->newTitle('PHP test blog post');
        $entry->content = $this->gdata->newContent('Blog post content...');
        $insertedEntry = $this->gdata->insertEntry($entry, $postUrl);
        $this->assertTrue( 
                strpos($insertedEntry->getEditLink()->href, 'http') === 0);
        $insertedEntry->title->text = 'PHP test blog post modified';
        $updatedEntry = $this->gdata->updateEntry($insertedEntry);
        $this->assertEquals('PHP test blog post modified',
                $updatedEntry->title->text);
        $updatedEntry->title->text = 'PHP test blog post modified twice';
        // entry->saveXML() and entry->getXML() should be the same
        $this->assertEquals($updatedEntry->saveXML(), 
                $updatedEntry->getXML());
        $newlyUpdatedEntry = $this->gdata->updateEntry($updatedEntry);
        $this->assertEquals('PHP test blog post modified twice',
                $updatedEntry->title->text);
        $updatedEntry->delete();
    }

    public function testFeedImplementation()
    {
        $blogsUrl = 'http://www.blogger.com/feeds/default/blogs';
        $blogsQuery = $this->gdata->newQuery($blogsUrl);
        $retrievedFeed = $this->gdata->getFeed($blogsQuery);

        // Make sure the iterator and array impls match
        $entry1 = $retrievedFeed->current();
        $entry2 = $retrievedFeed[0];
        $this->assertEquals($entry1, $entry2);

        /*
        TODO: Fix these tests
        // Test ArrayAccess interface
        $firstBlogTitle = $retrievedFeed[0]->title->text;
        $entries = $retrievedFeed->entry;
        $entries[0]->title->text = $firstBlogTitle . "**";
        $retrievedFeed[0] = $entries[0];
        $this->assertEquals($retrievedFeed->entry[0]->title->text,
                $retrievedFeed[0]->title->text);
        $this->assertEquals($firstBlogTitle . "**",
                $retrievedFeed[0]->title->text);
        */
    }

    public function testBadFeedRetrieval()
    {
        $feed = $this->gdata->newFeed();
        try {
            $returnedFeed = $this->gdata->getFeed($feed);
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            // we're expecting to cause an exception here
        }
    }

    public function testBadEntryRetrieval()
    {
        $entry = $this->gdata->newEntry();
        try {
            $returnedEntry = $this->gdata->getEntry($entry);
        } catch (Zend_Gdata_App_InvalidArgumentException $e) {
            // we're expecting to cause an exception here
        }
    }

}
