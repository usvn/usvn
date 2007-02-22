<?php
class USVN_modules_svnhooks_Hooks
{
	/**
	* Start commit hook publish for XML-RPC
	*
	* @param string $repository The repository path
	* @param string $user The user login
	* @return string
	*/
	public function startCommit($repository, $user)
	{
		file_put_contents('/tmp/testhooks', "test $repository $user\n");
		return "Youpi";
	}
}