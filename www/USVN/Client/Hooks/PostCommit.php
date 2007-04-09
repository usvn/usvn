<?php
/**
 * PostCommit  hook use by subversion hook
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
* PostCommit  hook use by subversion hook
*/
class USVN_Client_Hooks_PostCommit extends USVN_Client_Hooks_Hook
{
    private $revision;

	/**
	* @param string
	* @param string
	*/
    public function __construct($repos_path, $revision)
    {
        parent::__construct($repos_path);
        $this->revision = intval($revision);
		$this->user = $this->svnLookRevision('author', $repos_path, $revision);
		$this->log = $this->svnLookRevision('log', $repos_path, $revision);
		$this->changed = USVN_Client_SVNUtils::changedFiles($this->svnLookRevision('changed', $repos_path, $revision));
    }

	/**
	* Contact USVN server
	*/
    public function send()
    {
        $this->xmlrpc->call('usvn.client.hooks.postCommit', array($this->config->project, $this->config->auth, $this->revision, $this->user, $this->log, $this->changed));
    }
}
