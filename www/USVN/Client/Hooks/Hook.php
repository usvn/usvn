<?php
/**
 * Base class for client hooks
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

require_once 'Zend/XmlRpc/Client.php';
require_once 'USVN/Client/Config.php';
require_once 'USVN/Client/SVNUtils.php';

/**
 * Base class for client hooks
 */
abstract class USVN_Client_Hooks_Hook
{
    protected $xmlrpc;
    protected $config;
    protected $repos_path;

    /**
    * @var repos_path Path of the svn repository
    */
    public function __construct($repos_path)
    {
        $this->repos_path = $repos_path.'/';
        $this->config = new USVN_Client_Config($repos_path);
		$url = str_replace("//project", "/project", $this->config->url . "/project/" . $this->config->project . "/svnhooks/");
        $this->xmlrpc = new Zend_XmlRpc_Client($url);
    }

    /**
    * Don't use this! It's only for change the HTTP Client use
    * by XMLRPC for test unit.
	* @param Zend_Http_Client
    */
    public function setHttpClient($httpclient)
    {
        $this->xmlrpc->setHttpClient($httpclient);
    }

    /**
    * Don't use this! It's only for test unit.
    */
    public function getLastRequest()
    {
        return $this->xmlrpc->getLastRequest();
    }

	/**
	* Call the svnlook binary on an svn transaction.
	*
	* @param string svnlook command (see svnlook help)
	* @param string repository path
	* @param string transaction (call TXN into svn hooks samples)
	* @return string Output of svnlook
	* @see http://svnbook.red-bean.com/en/1.1/ch09s03.html
	*/
	protected function svnLookTransaction($command, $repository, $transaction)
	{
		return USVN_Client_SVNUtils::svnLookTransaction($command, $repository, $transaction);
	}

	/**
	* Call the svnlook binary on an svn revision.
	*
	* @param string svnlook command (see svnlook help)
	* @param string SVN repository path
	* @param integer SVN revision
	* @return string Output of svnlook
	* @see http://svnbook.red-bean.com/en/1.1/ch09s03.html
	*/
	protected function svnLookRevision($command, $repository, $transaction)
	{
		return USVN_Client_SVNUtils::svnLookRevision($command, $repository, $transaction);
	}
}
