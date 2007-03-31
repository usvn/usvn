<?php
/**
 * Tools for manipulate directories
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
class USVN_DirectoryUtils
{
    /**
    * Remove a directory even if it is not empty.
    */
    static public function removeDirectory($path)
    {
        try {
            @$dir = new RecursiveDirectoryIterator($path);
        }
        catch(Exception $e) {
            return;
        }
        foreach(@new RecursiveIteratorIterator($dir) as $file) {
            unlink($file);
        }
        foreach($dir as $subDir) {
            if(!@rmdir($subDir)) {
                USVN_DirectoryUtils::removeDirectory($subDir);
            }
        }
        $dir = NULL; // Else on windows that doesn't work....
        @rmdir($path);
    }
}
