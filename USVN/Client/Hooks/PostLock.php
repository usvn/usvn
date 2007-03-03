<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/Hook.php';

/**
* prelock hook use by subversion hook
*/
class USVN_Client_Hooks_PostLock extends USVN_Client_Hooks_Hook
{
    private $path;
	private $user;

	/**
	* @param string repository path
	* @param string file to lock path
	* @param string user
	*/
    public function USVN_Client_Hooks_PostLock($repos_path, $path, $user)
    {
        parent::USVN_Client_Hooks_Hook($repos_path);
        $this->path = $path;
		$this->user = $user;
    }

	/**
	* Contact USVN server
	*/
    public function send()
    {
        $this->xmlrpc->call('usvn.client.hooks.postLock', array($this->config->auth, $this->path, $this->user));
    }
}