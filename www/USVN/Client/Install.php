<?php
/**
 * Install usvn hooks.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage install
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Client_Install
{
    private $path;
    private $url;
    private $password;
    private $user;
	private $httpclient;
	private $project;

	/**
	* @param string Local path of svn repository
	* @param string Url of USVN
	* @param string Project name
	* @param string Auth id
	* @param Zend_Http_Client HTTP client for XML don't change this except for tests
	*/
    public function __construct($path, $url, $project, $authid, $httpclient = NULL)
    {
        if (!USVN_Client_SVNUtils::isSVNRepository($path)) {
            throw new USVN_Exception("$path is not a valid SVN repository");
        }
        $this->path = $path.'/';
        $this->url = $url;
        $this->authid = $authid;
		$this->httpclient = $httpclient;
		$this->project = $project;
		$this->checkServer();
        if (!file_exists($this->path.'/usvn')) {
            if (!@mkdir($this->path.'/usvn')) {
                throw new USVN_Exception("Can't create ".$this->path.'/usvn'.".");
            }
        }
        $this->createConfigFile();
        $this->installHooks();
        $this->installSourceFiles();
    }

	private function checkServer()
	{
		$url = str_replace("//project", "/project", $this->url . "/project/" . $this->project . "/svnhooks/");
		 $xmlrpc = new Zend_XmlRpc_Client($url);
		 if ($this->httpclient !== NULL) {
			$xmlrpc->setHttpClient($this->httpclient);
		}
		try {
			$res = $xmlrpc->call('usvn.client.hooks.validUSVNServer', array($this->project, $this->authid));
		}
		catch (Exception $e) {
                throw new USVN_Exception("Invalid server URL ({$this->url}) or not an USVN server.");
		}
		if ($res !== 0) {
                throw new USVN_Exception($res);
		}
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
		if (!@mkdir($dst, 0700)) {
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
		$dst = $this->path.'/usvn/USVN';
        if (!@mkdir($dst, 0700)) {
			throw new USVN_Exception("Can't create directory $dst.");
		}
		$this->copyLibraryFiles('USVN/Client');
		copy('www/USVN/Exception.php', $this->path.'/usvn/USVN/Exception.php');
		$this->copyLibraryFiles('Zend/');
    }

    private function createConfigFile()
    {
        $config = new USVN_Client_Config($this->path);
        $config->url = $this->url;
        $config->auth = $this->authid;
		$config->project = $this->project;
        $config->save();
    }

    private function getHookPath()
    {
        return 'client/hooks/';
    }
}
