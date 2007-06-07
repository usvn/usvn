<?php
/**
 * Crypt password
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.6
 * @package Un_package_par_exemple_client
 * @subpackage Le_sous_package_par_exemple_hooks
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_Crypt
{
    /**
    * @param string Password
    * @return Encrypt password
    */
    static public function crypt($password)
    {
        return crypt($password, $password);
    }

    /**
    * Check if a clear password match encrypt password
    *
    * @param string
    * @param string
    * @return bool
    */
    static public function checkPassword($clear, $encrypt)
    {
        if (crypt($clear, $encrypt) == $encrypt)
            return true;
        return false;
    }
}
?>