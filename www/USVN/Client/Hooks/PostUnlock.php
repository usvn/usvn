<?php
/**
 * Post unlock hook use by subversion hook
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
* Post unlock hook use by subversion hook
*/
class USVN_Client_Hooks_PostUnlock extends USVN_Client_Hooks_Hook
{
    private $path;
	private $user;

	/**
	* @param string repository path
	* @param string file to Unlock path
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
	*/
    public function send()
    {
        $this->xmlrpc->call('usvn.client.hooks.postUnlock', array($this->config->project, $this->config->auth, $this->path, $this->user));
    }
}
