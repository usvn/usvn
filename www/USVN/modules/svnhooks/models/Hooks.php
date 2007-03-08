<?php
class USVN_modules_svnhooks_Hooks
{
	/**
	* Start commit hook publish for XML-RPC
	*
	* @param string The auth id for identify the server
	* @param string The user login
	* @return string or 0 String if error in commit, 0 if it's OK
	*/
	public function startCommit($authid, $user)
	{
		file_put_contents('/tmp/testhooksStartCommit', "$authid $user\n");
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
		file_put_contents('/tmp/testhooksPreCommit', "$authid\nUser: $user\nLog: $log\n--------------------\n".var_export($changedfiles, true)."\n");
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
		file_put_contents('/tmp/testhooksPostCommit', "$authid\n$revision\nUser: $user\nLog: $log\n--------------------\n".var_export($changedfiles, true)."\n");
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
		file_put_contents('/tmp/testhooksPreLock', "$authid\n$path\nUser: $user\n");
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
		file_put_contents('/tmp/testhooksPostLock', "$authid\nLock file: $path\nUser: $user\n");
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
		file_put_contents('/tmp/testhooksPreUnlock', "$authid\nUnlock file: $path\nUser: $user\n");
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
		file_put_contents('/tmp/testhooksPostUnlock', "$authid\nUnlock file: $path\nUser: $user\n");
	}
}