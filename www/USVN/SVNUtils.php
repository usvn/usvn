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
        if (file_exists($path . "/hooks") && file_exists($path . "/dav"))
        {
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
		$svnlook = USVN_SVNUtils::getSvnCommand('svnlook');
		return `$svnlook $command -t $transaction $repository`;
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
		return `svnlook $command -r $revision $repository`;
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
		return USVN_SVNUtils::parseSvnVersion(`svn --version`);
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
	* Create SVN repository
	* @param string Path to create subversion
	*/
	public static function createSvn($path)
	{
		$message = USVN_ConsoleUtils::runCmdCaptureMessage("svnadmin create $path", $return);
		if ($return) {
			throw new USVN_Exception(T_("Can't create subversion repository: %s"), $message);
		}
	}
}
