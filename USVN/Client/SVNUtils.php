<?php
/**
* @package client
* @subpackage utils
*/

/**
* Usefull static method to manipulate an svn repository
*/
class USVN_Client_SVNUtils
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
        if (file_exists($path."/hooks") && file_exists($path."/dav"))
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
		return `svnlook $command -t $transaction $repository`;
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
}