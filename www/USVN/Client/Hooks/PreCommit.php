<?php
/**
 * Precommit  hook use by subversion hook
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage hook
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once 'USVN/Client/Hooks/Hook.php';

/**
* Precommit  hook use by subversion hook
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
    public function __construct($repos_path, $txn)
    {
        parent::__construct($repos_path);
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
