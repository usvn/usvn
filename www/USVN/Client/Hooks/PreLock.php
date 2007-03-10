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
class USVN_Client_Hooks_PreLock extends USVN_Client_Hooks_Hook
{
    private $path;
	private $user;

	/**
	* @param string repository path
	* @param string file to lock path
	* @param string user
	*/
    public function __construct($repos_path, $path, $user)
    {
        parent::__construct($repos_path);
        $this->path = $path;
		$this->user = $user;
    }

	/**
	* Contact USVN server
	*
	* @return string or 0 (0 == lock OK)
	*/
    public function send()
    {
        return $this->xmlrpc->call('usvn.client.hooks.preLock', array($this->config->auth, $this->path, $this->user));
    }
}