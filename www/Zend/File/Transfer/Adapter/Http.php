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
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

require_once 'Zend/File/Transfer/Adapter/Abstract.php';

/**
 * File transfer adapter class for the HTTP protocol
 *
 * @category  Zend
 * @package   Zend_File_Transfer
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_File_Transfer_Adapter_Http extends Zend_File_Transfer_Adapter_Abstract
{
    /**
     * Constructor for Http File Transfers
     *
     * @param array $options OPTIONAL Options to set
     */
    public function __construct($options = array())
    {
        $this->_files = $this->_prepareFiles($_FILES);
        $this->addValidator('Upload', null, $this->_files);

        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Sets a validator for the class, erasing all previous set
     *
     * @param  string|array $validator Validator to set
     * @param  string|array $files     Files to limit this validator to
     * @return Zend_File_Transfer_Adapter
     */
    public function setValidators(array $validators, $files = null)
    {
        $this->clearValidators();
        $this->addValidator('Upload', null, $this->_files);
        return $this->addValidators($validators, $files);
    }

    /**
     * Send the file to the client (Download)
     *
     * @param  string|array $options Options for the file(s) to send
     * @return void
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function send($options = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Receive the file from the client (Upload)
     *
     * @param  string|array $files (Optional) Files to receive
     * @return bool
     */
    public function receive($files = null)
    {
        if (!$this->_validated) {
            if (!$this->isValid($files)) {
                return false;
            }
        }

        $check = $this->_getFiles($files);
        foreach ($check as $file => $content) {
            $directory   = '';
            $destination = $this->getDestination($file);
            if ($destination !== null) {
                $directory = $destination . DIRECTORY_SEPARATOR;
            }

            // Should never return false when it's tested by the upload validator
            if (!move_uploaded_file($content['tmp_name'], ($directory . $content['name']))) {
                if (isset($this->_options['ignoreNoFile'])) {
                    continue;
                }

                return false;
            }

            if (!$this->_filter($file)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Checks if the file was already sent
     *
     * @param  string|array $file Files to check
     * @return bool
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function isSent($file = null)
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Checks if the file was already received
     *
     * @param  string|array $files (Optional) Files to check
     * @return bool
     */
    public function isReceived($files = null)
    {
        $validate = new Zend_Validate_File_Upload();
        if (!$validate->isValid($files)) {
            return false;
        }

        return true;
    }

    /**
     * Returns the actual progress of file up-/downloads
     *
     * @return string Returns the state
     * @return int
     * @throws Zend_File_Transfer_Exception Not implemented
     */
    public function getProgress()
    {
        require_once 'Zend/File/Transfer/Exception.php';
        throw new Zend_File_Transfer_Exception('Method not implemented');
    }

    /**
     * Prepare the $_FILES array to match the internal syntax of one file per entry
     *
     * @param  array $files
     * @return array
     */
    protected function _prepareFiles(array $files = array())
    {
        $result = array();
        foreach ($files as $form => $content) {
            if (is_array($content['name'])) {
                foreach ($content as $param => $file) {
                    foreach ($file as $number => $target) {
                        $result[$form . '_' . $number . '_'][$param] = $target;
                    }
                }
            } else {
                $result[$form] = $content;
            }
        }

        return $result;
    }
}
