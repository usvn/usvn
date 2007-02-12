<?php
/**
* @package client
* @subpackage install
* @since 0.5
*/

require_once 'USVN/Client/SVNUtils.php';
require_once 'USVN/Client/Config.php';

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
            if (!@chmod($dst, 0700))
            {
                throw new Exception("Can't change right of $dst.");
            }
        }
    }

    private function createConfigFile()
    {
        $config = new USVN_Client_Config($this->path);
        $config->url = $this->url;
        $config->user = $this->user;
        $config->password = $this->password;
        $config->save();
    }

    private function getHookPath()
    {
        return 'client/hooks/';
    }
}