<?php
/**
 * Class who handle subversion hooks via XML-RPC
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package svnhooks
 * @subpackage model
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_modules_svnhooks_models_Hooks
{
	private function checkAuthId($authid)
	{
		return true;
	}

	/**
	* Start commit hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string The user login
	* @return string or 0 String if error in commit, 0 if it's OK
	*/
	public function startCommit($authid, $user)
	{
		//file_put_contents('tests/tmp/testhooksStartCommit', "$authid $user\n");
		return 0;
	}

	/**
	* Pre commit hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string The user login
	* @param string Log message
	* @param array List of changed files and here status (exemple: array(array('U', 'test'), array('A', 'toto')))
	* @return string or 0 String if error in commit, 0 if it's OK
	*/
	public function preCommit($authid, $user, $log, $changedfiles)
	{
		//file_put_contents('tests/tmp/testhooksPreCommit', "$authid\nUser: $user\nLog: $log\n--------------------\n".var_export($changedfiles, true)."\n");
		return 0;
	}

	/**
	* Post commit hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param integer Revision number
	* @param string The user login
	* @param string Log message
	* @param array List of changed files and here status (exemple: array(array('U', 'test'), array('A', 'toto')))
	*/
	public function postCommit($authid, $revision, $user, $log, $changedfiles)
	{
		//file_put_contents('tests/tmp/testhooksPostCommit', "$authid\n$revision\nUser: $user\nLog: $log\n--------------------\n".var_export($changedfiles, true)."\n");
	}

	/**
	* Pre lock hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string Path
	* @param string The user login
	* @return string or 0 String if error in lock, 0 if it's OK
	*/
	public function preLock($authid, $path, $user)
	{
		//file_put_contents('tests/tmp/testhooksPreLock', "$authid\n$path\nUser: $user\n");
		return 0;
	}

	/**
	* Post lock hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string Path
	* @param string The user login
	*/
	public function postLock($authid, $path, $user)
	{
		//file_put_contents('tests/tmp/testhooksPostLock', "$authid\nLock file: $path\nUser: $user\n");
	}

	/**
	* Pre unlock hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string Path
	* @param string The user login
	* @return string or 0 String if error in lock, 0 if it's OK
	*/
	public function preUnlock($authid, $path, $user)
	{
		//file_put_contents('tests/tmp/testhooksPreUnlock', "$authid\nUnlock file: $path\nUser: $user\n");
		return 0;
	}

	/**
	* Post unlock hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string Path
	* @param string The user login
	*/
	public function postUnlock($authid, $path, $user)
	{
		//file_put_contents('tests/tmp/testhooksPostUnlock', "$authid\nUnlock file: $path\nUser: $user\n");
	}

	/**
	* Pre revprop change hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param integer Revision
	* @param string The user login
	* @param string Property will be change (ex: svn:log)
	* @param string Action (ex: M)
	* @param string New property value
	* @return string or 0 String if error in property change, 0 if it's OK
	*/
	public function preRevpropChange($authid, $revision, $user, $property, $action, $value)
	{
		//file_put_contents('tests/tmp/testhooksPreRevpropChange', "$authid\nRevision: $revision\nUser: $user\nProperty: $property\nAction: $action\nValue: $value\n");
		return 0;
	}

	/**
	* Pre revprop change hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param integer Revision
	* @param string The user login
	* @param string Property will be change (ex: svn:log)
	* @param string Action (ex: M)
	* @param string Old property value
	*/
	public function postRevpropChange($authid, $revision, $user, $property, $action, $value)
	{
		//file_put_contents('tests/tmp/testhooksPostRevpropChange', "$authid\nRevision: $revision\nUser: $user\nProperty: $property\nAction: $action\nValue: $value\n");
	}

	/**
	* Use by client to check if he speak to a valid  USVN server
	*
	* @param string The auth id for identify the server
	* @return bool False if authid is invalid
	*/
	public function validUSVNServer($authid)
	{
		return true;
	}
}
