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
        if (file_exists($path)) {
            chmod($path, 0777);
            try {
                @$dir = new RecursiveDirectoryIterator($path);
            }
            catch(Exception $e) {
                return;
            }
            foreach(@new RecursiveIteratorIterator($dir) as $file) {
                chmod($file, 0777);
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

    /**
     * List the first level of a directory
     *
     * @param string $path
     * @todo add option to this method (level to scan, exclude file or not, juste directory, get more infomations...
     * @return array
     */
    static public function listDirectory($path)
	{
		$res = array();
		$dh = opendir($path);
		if (!$dh) {
			throw new USVN_Exception(T_("Can't read directory (%s).", $path));
		}
		while (($subDir = readdir($dh)) !== false) {
            if ($subDir != '.' && $subDir != '..' && $subDir != '.svn') {
				array_push($res, $subDir);
			}
        }
		return $res;
	}
}
