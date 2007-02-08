<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'Zend/XmlRpc/Client.php';
require_once 'USVN/Client/Config.php';

class USVN_Client_Hook_Hook
{
    protected $xmlrpc;
    protected $config;
    protected $repos_path;

    /**
    * @var repos_path Path of the svn repository
    */
    public function USVN_Client_Hook_Hook($repos_path)
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
}