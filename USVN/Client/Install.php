<?php
/**
* @package client
* @subpackage install
*/

require_once 'USVN/Client/SVNUtils.php';

/**
* The install command
*/
class USVN_Client_Install
{
    private $path;
    private $url;
    private $password;
    private $user;

    public function USVN_Client_Install($path, $url, $user, $password)
    {
        if (!USVN_Client_SVNUtils::isSVNRepository($path))
        {
            throw new Exception("$path is not a valid SVN repository");
        }
        $this->path = $path.'/';
        $this->url = $url;
        $this->user = $user;
        $this->password = $password;
        mkdir($this->path.'/usvn');
        $this->createConfigFile();
        $this->installHooks();
    }

    private function installHooks()
    {
        foreach (USVN_Client_SVNUtils::$hooks as $hook)
        {
            $src = $this->getHookPath()."/{$hook}";
            $dst = $this->path."/hooks/{$hook}";
            if (!@copy($src, $dst))
            {
                throw new Exception("Can't copy $src to $dst.");
            }
        }
    }

    private function createConfigFile()
    {
        $xml = new SimpleXMLElement("<usvn></usvn>");
        $xml->url = $this->url;
        $xml->user = $this->user;
        $xml->password = $this->password;
        if (!@file_put_contents($this->path.'/usvn/config.xml', $xml->asXml()))
        {
                throw new Exception("Can't write config file.");
        }
    }

    private function getHookPath()
    {
        return 'client/hooks/';
    }
}