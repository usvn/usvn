<?php
/**
 * Usefull static method to manipulate an svn repository
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage utils
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_SVNUtils
{
    public static $hooks = array('post-commit',
                                        'post-unlock',
                                        'pre-revprop-change',
                                        'post-lock',
                                        'pre-commit',
                                        'pre-unlock',
                                        'post-revprop-change',
                                        'pre-lock',
                                        'start-commit');

	/**
	* @param string Path of subversion repository
	* @return bool
	*/
    public static function isSVNRepository($path)
    {
        if (file_exists($path . "/hooks") && file_exists($path . "/dav")){
            return true;
        }
        return false;
    }

	/**
	* @param string Output of svnlook changed
	* @return array Exemple array(array('M', 'tutu'), array('M', 'dir/tata'))
	*/
	public static function changedFiles($list)
	{
		$res = array();
		$list = explode("\n", $list);
		foreach($list as $line) {
			if ($line) {
				$ex = explode(" ", $line, 2);
				array_push($res, $ex);
			}
		}
		return $res;
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
	public static function svnLookTransaction($command, $repository, $transaction)
	{
		$command = escapeshellarg($command);
		$transaction = escapeshellarg($transaction);
		$repository = escapeshellarg($repository);
		return USVN_ConsoleUtils::runCmdCaptureMessage("svnlook $command -t $transaction $repository", $return);
	}

	/**
	* Call the svnlook binary on an svn revision.
	*
	* @param string svnlook command (see svnlook help)
	* @param string repository path
	* @param integer revision
	* @return string Output of svnlook
	* @see http://svnbook.red-bean.com/en/1.1/ch09s03.html
	*/
	public static function svnLookRevision($command, $repository, $revision)
	{
		$command = escapeshellarg($command);
		$revision = escapeshellarg($revision);
		$repository = escapeshellarg($repository);
		return USVN_ConsoleUtils::runCmdCaptureMessage("svnlook $command -r $revision $repository", $return);
	}

	/**
	* Return minor version of svn client
	*
	* @return array  (ex: for svn version 1.3.3 array(1, 3, 3))
	*/
	public static function getSvnMinorVersion()
	{
		$version = USVN_SVNUtils::getSvnVersion();
		return $version[1];
	}

	/**
	* Return version of svn client
	*
	* @return array  (ex: for svn version 1.3.3 array(1, 3, 3))
	*/
	public static function getSvnVersion()
	{
		return USVN_SVNUtils::parseSvnVersion(USVN_ConsoleUtils::runCmdCaptureMessage("svn --version"));
	}

	/**
	* Parse output of svn --version for return the version number
	*
	* @param string output of svn --version
	* @return array  (ex: for svn version 1.3.3 array(1, 3, 3))
	*/
	public static function parseSvnVersion($version)
	{
		$lines = explode("\n", $version);
		$version_number = explode(" ", $lines[0]);
		return explode(".", $version_number[2]);
	}


	/**
	* It's for use with testunit. This method simulate svnadmin create $path
	*
	* @param string Path to create directory structs
	*/
	public static function createSvnDirectoryStruct($path)
	{
		@mkdir($path);
		@mkdir($path . "/hooks");
		@mkdir($path . "/locks");
		@mkdir($path . "/conf");
		@mkdir($path . "/dav");
		@mkdir($path . "/db");
	}

    /**
    * Import file into subversion repository
    *
    * @param string path to server repository
    * @param string path to directory to import
    */
    private static function _svnImport($server, $local)
    {
        $server = USVN_SVNUtils::_getRepositoryPath($server);
        $local = escapeshellarg($local);
        $cmd = "svn import --non-interactive --username USVN -m \"" . T_("Commit by USVN") ."\" $local $server";
		$message = USVN_ConsoleUtils::runCmdCaptureMessage($cmd, $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't import into subversion repository.\nCommand:\n%s\n\nError:\n%s"), $cmd, $message);
		}
    }

    /**
    * Create SVN repository with standard organisation
    * /trunk
    * /tags
    * /branches
    *
    * @param string Path to create subversion
    */
    public static function createSvn($path)
    {
      $path = escapeshellarg($path);
      $message = USVN_ConsoleUtils::runCmdCaptureMessage("svnadmin create $path", $return);
      if ($return) {
		throw new USVN_Exception(T_("Can't create subversion repository: %s"), $message);
      }
      $tmpdir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . "USVN" . md5(uniqid());
      if (!mkdir($tmpdir)) {
		USVN_DirectoryUtils::removeDirectory($path);
		throw new USVN_Exception(T_("Can't checkout subversion repository: %s"), $message);
      }
      try {
		mkdir($tmpdir . DIRECTORY_SEPARATOR . "trunk");
		mkdir($tmpdir . DIRECTORY_SEPARATOR . "branches");
		mkdir($tmpdir . DIRECTORY_SEPARATOR . "tags");
		USVN_SVNUtils::_svnImport($path, $tmpdir);
      }
      catch (Exception $e) {
		USVN_DirectoryUtils::removeDirectory($path);
		USVN_DirectoryUtils::removeDirectory($tmpdir);
		throw $e;
      }
      USVN_DirectoryUtils::removeDirectory($tmpdir);
    }

    /**
    * Checkout SVN repository into filesystem
    * @param string Path to subversion repository
    * @param string Path to destination
    */
    public static function checkoutSvn($src, $dst)
    {
		$dst = escapeshellarg($dst);
		$src = USVN_SVNUtils::_getRepositoryPath($src);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("svn co $src $dst", $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't checkout subversion repository: %s"), $message);
		}
	}

	/**
	* List files into Subversion
    *
	* @param string Path to subversion repository
    * @param string Path into subversion repository
    * @return associative array like: array(array(name => "tutu", isDirectory => true))
	*/
	public static function listSvn($repository, $path)
	{
        $path = USVN_SVNUtils::_getRepositoryPath($repository."/$path");
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("svn ls $path", $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't list subversion repository: %s"), $message);
		}
        $res = array();
        foreach (preg_split("/[\r]?\n/", $message) as $file) {
            if (strlen($file)) {
                if (substr($file, -1, 1) == '/') {
                    array_push($res, array("name" => substr($file, 0, strlen($file) - 1), "isDirectory" => true, "path" => str_replace('//', '/', $path . "/" . $file)));
                }
                else {
                    array_push($res, array("name" => $file, "isDirectory" => false, "path" => str_replace('//', '/', $path . "/" . $file)));
                }
            }
        }
        return $res;
	}

	public static function log($repository, $limit = 0)
	{
        $repository = USVN_SVNUtils::_getRepositoryPath($repository);
        $limit = escapeshellarg($limit);
        if ($limit) {
        	$limit = "--limit $limit";
        }
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("svn log $limit $repository", $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't get subversion repository logs: %s"), $message);
		}
		return $message;
	}

	/**
	 * @param string Path to repository
	 * @return string absolute path to repository
	 */
	private static function _getRepositoryPath($path)
	{
		if(strtoupper(substr(PHP_OS, 0,3)) == 'WIN' ) {
			$newpath = realpath($path);
			if ($newpath !== FALSE) {
				$path = $newpath;
			}
			$path = escapeshellarg(str_replace('//', '/', str_replace('\\', '/', $path)));
			$path = preg_replace('/"[\s]*([^\s]*)[\s]*"/', '"file:///$1"', $path);
			return $path;
		}
		return 'file://' . realpath($path);
	}
}
