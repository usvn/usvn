<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_File_Rename implements Zend_Filter_Interface
{
    /**
     * Array of oldfile => newfile
     */
    protected $_files = array();

    /**
     * Array of oldfile => overwrite
     */
    protected $_overwrite = array();

    /**
     * Class constructor
     *
     * @param string|array $oldfile   File which should be renamed/moved
     * @param string|array $newfile   New filename, when not set $oldfile will be used as new filename
     *                                for $value when filtering
     * @param boolean      $overwrite If set to true, it will overwrite existing files
     */
    public function __construct($oldfile, $newfile = null, $overwrite = false)
    {
        $this->setFile($oldfile, $newfile, $overwrite);
    }

    /**
     * Returns the files to rename and their new name and location
     *
     * @param  boolean $overwrite Select if files to overwrite should also be returned:
     *                            null, default, will return all files
     *                            false, will return only files which should not be overwritten
     *                            true, will return only files which should be overwritten
     * @return array Old and new files
     */
    public function getFile($overwrite = null)
    {
        if ($overwrite === null) {
            return $this->_files;
        }

        $files = array();
        if ((boolean) $overwrite == true) {
            foreach($this->_overwrite as $file => $flag) {
                if ($flag == (boolean) $overwrite) {
                    $files[$file] = $this->_files[$file];
                }
            }
        } else {
            foreach($this->_overwrite as $file => $flag) {
                if ($flag != (boolean) $overwrite) {
                    $files[$file] = $this->_files[$file];
                }
            }
        }

        return $files;
    }

    /**
     * Sets a new file or directory as target, deleting existing ones
     *
     * @param  string|array $oldfile   Old file which should be renamed/moved, if $newfile
     *                                 is empty $oldfile will be used as $newfile
     * @param  string|array $newfile   New file to set the name to
     * @param  boolean      $overwrite If true any existing file will be overwritten
     * @return Zend_Filter_File_Rename
     */
    public function setFile($oldfile, $newfile = null, $overwrite = false)
    {
        $this->_files = array();
        $this->addFile($oldfile, $newfile, $overwrite);

        return $this;
    }

    /**
     * Adds a new file or directory as target to the existing ones
     *
     * @param  string|array $oldfile   Old file which should be renamed/moved, if $newfile
     *                                 is empty $oldfile will be used as $newfile
     * @param  string|array $newfile   New file to set the name to
     * @param  boolean      $overwrite If true any existing file will be overwritten
     * @return Zend_Filter_File_Rename
     */
    public function addFile($oldfile, $newfile = null, $overwrite = false)
    {
        if (is_bool($newfile)) {
            $overwrite = $newfile;
            $newfile   = null;
        }

        if (is_array($oldfile)) {
            if (is_array($newfile)) {
                $files = array_combine($oldfile, $newfile);
            } else {
                $files = $oldfile;
            }
        } else {
            if (!empty($newfile)) {
                $files = array($oldfile => $newfile);
            } else {
                $files = array(0 => $oldfile);
            }
        }

        $newoption = null;
        foreach ($files as $oldfile => $newfile) {
            if (isset($this->_files[$oldfile]) and (empty($newfile))) {
                unset($this->_files[$oldfile]);
                unset($this->_overwrite[$oldfile]); 
            }

            if (is_bool($newfile)) {
                $newoption = $newfile;
            } else {
                $this->_files[$oldfile]     = $newfile;
                $this->_overwrite[$oldfile] = $overwrite;
            }
        }

        if ($newoption !== null) {
            foreach($files as $oldfile => $newfile) {
                $this->_overwrite[$oldfile] = $newoption;
            }
        }

        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Renames the file $value to the new name set before
     * Returns the file $value, removing all but digit characters
     *
     * @param  string $value Full path of file to change
     * @return string The new filename which has been set, or false when there were errors
     */
    public function filter($value)
    {
        if (isset($this->_files[$value])) {
            $newfile   = $this->_files[$value];
            $overwrite = $this->_overwrite[$value];
        } else if (isset($this->_files[0])) {
            if (!file_exists($value)) {
                return $value;
            }

            $newfile   = $this->_files[0];
            $overwrite = $this->_overwrite[0];
        } else {
            return $value;
        }

        if (is_dir($newfile)) {
            $file = basename($value);
            $last = $newfile[strlen($newfile) - 1];
            if (($last != '/') and ($last != '\\')) {
                $newfile .= DIRECTORY_SEPARATOR;
            }

            $newfile .=  $file;
        }

        if (($overwrite == true) and (file_exists($newfile))) {
            unlink($newfile);
        }

        if (file_exists($newfile)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("File $value could not be renamed. It already exists.");
        }

        $result = rename($value, $newfile);

        if ($result === true) {
            return $newfile;
        }

        return false;
    }
}
