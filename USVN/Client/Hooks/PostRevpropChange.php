<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/Hook.php';

/**
* PostRevpropChange hook use by subversion hook
*/
class USVN_Client_Hooks_PostRevpropChange extends USVN_Client_Hooks_Hook
{
    private $revision;
	private $user;
	private $property;
	private $action;
	private $value;

	/**
	* @param string repository path
	* @param integer Revision
	* @param string The user login
	* @param string Property will be change (ex: svn:log)
	* @param string Action (ex: M)
	* @param string New property value
	*/
    public function __construct($repos_path, $revision, $user, $property, $action, $value)
    {
        parent::__construct($repos_path);
        $this->revision = intval($revision);
		$this->user = $user;
		$this->property = $property;
		$this->action = $action;
		$this->value = $value;
    }

	/**
	* Contact USVN server
	*/
    public function send()
    {
        $this->xmlrpc->call('usvn.client.hooks.postRevpropChange', array($this->config->auth,
																														$this->revision,
																														$this->user,
																														$this->property,
																														$this->action,
																														$this->value));
    }
}