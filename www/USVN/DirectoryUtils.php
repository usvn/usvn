<?php
/**
* @package utils
* @subpackage directory
* @since 0.5
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
