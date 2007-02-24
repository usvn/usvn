<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'Zend/XmlRpc/Client.php';
require_once 'USVN/Client/Config.php';
require_once 'USVN/Client/SVNUtils.php';

class USVN_Client_Hooks_Hook
{
    protected $xmlrpc;
    protected $config;
    protected $repos_path;

    /**
    * @var repos_path Path of the svn repository
    */
    public function USVN_Client_Hooks_Hook($repos_path)
    {
        $this->repos_path = $repos_path.'/';
        $this->config = new USVN_Client_Config($repos_path);
        $this->xmlrpc = new Zend_XmlRpc_Client($this->config->url);
    }

    /**
    * Don't use this! It's only for change the HTTP Client use
    * by XMLRPC for test unit.
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
}