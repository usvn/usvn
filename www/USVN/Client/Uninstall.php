<?php
/**
 * Uninstall usvn hooks.
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 * @subpackage uninstall
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once 'USVN/Client/SVNUtils.php';
require_once 'USVN/DirectoryUtils.php';
require_once 'USVN/Exception.php';

/**
* The uninstall command
*/
class USVN_Client_Uninstall
{
    private $path;

    public function USVN_Client_Uninstall($path)
    {
        if (!USVN_Client_SVNUtils::isSVNRepository($path))
        {
            throw new USVN_Exception("$path is not a valid SVN repository");
        }
        $this->path = $path.'/';
        $this->removeHooks();
        USVN_DirectoryUtils::removeDirectory($this->path.'/usvn');
    }

    private function removeHooks()
    {
        foreach (USVN_Client_SVNUtils::$hooks as $hook)
        {
            $dst = $this->path."/hooks/{$hook}";
            if (!@unlink($dst))
            {
                throw new USVN_Exception("Can't remove $dst.\nAre your sure that {$this->path} is an usvn repository?");
            }
        }
    }
}
