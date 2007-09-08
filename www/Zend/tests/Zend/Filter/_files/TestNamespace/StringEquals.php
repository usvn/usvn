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
 * @version    $Id: Digits.php 4135 2007-03-20 12:46:11Z darby $
 */


/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class TestNamespace_StringEquals extends Zend_Validate_Abstract
{

    const NOT_EQUALS = 'stringNotEquals';

    /**
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_EQUALS => "Not all strings in the argument are equal"
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if all the elements of the array argument
     * are equal to one another with string comparison.
     *
     * @param  array $value
     * @return boolean
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        $initial = (string) current((array)$value);
        foreach ((array) $value as $element) {
            if ((string) $element != $initial) {
                $this->_error();
                return false;
            }
        }
        return true;
    }

}
