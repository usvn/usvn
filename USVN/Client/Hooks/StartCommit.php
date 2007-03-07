<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/Hook.php';

/**
* Start commit  hook use by subversion hook
*/
class USVN_Client_Hooks_StartCommit extends USVN_Client_Hooks_Hook
{
    private $user;

	/**
	* @param string
	* @param string
	*/
    public function __construct($repos_path, $user)
    {
        parent::__construct($repos_path);
        $this->user = $user;
    }

	/**
	* Contact USVN server
	*
	* @return string or 0 (0 == commit OK)
	*/
    public function send()
    {
        return $this->xmlrpc->call('usvn.client.hooks.startCommit', array($this->config->auth, $this->user));
    }
}