<?php
class USVN_modules_svnhooks_Hooks
{
	/**
	* Start commit hook publish for XML-RPC
	*
	* @param string $repository The auth id for identify the server
	* @param string $user The user login
	* @return string
	*/
	public function startCommit($authid, $user)
	{
		file_put_contents('/tmp/testhooks', "test $authid $user\n");
		return "Youpi";
	}
}