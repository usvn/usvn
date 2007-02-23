<?php
/**
* @package svnhooks
* @since 0.5
*/

require_once 'www/USVN/modules/svnhooks/models/Hooks.php';

class Test_SvnHooks_Hooks extends PHPUnit2_Framework_TestCase
{
    public function test_startCommit()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->startCommit("test", "test");
    }
}