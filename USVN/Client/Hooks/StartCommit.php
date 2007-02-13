<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/Hook.php';

class USVN_Client_Hooks_StartCommit extends USVN_Client_Hooks_Hook
{
    private $user;

    public function USVN_Client_Hooks_StartCommit($repos_path, $user)
    {
        parent::USVN_Client_Hooks_Hook($repos_path);
        $this->user = $user;
    }

    public function send()
    {
        return $this->xmlrpc->call('usvn.client.hooks.startCommit', array($this->repos_path, $this->user));
    }
}