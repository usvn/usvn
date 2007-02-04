<?php
/**
* @package client
* @subpackage install
*/

require_once 'USVN/Client/SVNUtils.php';

/**
* The install command
*/
class USVN_Client_Install
{
    public function USVN_Client_Install($path, $url, $user, $password)
    {
        if (!USVN_Client_SVNUtils::isSVNRepository($path))
        {
            throw new Exception("$path is not a valid SVN repository");
        }
    }
}