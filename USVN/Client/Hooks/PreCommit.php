<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'USVN/Client/Hooks/Hook.php';

/**
* precommit  hook use by subversion hook
*/
class USVN_Client_Hooks_PreCommit extends USVN_Client_Hooks_Hook
{
    private $txn;
	private $user;
	private $log;
	private $changed;

	/**
	* @param string repository path
	* @param string transaction
	*/
    public function USVN_Client_Hooks_PreCommit($repos_path, $txn)
    {
        parent::USVN_Client_Hooks_Hook($repos_path);
        $this->txn = $txn;
		$this->user = $this->svnLookTransaction('author', $repos_path, $txn);
		$this->log = $this->svnLookTransaction('log', $repos_path, $txn);
		$this->changed = USVN_Client_SVNUtils::changedFiles($this->svnLookTransaction('changed', $repos_path, $txn));
    }

	/**
	* Contact USVN server
	*
	* @return string or 0 (0 == commit OK)
	*/
    public function send()
    {
        return $this->xmlrpc->call('usvn.client.hooks.preCommit', array($this->config->auth, $this->user, $this->log, $this->changed));
    }
}