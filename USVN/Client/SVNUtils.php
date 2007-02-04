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
    public static function isSVNRepository($path)
    {
        if (file_exists($path."/hooks") && file_exists($path."/dav"))
        {
            return true;
        }
        return false;
    }
}