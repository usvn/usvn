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
		$list = explode(" ", $list);
		$first = true;
		$action = "";
		$file = "";
		foreach ($list as $item) {
			if ($first && in_array($item, array('M', 'D', 'A'))) {
				if ($action) {
					array_push($res, array($action, $file));
				}
				$action = $item;
				$file = "";
				$first = false;
			}
			else{
				if ($file) {
					$file .= " $item";
				}
				else {
					$file = $item;
				}
				$first = true;
			}
		}
		array_push($res, array($action, $file));
		return $res;
	}
}