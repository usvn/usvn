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
 * @package    Zend_Amf
 * @subpackage Response
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Http.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

/** Zend_Amf_Response */
require_once 'Zend/Amf/Response.php';

/**
 * Creates the proper http headers and send the serialized AMF stream to standard out.
 *
 * @package    Zend_Amf
 * @subpackage Response
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Amf_Response_Http extends Zend_Amf_Response
{
    /**
     * Create the application response header for AMF and sends the serialized AMF string
     *
     * @return string
     */
    public function getResponse()
    {
        if (!headers_sent()) {
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
            header('Content-Type: application/x-amf');
        }
        return parent::getResponse();
    }
}
