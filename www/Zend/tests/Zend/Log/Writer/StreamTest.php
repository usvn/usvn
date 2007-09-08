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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: StreamTest.php 4560 2007-04-20 08:01:32Z mike $
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log_Writer_Mock */
require_once 'Zend/Log/Writer/Stream.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: StreamTest.php 4560 2007-04-20 08:01:32Z mike $
 */
class Zend_Log_Writer_StreamTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorThrowsWhenResourceIsNotStream()
    {
        $resource = xml_parser_create();
        try {
            new Zend_Log_Writer_Stream($resource);
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/not a stream/i', $e->getMessage());
        }
        xml_parser_free($resource);
    }

    public function testConstructorWithValidStream()
    {
        $stream = fopen('php://memory', 'a');
        new Zend_Log_Writer_Stream($stream);
    }
    
    public function testConstructorWithValidUrl()
    {
        new Zend_Log_Writer_Stream('php://memory');
    }

    public function testConstructorThrowsWhenModeSpecifiedForExistingStream()
    {
        $stream = fopen('php://memory', 'a');
        try {
            new Zend_Log_Writer_Stream($stream, 'w');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/existing stream/i', $e->getMessage());
        }
    }
    
    public function testConstructorThrowsWhenStreamCannotBeOpened()
    {
        try {
            new Zend_Log_Writer_Stream('');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/cannot be opened/i', $e->getMessage());
        }
    }
    
    public function testWrite()
    {
        $stream = fopen('php://memory', 'a');
        $fields = array('message' => 'message-to-log');

        $writer = new Zend_Log_Writer_Stream($stream);
        $writer->write($fields);

        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertContains($fields['message'], $contents);
    }
    
    public function testWriteThrowsWhenStreamWriteFails()
    {
        $stream = fopen('php://memory', 'a');
        $writer = new Zend_Log_Writer_Stream($stream);
        fclose($stream);
        
        try {
            $writer->write(array('message' => 'foo'));
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/unable to write/i', $e->getMessage());
        }
    }

    public function testShutdownClosesStreamResource()
    {
        $writer = new Zend_Log_Writer_Stream('php://memory', 'a');
        $writer->write(array('message' => 'this write should succeed'));
        
        $writer->shutdown();
        
        try {
            $writer->write(array('message' => 'this write should fail'));
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/unable to write/i', $e->getMessage());
        }
    }

    public function testSettingNewFormatter()
    {
        $stream = fopen('php://memory', 'a');
        $writer = new Zend_Log_Writer_Stream($stream);
        $expected = 'foo';
        
        $formatter = new Zend_Log_Formatter_Simple($expected);
        $writer->setFormatter($formatter);

        $writer->write(array('bar'=>'baz'));
        rewind($stream);
        $contents = stream_get_contents($stream);
        fclose($stream);

        $this->assertContains($expected, $contents);        
    }
}
