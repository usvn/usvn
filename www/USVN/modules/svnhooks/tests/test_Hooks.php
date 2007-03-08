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
		$hook->startCommit("007", "test");
    }

    public function test_preCommit()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->preCommit("007", "test", "Ceci est un commit", array(array('M', 'tata'), array('A', 'tutu')));
    }

    public function test_postCommit()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->postCommit("007", 1, "test", "Ceci est un commit", array(array('M', 'tata'), array('A', 'tutu')));
    }

    public function test_preLock()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->preLock("007", "tutu", "test");
    }

    public function test_postLock()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->postLock("007", "tutu", "test");
    }

    public function test_preUnlock()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->preUnlock("007", "tutu", "test");
    }

    public function test_postUnlock()
    {
		$hook = new USVN_modules_svnhooks_Hooks();
		$hook->postUnlock("007", "tutu", "test");
    }
}