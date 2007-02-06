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

    public function USVN_Client_Install($path, $url, $user, $password)
    {
        if (!USVN_Client_SVNUtils::isSVNRepository($path))
        {
            throw new Exception("$path is not a valid SVN repository");
        }
        $this->path = $path.'/';
        mkdir($this->path.'/usvn');
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

    private function getHookPath()
    {
        return 'client/hooks/';
    }
}