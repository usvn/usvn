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
 * @version    $Id: Date.php 4974 2007-05-25 21:11:56Z bkarwin $
 */


/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_Date extends Zend_Validate_Abstract
{

    const NOT_YYYY_MM_DD = 'dateNotYYYY-MM-DD';
    const INVALID        = 'dateInvalid';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_YYYY_MM_DD => "'%value%' is not of the format YYYY-MM-DD",
        self::INVALID        => "'%value%' does not appear to be a valid date"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value is a valid date of the format YYYY-MM-DD
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        if (!preg_match('/\d{4}-\d{2}-\d{2}/', $valueString)) {
            $this->_error(self::NOT_YYYY_MM_DD);
            return false;
        }

        list($year, $month, $day) = sscanf($valueString, '%d-%d-%d');

        if (!checkdate($month, $day, $year)) {
            $this->_error(self::INVALID);
            return false;
        }

        return true;
    }

}
