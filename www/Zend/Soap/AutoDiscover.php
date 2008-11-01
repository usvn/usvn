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
 * @package    Zend_Soap
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AutoDiscover.php 11815 2008-10-10 02:50:19Z yoshida@zend.co.jp $
 */

require_once 'Zend/Server/Interface.php';
require_once 'Zend/Soap/Wsdl.php';
require_once 'Zend/Server/Reflection.php';
require_once 'Zend/Server/Exception.php';
require_once 'Zend/Server/Abstract.php';
require_once 'Zend/Uri.php';

/**
 * Zend_Soap_AutoDiscover
 * 
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_AutoDiscover extends Zend_Server_Abstract implements Zend_Server_Interface {
    /**
     * @var Zend_Soap_Wsdl
     */
    private $_wsdl = null;

    /**
     * @var Zend_Server_Reflection
     */
    private $_reflection = null;

    /**
     * @var array
     */
    private $_functions = array();

    /**
     * @var boolean
     */
    private $_extractComplexTypes;
    
    /**
     * Constructor
     * 
     * @param boolean $extractComplexTypes
     */
    public function __construct($extractComplexTypes = true)
    {
        $this->_reflection = new Zend_Server_Reflection();
        $this->_extractComplexTypes = $extractComplexTypes;
    }
    
    /**
     * Set the Class the SOAP server will use
     *
     * @param string $class Class Name
     * @param string $namespace Class Namspace - Not Used
     * @param array $argv Arguments to instantiate the class - Not Used
     */
    public function setClass($class, $namespace = '', $argv = null)
    {
        $uri = Zend_Uri::factory('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

        $wsdl = new Zend_Soap_Wsdl($class, $uri, $this->_extractComplexTypes);
        
        $port = $wsdl->addPortType($class . 'Port');
        $binding = $wsdl->addBinding($class . 'Binding', 'tns:' .$class. 'Port');
        
        $wsdl->addSoapBinding($binding, 'rpc');
        $wsdl->addService($class . 'Service', $class . 'Port', 'tns:' . $class . 'Binding', $uri);
        foreach ($this->_reflection->reflectClass($class)->getMethods() as $method) {
            /* <wsdl:portType>'s */
            $portOperation = $wsdl->addPortOperation($port, $method->getName(), 'tns:' .$method->getName(). 'Request', 'tns:' .$method->getName(). 'Response');
            $desc = $method->getDescription();
            if (strlen($desc) > 0) {
                /** @todo check, what should be done for portoperation documentation */
                //$wsdl->addDocumentation($portOperation, $desc);
            }
            /* </wsdl:portType>'s */
            
            $this->_functions[] = $method->getName();

            foreach ($method->getPrototypes() as $prototype) {
                $args = array();
                foreach ($prototype->getParameters() as $param) {
                    $args[$param->getName()] = $wsdl->getType($param->getType());
                }
                $message = $wsdl->addMessage($method->getName() . 'Request', $args);
                if (strlen($desc) > 0) {
                    //$wsdl->addDocumentation($message, $desc);
                }
                if ($prototype->getReturnType() != "void") {
                    $message = $wsdl->addMessage($method->getName() . 'Response', array($method->getName() . 'Return' => $wsdl->getType($prototype->getReturnType())));
                }

                /* <wsdl:binding>'s */
                $operation = $wsdl->addBindingOperation($binding, $method->getName(),  array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"), array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"));
                $wsdl->addSoapOperation($binding, $uri->getUri() . '#' .$method->getName());
                /* </wsdl:binding>'s */
            }
        }
        $this->_wsdl = $wsdl;
    }
    
    /**
     * Add a Single or Multiple Functions to the WSDL
     *
     * @param string $function Function Name
     * @param string $namespace Function namespace - Not Used
     */
    public function addFunction($function, $namespace = '')
    {
        static $port;
        static $operation;
        static $binding;
        
        if (!is_array($function)) {
            $function = (array) $function;
        }
        
        $uri = Zend_Uri::factory('http://'  .$_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME']);

        if (!($this->_wsdl instanceof Zend_Soap_Wsdl)) {
            $parts = explode('.', basename($_SERVER['SCRIPT_NAME']));
            $name = $parts[0];
            $wsdl = new Zend_Soap_Wsdl($name, $uri, $this->_extractComplexTypes);
            
            $port = $wsdl->addPortType($name . 'Port');
            $binding = $wsdl->addBinding($name . 'Binding', 'tns:' .$name. 'Port');
            
            $wsdl->addSoapBinding($binding, 'rpc');
            $wsdl->addService($name . 'Service', $name . 'Port', 'tns:' . $name . 'Binding', $uri);
        } else {
            $wsdl = $this->_wsdl;
        }
        
        foreach ($function as $func) {
            $method = $this->_reflection->reflectFunction($func);
            foreach ($method->getPrototypes() as $prototype) {
                $args = array();
                foreach ($prototype->getParameters() as $param) {
                    $args[$param->getName()] = $wsdl->getType($param->getType());
                }
                $message = $wsdl->addMessage($method->getName() . 'Request', $args);
                $desc = $method->getDescription();
                if (strlen($desc) > 0) {
                    //$wsdl->addDocumentation($message, $desc);
                }
                if ($prototype->getReturnType() != "void") {
                    $message = $wsdl->addMessage($method->getName() . 'Response', array($method->getName() . 'Return' => $wsdl->getType($prototype->getReturnType())));
                }
                 /* <wsdl:portType>'s */
                   $portOperation = $wsdl->addPortOperation($port, $method->getName(), 'tns:' .$method->getName(). 'Request', 'tns:' .$method->getName(). 'Response');
                if (strlen($desc) > 0) {
                    //$wsdl->addDocumentation($portOperation, $desc);
                }
                   /* </wsdl:portType>'s */
            
                /* <wsdl:binding>'s */
                $operation = $wsdl->addBindingOperation($binding, $method->getName(),  array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"), array('use' => 'encoded', 'encodingStyle' => "http://schemas.xmlsoap.org/soap/encoding/"));
                $wsdl->addSoapOperation($binding, $uri->getUri() . '#' .$method->getName());
                /* </wsdl:binding>'s */
            
                $this->_functions[] = $method->getName();
                
                // We will only add one prototype
                break;
            }
        }
        $this->_wsdl = $wsdl;
    }
    
    /**
     * Action to take when an error occurs
     *
     * @todo Imeplement
     * @param string $fault
     * @param string|int $code
     */
    public function fault($fault = null, $code = null)
    {
        
    }
    
    /**
     * Handle the Request
     *
     * @param string $request A non-standard request - Not Used
     */
    public function handle($request = false)
    {
        if (!headers_sent()) {
            header('Content-Type: text/xml');
        }
        $this->_wsdl->dump();
    }
    
    /**
     * Return an array of functions in the WSDL
     *
     * @return array
     */
    public function getFunctions()
    {
        return $this->_functions;    
    }
    
    /**
     * Load Functions
     *
     * @todo Implement
     * @param unknown_type $definition
     */
    public function loadFunctions($definition)
    {
        
    }
    
    /**
     * Set Persistance
     *
     * @todo Implement
     * @param int $mode
     */
    public function setPersistence($mode)
    {

    }

    /**
     * Returns an XSD Type for the given PHP type
     *
     * @param string $type PHP Type to get the XSD type for
     * @return string
     */
    public function getType($type)
    {
        if (!($this->_wsdl instanceof Zend_Soap_Wsdl)) {
            /** @todo Exception throwing may be more correct */

            // WSDL is not defined yet, so we can't recognize type in context of current service
            return '';
        } else {
            return $this->_wsdl->getType($type);
        }
    }
}

