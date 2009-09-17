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
 * $Id: DirectoryUtils.php 1345 2007-11-17 08:53:56Z dolean_j $
 */
class USVN_DirectoryUtils
{
    /**
    * Remove a directory even if it is not empty.
    */
    static public function removeDirectory($remove_path)
    {
		if (!file_exists($remove_path)) {
			return;
		}
        if (($path = realpath($remove_path)) !== FALSE) {
            if (@chmod($path, 0777) === FALSE) {
                throw new USVN_Exception(T_("Can't delete directory %s. Permission denied."), $path);
            }
            try {
            	if (is_dir($path)) {
                	$dh = opendir($path);
            	} else {
            		return;
            	}
            }
            catch(Exception $e) {
                return;
            }
            while (($file = readdir($dh)) !== false) {
				if ($file != '.' && $file != '..') {
					if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
						USVN_DirectoryUtils::removeDirectory($path . DIRECTORY_SEPARATOR . $file);
					}
					else {
                        if (chmod($path . DIRECTORY_SEPARATOR . $file, 0777) === FALSE) {
                            throw new USVN_Exception(T_("Can't delete file %s.", $path . DIRECTORY_SEPARATOR . $file));
                        }
						unlink($path . DIRECTORY_SEPARATOR . $file);
					}
				}
            }
            closedir($dh);
            if (@rmdir($path) === FALSE) {
                throw new USVN_Exception(T_("Can't delete directory %s."), $path);
            }
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
			throw new USVN_Exception(T_("Can't read directory %s.", $path));
		}
		while (($subDir = readdir($dh)) !== false) {
            if ($subDir != '.' && $subDir != '..' && $subDir != '.svn') {
				array_push($res, $subDir);
			}
        }
		return $res;
	}

	/**
	* Create and return a tmp directory
	*
	* @return string Path to tmp directory
	*/
	static public function getTmpDirectory()
	{
		$path = tempnam("", "USVN_");
		unlink($path);
		mkdir($path);
		return $path;
	}

	/**
	 * Return true if path === / on Unix or path == C: or D: or ... on Windows
	 *
	 * @param Path
	 * @return boolean
	 */
	static public function isRootDirectory($path)
	{
		if(strtoupper(substr(PHP_OS, 0,3)) == 'WIN' ) {
			if (!preg_match('#[A-Z]:([\\\\/]+).+#', $path)) {
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return $path === '/';
		}
	}

	/**
	 * Return true if the first path is include into the other path
	 *
	 * @param string $path1
	 * @param string $path2
	 * @return boolean
	 */
	static public function firstDirectoryIsInclude($path1, $path2)
	{
		$tmp1 = realpath($path1);
		$tmp2 = realpath($path2);
		$tmp1 = str_replace('\\', '/', $tmp1);
		$tmp2 = str_replace('\\', '/', $tmp2);
		if (!strncmp($tmp1, $tmp2, strlen($tmp2))) {
			return true;
		}
		return false;
	}
}
