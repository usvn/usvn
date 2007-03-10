<?php
/**
* @package client
* @subpackage install
* @since 0.5
*/

/**
* The install command
*/
class USVN_Client_Install
{
    private $path;
    private $url;
    private $password;
    private $user;

	/**
	* @param string Local path of svn repository
	* @param string Url of USVN
	* @param string Auth id
	*/
    public function USVN_Client_Install($path, $url, $authid)
    {
        if (!USVN_Client_SVNUtils::isSVNRepository($path)) {
            throw new USVN_Exception("$path is not a valid SVN repository");
        }
        $this->path = $path.'/';
        $this->url = $url;
        $this->authid = $authid;
		if (!@mkdir($this->path.'/usvn')) {
			throw new USVN_Exception("Can't create ".$this->path.'/usvn'.".");
		}
        $this->createConfigFile();
        $this->installHooks();
        $this->installSourceFiles();
    }

    private function installHooks()
    {
        foreach (USVN_Client_SVNUtils::$hooks as $hook)
        {
            $src = $this->getHookPath()."/{$hook}";
            $dst = $this->path."/hooks/{$hook}";
            if (!@copy($src, $dst)) {
                throw new USVN_Exception("Can't copy $src to $dst.");
            }
            if (!@chmod($dst, 0700)) {
                throw new USVN_Exception("Can't change right of $dst.");
            }
        }
    }

	private function copyLibraryFiles($dir)
	{
		$dst = $this->path.'/usvn/'.$dir;
		if (!@mkdir($dst)) {
			throw new USVN_Exception("Can't create $dst");
		}
		$src = "www/".$dir;
         if ($dh = @opendir($src)) {
            while (($file = readdir($dh)) !== false) {
                if ($file[0] != '.') {
					if (is_dir($src.'/'.$file)) {
						$this->copyLibraryFiles($dir.'/'.$file);
					}
					else {
						copy($src.'/'.$file, $dst.'/'.$file);
					}
                }
            }
            closedir($dh);
        }
		else {
			throw new USVN_Exception("Can't read USVN library source code. Check right of $dir");
		}
	}

    private function installSourceFiles()
    {
        mkdir($this->path.'/usvn/USVN');
		$this->copyLibraryFiles('USVN/Client');
    }

    private function createConfigFile()
    {
        $config = new USVN_Client_Config($this->path);
        $config->url = $this->url;
        $config->auth = $this->authid;
        $config->save();
    }

    private function getHookPath()
    {
        return 'client/hooks/';
    }
}