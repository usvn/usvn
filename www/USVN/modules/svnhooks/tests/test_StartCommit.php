<?php
/**
* @package svnhooks
* @since 0.5
*/

require_once 'www/USVN/modules/svnhooks/models/Hooks.php';

class TestStartCommit extends PHPUnit2_Framework_TestCase
{
    public function test_startCommit()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->startCommit("test", "test");
    }
}