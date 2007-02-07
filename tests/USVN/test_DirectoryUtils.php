<?php
/**
* @package utils
* @subpackage directory
* @since 0.5
*/

require_once 'USVN/DirectoryUtils.php';

class TestDirectoryUtils extends PHPUnit2_Framework_TestCase
{
    public function test_removeDirectory()
    {
        mkdir('tests/tmp/dir');
        mkdir('tests/tmp/dir/1');
        mkdir('tests/tmp/dir/2');
        mkdir('tests/tmp/dir/2/3');
        file_put_contents('tests/tmp/dir/2/3/test', 'tutu');
        file_put_contents('tests/tmp/dir/test', 'tutu');
        USVN_DirectoryUtils::removeDirectory('tests/tmp/dir');
        $this->assertFalse(file_exists('tests/tmp/dir'));
    }
}