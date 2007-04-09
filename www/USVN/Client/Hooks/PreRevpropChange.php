<?php
/**
 * PreRevpropChange hook use by subversion hook
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
* PreRevpropChange hook use by subversion hook
*/
class USVN_Client_Hooks_PreRevpropChange extends USVN_Client_Hooks_Hook
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
	*
	* @return string or 0 (0 == Property change  OK)
	*/
    public function send()
    {
        return $this->xmlrpc->call('usvn.client.hooks.preRevpropChange', array($this->config->project, $this->config->auth,
																														$this->revision,
																														$this->user,
																														$this->property,
																														$this->action,
																														$this->value));
    }
}
