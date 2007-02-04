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

    public static function isSVNRepository($path)
    {
        if (file_exists($path."/hooks") && file_exists($path."/dav"))
        {
            return true;
        }
        return false;
    }
}