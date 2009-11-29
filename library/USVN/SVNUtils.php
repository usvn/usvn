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
 * $Id: SVNUtils.php 1536 2008-11-01 16:08:37Z duponc_j $
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
    public static function isSVNRepository($path, $available = false)
    {
        if (file_exists($path . "/hooks") && file_exists($path . "/db")){
            return true;
        }
				if ($available && is_dir($path)) {
					$not_dir = false;
					foreach (array_diff(scandir($path), array('.', '..')) as $file) {
						if (!is_dir($path.'/'.$file)) {
							$not_dir = true;
						}
					}
					if ($not_dir === false) {
						return true;
					}
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
		return USVN_ConsoleUtils::runCmdCaptureMessage(USVN_SVNUtils::svnCommand("$command -t $transaction $repository"), $return);
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
		return USVN_ConsoleUtils::runCmdCaptureMessage(USVN_SVNUtils::svnCommand("$command -r $revision $repository"), $return);
	}

	/**
	* Return minor version of svn client
	*
	* @return int (ex for svn 1.3.4 return 3)
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
		return USVN_SVNUtils::parseSvnVersion(USVN_ConsoleUtils::runCmdCaptureMessage(USVN_SVNUtils::svnCommand("--version --quiet"), $return));
	}

	/**
	* Parse output of svn --version for return the version number
	*
	* @param string output of svn --version
	* @return array  (ex: for svn version 1.3.3 array(1, 3, 3))
	*/
	public static function parseSvnVersion($version)
	{
		$version = rtrim($version);
		return explode(".", $version);
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
	* Get the command svn
	*
	* @param string Parameters
	*/
	public static function svnCommand($cmd)
	{
		return "svn --config-dir /USVN/fake $cmd";
	}

	/**
	* Get the command svnadmin
	*
	* @param string Parameters
	*/
	public static function svnadminCommand($cmd)
	{
		return "svnadmin --config-dir /USVN/fake $cmd";
	}

    /**
    * Import file into subversion repository
    *
    * @param string path to server repository
    * @param string path to directory to import
    */
    private static function _svnImport($server, $local)
    {
        $server = USVN_SVNUtils::getRepositoryPath($server);
        $local = escapeshellarg($local);
        $cmd = USVN_SVNUtils::svnCommand("import --non-interactive --username USVN -m \"" . T_("Commit by USVN") ."\" $local $server");
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
      $escape_path = escapeshellarg($path);
      $message = USVN_ConsoleUtils::runCmdCaptureMessage(USVN_SVNUtils::svnadminCommand("create $escape_path"), $return);
      if ($return) {
		throw new USVN_Exception(T_("Can't create subversion repository: %s"), $message);
      }
    }

    /**
    * Create standard svn directories
    * /trunk
    * /tags
    * /branches
    *
    * @param string Path to create subversion
    */
    public static function createStandardDirectories($path)
    {
      $tmpdir = USVN_DirectoryUtils::getTmpDirectory();
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
		$src = USVN_SVNUtils::getRepositoryPath($src);
		$message = USVN_ConsoleUtils::runCmdCaptureMessage(USVN_SVNUtils::svnCommand("co $src $dst"), $return);
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
		$escape_path = USVN_SVNUtils::getRepositoryPath($repository . '/' . $path);
		$lists = USVN_ConsoleUtils::runCmdCaptureMessageUnsafe(USVN_SVNUtils::svnCommand("ls --xml $escape_path"), $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't list subversion repository: %s"), $lists);
		}
		$res = array();
		$xml = new SimpleXMLElement($lists);
		foreach ($xml->list->entry as $list)
		{
			if ($list['kind'] == 'file') {
				array_push($res, array(
								'name' => (string)$list->name,
								'isDirectory' => false,
								'path' => str_replace('//', '/', $path . '/' . $list->name),
								'size' => $list->size,
								'revision' => $list->commit['revision'],
								'author' => $list->commit->author,
								'date' => $list->commit->date
								)
							);
			}
			else
			{
				array_push($res, array(
								'name' => (string)$list->name,
								'isDirectory' => true,
								'path' => str_replace('//', '/', $path . '/' . $list->name  . '/'),
								'revision' =>  $list->commit['revision'],
								'author' => $list->commit->author,
								'date' => $list->commit->date
								)
							) ;
			}
		}
		$sortfunc = create_function('$a, $b', 'return (strcasecmp($a["name"], $b["name"]));');
		usort($res, $sortfunc);
		return $res;
	}

	/**
	 * This code work only for directory
	 * Directory separator need to be /
	 */
	private static function getCannocialPath($path)
	{
		$origpath = $path;
		$path = preg_replace('#//+#', '/', $path);
		$list_path = preg_split('#/#', $path, -1, PREG_SPLIT_NO_EMPTY);
		$i = 0;
		while (isset($list_path[$i])) {
			if ($list_path[$i] == '..') {
				unset($list_path[$i]);
				if ($i > 0) {
					unset($list_path[$i - 1]);
				}
				$list_path = array_values($list_path);
				$i = 0;
			}
			elseif ($list_path[$i] == '.') {
				unset($list_path[$i]);
				$list_path = array_values($list_path);
				$i = 0;
			}
			else {
				$i++;
			}
		}
		$newpath = '';
		$first = true;
		foreach ($list_path as $path) {
			if (!$first) {
				$newpath .= '/';
			}
			else {
				$first = false;
			}
			$newpath .= $path;
		}
		if ($origpath[0] == '/') {
			return '/' . $newpath;
		}
		if(strtoupper(substr(PHP_OS, 0,3)) == 'WIN' ) {
			return $newpath;
		}
		else {
			return getcwd() . '/' . $newpath;
		}
	}

	/**
	 * Return clean version of a Subversion repository path betwenn ' and with file:// before
	 *
	 * @param string Path to repository
	 * @return string absolute path to repository
	 */
	public static function getRepositoryPath($path)
	{
		if(strtoupper(substr(PHP_OS, 0,3)) == 'WIN' ) {
			$newpath = realpath($path);
			if ($newpath === FALSE) {
				$path = str_replace('//', '/', str_replace('\\', '/', $path));
				$path = USVN_SVNUtils::getCannocialPath($path);
			}
			else {
				$path = $newpath;
			}
			return "\"file:///" . str_replace('\\', '/', $path) . "\"";
		}
		$newpath = realpath($path);
		if ($newpath === FALSE) {
			$newpath = USVN_SVNUtils::getCannocialPath($path);
		}
		return escapeshellarg('file://' . $newpath);
	}

	/**
	*
	* @param string Project name
	* @param string Path into Subversion
	* @return string Return url of files into Subversion
	*/
	public static function getSubversionUrl($project, $path)
	{
		$config = Zend_Registry::get('config');
		$url = $config->subversion->url;
		if (substr($url, -1, 1) != '/') {
			$url .= '/';
		}
		$url .= $project . $path;
		return $url;
	}
}
