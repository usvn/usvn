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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: InArray.php 3278 2007-02-07 21:54:50Z darby $
 */

/**
 * @see Zend_Validate_Interface
 */
require_once 'Zend/Validate/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Validate_Abstract implements Zend_Validate_Interface
{

    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @var array
     */
    protected $_messageVariables = array();

    /**
     * @var array
     */
    protected $_messageTemplates = array();

    /**
     * @var array
     */
    protected $_messages = array();

    /**
     * @var array
     */
    protected $_errors = array();

    /**
     * @param string $messageKey
     * @param string $value
     * @return string
     */
    protected function _createMessage($messageKey, $value)
    {
        if (!isset($this->_messageTemplates[$messageKey])) {
            return null;
        }

        $message = $this->_messageTemplates[$messageKey];
        $message = str_replace("%value%", (string) $value, $message);
        foreach ($this->_messageVariables as $ident => $property) {
            $message = str_replace("%$ident%", $this->$property, $message);
        }
        return $message;
    }

    /**
     * @param string $messageKey OPTIONAL
     * @param string $value      OPTIONAL
     * @return void
     */
    protected function _error($messageKey = null, $value = null)
    {
        if ($messageKey === null) {
            $messageKey = current(array_keys($this->_messageTemplates));
        }
        if ($value === null) {
            $value = $this->_value;
        }
        $this->_errors[]   = $messageKey;
        $this->_messages[] = $this->_createMessage($messageKey, $value);
    }

    /**
     * @param mixed $value
     * @return void
     */
    protected function _setValue($value)
    {
        $this->_value    = $value;
        $this->_errors   = array();
        $this->_messages = array();
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * @param string $messageString
     * @param string $messageKey    OPTIONAL
     * @return Zend_Validate_Abstract
     * @throws Zend_Validate_Exception
     */
    public function setMessage($messageString, $messageKey = null)
    {
        if ($messageKey === null) {
            $messageKey = current(array_keys($this->_messageTemplates));
        }
        if (!isset($this->_messageTemplates[$messageKey])) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("No message template exists for key '$messageKey'");
        }
        $this->_messageTemplates[$messageKey] = $messageString;
        return $this;
    }

    /**
     * @param array $messages
     * @return Zend_Validate_Abstract
     */
    public function setMessages(array $messages)
    {
        foreach ($messages as $key => $message) {
            $this->setMessage($message, $key);
        }
        return $this;
    }

    /**
     * @param string $property
     * @return mixed
     * @throws Zend_Validate_Exception
     */
    public function __get($property)
    {
        if ($property == 'value') {
            return $this->_value;
        }
        if (array_key_exists($property, $this->_messageVariables)) {
            return $this->{$this->_messageVariables[$property]};
        }
        /**
         * @see Zend_Validate_Exception
         */
        require_once 'Zend/Validate/Exception.php';
        throw new Zend_Validate_Exception("No property exists by the name '$property'");
    }

}
