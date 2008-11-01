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
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

/**
 * @see Zend_Validate_File_MimeType
 */
require_once 'Zend/Validate/File/MimeType.php';

/**
 * Validator for the mime type of a file
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_ExcludeMimeType extends Zend_Validate_File_MimeType
{
    const FALSE_TYPE   = 'fileExcludeMimeTypeFalse';
    const NOT_DETECTED = 'fileExcludeMimeTypeNotDetected';
    const NOT_READABLE = 'fileExcludeMimeTypeNotReadable';

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if the mimetype of the file does not matche the given ones. Also parts
     * of mimetypes can be checked. If you give for example "image" all image
     * mime types will not be accepted like "image/gif", "image/jpeg" and so on.
     *
     * @param  string $value Real file to check for mimetype
     * @param  array  $file  File data from Zend_File_Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        // Is file readable ?
        if (!@is_readable($value)) {
            $this->_throw($file, self::NOT_READABLE);
            return false;
        }

        if ($file !== null) {
            $info['type'] = $file['type'];
        } else {
            $this->_throw($file, self::NOT_DETECTED);
            return false;
        }

        $mimetype = $this->getMimeType(true);
        if (in_array($info['type'], $mimetype)) {
            $this->_throw($file, self::FALSE_TYPE);
            return false;
        }

        foreach($mimetype as $mime) {
            $types = explode('/', $info['type']);
            if (in_array($mime, $types)) {
                $this->_throw($file, self::FALSE_TYPE);
                return false;
            }
        }

        return true;
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            $this->_value = $file['name'];
        }

        $this->_error($errorType);
        return false;
    }
}
