<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Storage_Folder_Maildir
 */
require_once 'Zend/Mail/Storage/Writable/Maildir.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MaildirWritableTest extends PHPUnit_Framework_TestCase
{
    protected $_params;
    protected $_originalDir;
    protected $_tmpdir;
    protected $_subdirs = array('.', '.subfolder', '.subfolder.test');

    public function setUp()
    {
        $this->_originalDir = dirname(__FILE__) . '/_files/test.maildir/';

        if (!is_dir($this->_originalDir . '/cur/')) {
            $this->markTestSkipped('You have to unpack maildir.tar in Zend/Mail/_files/test.maildir/ '
                                 . 'directory before enabling the maildir tests');
            return;
        }

        if ($this->_tmpdir == null) {
            if (TESTS_ZEND_MAIL_TEMPDIR != null) {
                $this->_tmpdir = TESTS_ZEND_MAIL_TEMPDIR;
            } else {
                $this->_tmpdir = dirname(__FILE__) . '/_files/test.tmp/';
            }
            if (!file_exists($this->_tmpdir)) {
                mkdir($this->_tmpdir);
            }
            $count = 0;
            $dh = opendir($this->_tmpdir);
            while (readdir($dh) !== false) {
                ++$count;
            }
            closedir($dh);
            if ($count != 2) {
                $this->markTestSkipped('Are you sure your tmp dir is a valid empty dir?');
                return;
            }
        }

        $this->_params = array();
        $this->_params['dirname'] = $this->_tmpdir;

        foreach ($this->_subdirs as $dir) {
            if ($dir != '.') {
                mkdir($this->_tmpdir . $dir);
            }
            foreach (array('cur', 'new') as $subdir) {
                if (!file_exists($this->_originalDir . $dir . '/' . $subdir)) {
                    continue;
                }
                mkdir($this->_tmpdir . $dir . '/' . $subdir);
                $dh = opendir($this->_originalDir . $dir . '/' . $subdir);
                while (($entry = readdir($dh)) !== false) {
                    $entry = $dir . '/' . $subdir . '/' . $entry;
                    if (!is_file($this->_originalDir . $entry)) {
                        continue;
                    }
                    copy($this->_originalDir . $entry, $this->_tmpdir . $entry);
                }
                closedir($dh);
            }
        }
    }

    public function tearDown()
    {
        foreach (array_reverse($this->_subdirs) as $dir) {
            if (!file_exists($this->_tmpdir . $dir)) {
                continue;
            }
            foreach (array('cur', 'new', 'tmp') as $subdir) {
                if (!file_exists($this->_tmpdir . $dir . '/' . $subdir)) {
                    continue;
                }
                $dh = opendir($this->_tmpdir . $dir . '/' . $subdir);
                while (($entry = readdir($dh)) !== false) {
                    $entry = $this->_tmpdir . $dir . '/' . $subdir . '/' . $entry;
                    if (!is_file($entry)) {
                        continue;
                    }
                    unlink($entry);
                }
                closedir($dh);
                rmdir($this->_tmpdir . $dir . '/' . $subdir);
            }
            if ($dir != '.') {
                rmdir($this->_tmpdir . $dir);
            }
        }
    }

    public function testCreateFolder()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $mail->createFolder('subfolder.test1');
        $mail->createFolder('test2', 'INBOX.subfolder');
        $mail->createFolder('test3', $mail->getFolders()->subfolder);
        $mail->createFolder('foo.bar');

        try {
            $mail->selectFolder($mail->getFolders()->subfolder->test1);
            $mail->selectFolder($mail->getFolders()->subfolder->test2);
            $mail->selectFolder($mail->getFolders()->subfolder->test3);
            $mail->selectFolder($mail->getFolders()->foo->bar);
        } catch (Exception $e) {
            $this->fail('could not get new folders');
        }

        // to tear down
        $this->_subdirs[] = '.subfolder.test1';
        $this->_subdirs[] = '.subfolder.test2';
        $this->_subdirs[] = '.subfolder.test3';
        $this->_subdirs[] = '.foo';
        $this->_subdirs[] = '.foo.bar';
    }

    public function testCreateFolderEmtpyPart()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        try {
            $mail->createFolder('foo..bar');
        } catch (Exception $e) {
            return; //ok
        }

        $this->fail('no exception while creating folder with empty part name');
    }

    public function testCreateFolderSlash()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        try {
            $mail->createFolder('foo/bar');
        } catch (Exception $e) {
            return; //ok
        }

        $this->fail('no exception while creating folder with slash');
    }

    public function testCreateFolderDirectorySeparator()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        try {
            $mail->createFolder('foo' . DIRECTORY_SEPARATOR . 'bar');
        } catch (Exception $e) {
            return; //ok
        }

        $this->fail('no exception while creating folder with DIRECTORY_SEPARATOR');
    }

    public function testCreateFolderExistingDir()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        unset($mail->getFolders()->subfolder->test);

        try {
            $mail->createFolder('subfolder.test');
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to create existing folder');
    }

    public function testCreateExistingFolder()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);

        try {
            $mail->createFolder('subfolder.test');
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to create existing folder');
    }

    public function testRemoveFolderName()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $mail->removeFolder('INBOX.subfolder.test');

        try {
            $mail->selectFolder($mail->getFolders()->subfolder->test);
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('folder still exists');
    }

    public function testRemoveFolderInstance()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $mail->removeFolder($mail->getFolders()->subfolder->test);

        try {
            $mail->selectFolder($mail->getFolders()->subfolder->test);
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('folder still exists');
    }

    public function testRemoveFolderWithChildren()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);

        try {
            $mail->removeFolder($mail->getFolders()->subfolder);
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to remove a folder with children');
    }

    public function testRemoveSelectedFolder()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $mail->selectFolder('subfolder.test');

        try {
            $mail->removeFolder('subfolder.test');
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('no error while removing selected folder');
    }

    public function testRemoveInvalidFolder()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);

        try {
            $mail->removeFolder('thisFolderDoestNotExist');
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('no error while removing invalid folder');
    }

    public function testRenameFolder()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        try {
            $mail->renameFolder('INBOX.subfolder', 'INBOX.foo');
            $mail->renameFolder($mail->getFolders()->foo, 'subfolder');
        } catch (Exception $e) {
            $this->fail('renaming failed');
        }

        try {
            $mail->renameFolder('INBOX', 'foo');
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('no error while renaming INBOX');
    }

    public function testRenameSelectedFolder()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $mail->selectFolder('subfolder.test');

        try {
            $mail->renameFolder('subfolder.test', 'foo');
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('no error while renaming selected folder');
    }

    public function testRenameToChild()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);

        try {
            $mail->renameFolder('subfolder.test', 'subfolder.test.foo');
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('no error while renaming folder to child of old');
    }

    public function testAppend()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $count = $mail->countMessages();

        $message = '';
        $message .= "From: me@example.org\r\n";
        $message .= "To: you@example.org\r\n";
        $message .= "Subject: append test\r\n";
        $message .= "\r\n";
        $message .= "This is a test\r\n";
        $mail->appendMessage($message);

        $this->assertEquals($count + 1, $mail->countMessages());
        $this->assertEquals($mail->getMessage($count + 1)->subject, 'append test');
    }

    public function testCopy()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);

        $mail->selectFolder('subfolder.test');
        $count = $mail->countMessages();
        $mail->selectFolder('INBOX');
        $message = $mail->getMessage(1);

        $mail->copyMessage(1, 'subfolder.test');
        $mail->selectFolder('subfolder.test');
        $this->assertEquals($count + 1, $mail->countMessages());
        $this->assertEquals($mail->getMessage($count + 1)->subject, $message->subject);
        $this->assertEquals($mail->getMessage($count + 1)->from, $message->from);
        $this->assertEquals($mail->getMessage($count + 1)->to, $message->to);

        try {
            $mail->copyMessage(1, 'justARandomFolder');
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('no error while copying to wrong folder');
    }

    public function testSetFlags()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);

        $mail->setFlags(1, array(Zend_Mail_Storage::FLAG_SEEN));
        $message = $mail->getMessage(1);
        $this->assertTrue($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN));
        $this->assertFalse($message->hasFlag(Zend_Mail_Storage::FLAG_FLAGGED));

        $mail->setFlags(1, array(Zend_Mail_Storage::FLAG_SEEN, Zend_Mail_Storage::FLAG_FLAGGED));
        $message = $mail->getMessage(1);
        $this->assertTrue($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN));
        $this->assertTrue($message->hasFlag(Zend_Mail_Storage::FLAG_FLAGGED));

        $mail->setFlags(1, array(Zend_Mail_Storage::FLAG_FLAGGED));
        $message = $mail->getMessage(1);
        $this->assertFalse($message->hasFlag(Zend_Mail_Storage::FLAG_SEEN));
        $this->assertTrue($message->hasFlag(Zend_Mail_Storage::FLAG_FLAGGED));

        try {
            $mail->setFlags(1, array(Zend_Mail_Storage::FLAG_RECENT));
        } catch (Exception $e) {
            return; // ok
        }
        $this->fail('should not be able to set recent flag');
    }

    public function testSetFlagsRemovedFile()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        unlink($this->_params['dirname'] . 'cur/1000000000.P1.example.org:2,S');

        try {
            $mail->setFlags(1, array(Zend_Mail_Storage::FLAG_FLAGGED));
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to set flags with removed file');
    }

    public function testRemove()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        $count = $mail->countMessages();

        $mail->removeMessage(1);
        $this->assertEquals($mail->countMessages(), --$count);

        unset($mail[2]);
        $this->assertEquals($mail->countMessages(), --$count);
    }

    public function testRemoveRemovedFile()
    {
        $mail = new Zend_Mail_Storage_Writable_Maildir($this->_params);
        unlink($this->_params['dirname'] . 'cur/1000000000.P1.example.org:2,S');

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('should not be able to remove message which is already removed in fs');
    }
}
