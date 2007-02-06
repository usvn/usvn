<?php
/**
* @package utils
* @subpackage directory
*/

/**
* Provide usefull static methods to manipulate directory
*/
class USVN_DirectoryUtils
{
    /**
    * Remove a directory even if it is not empty.
    */
    public function removeDirectory($path)
    {
        $dir = new RecursiveDirectoryIterator($path);
        foreach(new RecursiveIteratorIterator($dir) as $file)
        {
            unlink($file);
        }
        foreach($dir as $subDir)
        {
            if(!@rmdir($subDir))
            {
                USVN_DirectoryUtils::removeDirectory($subDir);
            }
        }
        rmdir($path);
    }
}
