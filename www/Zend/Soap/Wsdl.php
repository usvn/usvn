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
 * @version    $Id: Wsdl.php 11815 2008-10-10 02:50:19Z yoshida@zend.co.jp $
 */

require_once 'Zend/Server/Exception.php';


/**
 * Zend_Soap_Wsdl
 *
 * @category   Zend
 * @package    Zend_Soap
 */
class Zend_Soap_Wsdl {
    /**
     * @var object DomDocument Instance
     */
    private $_dom;

    /**
     * @var object WSDL Root XML_Tree_Node
     */
    private $_wsdl;

    /**
     * @var string URI where the WSDL will be available
     */
    private $_uri;

    /**
     * @var DOMElement
     */
    private $_schema = null;

    /**
     * Types defined on schema
     *
     * @var array
     */
    private $_includedTypes = array();

    /**
     * @var boolean
     */
    private $_extractComplexTypes;


    /**
     * Constructor
     *
     * @param string  $name Name of the Web Service being Described
     * @param string  $uri URI where the WSDL will be available
     * @param boolean $extractComplexTypes
     */
    public function __construct($name, $uri, $extractComplexTypes = true)
    {
        if ($uri instanceof Zend_Uri_Http) {
            $uri = $this->_uri = $uri->getUri();
        }

        /**
         * @todo change DomDocument object creation from cparsing to construxting using API
         * It also should authomatically escape $name and $uri values if necessary
         */
        $wsdl = "<?xml version='1.0' ?>
                <definitions name='$name' targetNamespace='$uri'
                    xmlns='http://schemas.xmlsoap.org/wsdl/'
                    xmlns:tns='$uri'
                    xmlns:soap='http://schemas.xmlsoap.org/wsdl/soap/'
                    xmlns:xsd='http://www.w3.org/2001/XMLSchema'
                    xmlns:soap-enc='http://schemas.xmlsoap.org/soap/encoding/'></definitions>";
        $this->_dom = new DOMDocument();
        if (!$this->_dom->loadXML($wsdl)) {
            throw new Zend_Server_Exception('Unable to create DomDocument');
        } else {
            $this->_wsdl = $this->_dom->documentElement;
        }

        $this->_extractComplexTypes = $extractComplexTypes;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_messages message} element to the WSDL
     *
     * @param string $name Name for the {@link http://www.w3.org/TR/wsdl#_messages message}
     * @param array $parts An array of {@link http://www.w3.org/TR/wsdl#_message parts}
     *                     The array is constructed like: 'name of part' => 'part xml schema data type'
     * @return object The new message's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addMessage($name, $parts)
    {
        $message = $this->_dom->createElement('message');

        $message->setAttribute('name', $name);

        if (sizeof($parts) > 0) {
            foreach ($parts as $name => $type) {
                $part = $this->_dom->createElement('part');
                $part->setAttribute('name', $name);
                $part->setAttribute('type', $type);
                $message->appendChild($part);
            }
        }

        $this->_wsdl->appendChild($message);

        return $message;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_porttypes portType} element to the WSDL
     *
     * @param string $name portType element's name
     * @return object The new portType's XML_Tree_Node for use in {@link function addPortOperation} and {@link function addDocumentation}
     */
    public function addPortType($name)
    {
        $portType = $this->_dom->createElement('portType');
        $portType->setAttribute('name', $name);
        $this->_wsdl->appendChild($portType);

        return $portType;
    }

    /**
     * Add an {@link http://www.w3.org/TR/wsdl#_request-response operation} element to a portType element
     *
     * @param object $portType a portType XML_Tree_Node, from {@link function addPortType}
     * @param string $name Operation name
     * @param string $input Input Message
     * @param string $output Output Message
     * @param string $fault Fault Message
     * @return object The new operation's XML_Tree_Node for use in {@link function addDocumentation}
     */
    public function addPortOperation($portType, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->_dom->createElement('operation');
        $operation->setAttribute('name', $name);

        if (is_string($input) && (strlen(trim($input)) >= 1)) {
            $node = $this->_dom->createElement('input');
            $node->setAttribute('message', $input);
            $operation->appendChild($node);
        }
        if (is_string($output) && (strlen(trim($output)) >= 1)) {
            $node= $this->_dom->createElement('output');
            $node->setAttribute('message', $output);
            $operation->appendChild($node);
        }
        if (is_string($fault) && (strlen(trim($fault)) >= 1)) {
            $node = $this->_dom->createElement('fault');
            $node->setAttribute('message', $fault);
            $operation->appendChild($node);
        }

        $portType->appendChild($operation);

        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_bindings binding} element to WSDL
     *
     * @param string $name Name of the Binding
     * @param string $type name of the portType to bind
     * @return object The new binding's XML_Tree_Node for use with {@link function addBindingOperation} and {@link function addDocumentation}
     */
    public function addBinding($name, $portType)
    {
        $binding = $this->_dom->createElement('binding');
        $binding->setAttribute('name', $name);
        $binding->setAttribute('type', $portType);

        $this->_wsdl->appendChild($binding);

        return $binding;
    }

    /**
     * Add an operation to a binding element
     *
     * @param object $binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param array $input An array of attributes for the input element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array $output An array of attributes for the output element, allowed keys are: 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @param array $fault An array of attributes for the fault element, allowed keys are: 'name', 'use', 'namespace', 'encodingStyle'. {@link http://www.w3.org/TR/wsdl#_soap:body More Information}
     * @return object The new Operation's XML_Tree_Node for use with {@link function addSoapOperation} and {@link function addDocumentation}
     */
    public function addBindingOperation($binding, $name, $input = false, $output = false, $fault = false)
    {
        $operation = $this->_dom->createElement('operation');
        $operation->setAttribute('name', $name);

        if (is_array($input)) {
            $node = $this->_dom->createElement('input');
            $soap_node = $this->_dom->createElement('soap:body');
            foreach ($input as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }

        if (is_array($output)) {
            $node = $this->_dom->createElement('output');
            $soap_node = $this->_dom->createElement('soap:body');
            foreach ($output as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }

        if (is_array($fault)) {
            $node = $this->_dom->createElement('fault');
            if (isset($fault['name'])) {
                $node->setAttribute('name', $fault['name']);
            }
            $soap_node = $this->_dom->createElement('soap:body');
            foreach ($output as $name => $value) {
                $soap_node->setAttribute($name, $value);
            }
            $node->appendChild($soap_node);
            $operation->appendChild($node);
        }

        $binding->appendChild($operation);

        return $operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:binding SOAP binding} element to a Binding element
     *
     * @param object $binding A binding XML_Tree_Node returned by {@link function addBinding}
     * @param string $style binding style, possible values are "rpc" (the default) and "document"
     * @param string $transport Transport method (defaults to HTTP)
     * @return boolean
     */
    public function addSoapBinding($binding, $style = 'document', $transport = 'http://schemas.xmlsoap.org/soap/http')
    {
        $soap_binding = $this->_dom->createElement('soap:binding');
        $soap_binding->setAttribute('style', $style);
        $soap_binding->setAttribute('transport', $transport);

        $binding->appendChild($soap_binding);

        return $soap_binding;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_soap:operation SOAP operation} to an operation element
     *
     * @param object $operation An operation XML_Tree_Node returned by {@link function addBindingOperation}
     * @param string $soap_action SOAP Action
     * @return boolean
     */
    public function addSoapOperation($binding, $soap_action)
    {
        if ($soap_action instanceof Zend_Uri_Http) {
            $soap_action = $soap_action->getUri();
        }
        $soap_operation = $this->_dom->createElement('soap:operation');
        $soap_operation->setAttribute('soapAction', $soap_action);

        $binding->insertBefore($soap_operation, $binding->firstChild);

        return $soap_operation;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_services service} element to the WSDL
     *
     * @param string $name Service Name
     * @param string $port_name Name of the port for the service
     * @param string $binding Binding for the port
     * @param string $location SOAP Address for the service
     * @return object The new service's XML_Tree_Node for use with {@link function addDocumentation}
     */
    public function addService($name, $port_name, $binding, $location)
    {
        if ($location instanceof Zend_Uri_Http) {
            $location = $location->getUri();
        }
        $service = $this->_dom->createElement('service');
        $service->setAttribute('name', $name);

        $port = $this->_dom->createElement('port');
        $port->setAttribute('name', $port_name);
        $port->setAttribute('binding', $binding);

        $soap_address = $this->_dom->createElement('soap:address');
        $soap_address->setAttribute('location', $location);

        $port->appendChild($soap_address);
        $service->appendChild($port);

        $this->_wsdl->appendChild($service);

        return $service;
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_documentation document} element to any element in the WSDL
     *
     * @param object $input_node An XML_Tree_Node returned by another method to add the document to
     * @param string $document Human readable documentation for the node
     * @return boolean
     */
    public function addDocumentation($input_node, $documentation)
    {
        if ($input_node === $this) {
            $node = $this->_dom->documentElement;
        } else {
            $node = $input_node;
        }

        /** @todo Check if 'documentation' is a correct name for the element (WSDL spec uses 'document') */
        $doc = $this->_dom->createElement('documentation');
        $doc_cdata = $this->_dom->createTextNode($documentation);
        $doc->appendChild($doc_cdata);
        $node->appendChild($doc);

        return $doc;
    }

    /**
     * Add WSDL Types element
     *
     * @param object $types A DomDocument|DomNode|DomElement|DomDocumentFragment with all the XML Schema types defined in it
     */
    public function addTypes($types)
    {
        if ($types instanceof DomDocument) {
            $dom = $this->_dom->importNode($types->documentElement);
            $this->_wsdl->appendChild($types->documentElement);
        } elseif ($types instanceof DomNode || $types instanceof DomElement || $types instanceof DomDocumentFragment ) {
            $dom = $this->_dom->importNode($types);
            $this->_wsdl->appendChild($dom);
        }
    }

    /**
     * Return the WSDL as XML
     *
     * @return string WSDL as XML
     */
    public function toXML()
    {
           return $this->_dom->saveXML();
    }

    /**
     * Return DOM Document
     *
     * @return object DomDocum ent
     */
    public function toDomDocument()
    {
        return $this->_dom;
    }

    /**
     * Echo the WSDL as XML
     *
     * @return boolean
     */
    public function dump($filename = false)
    {
        if (!$filename) {
            echo $this->toXML();
            return true;
        } else {
            return file_put_contents($filename, $this->toXML());
        }
    }

    /**
     * Returns an XSD Type for the given PHP type
     *
     * @param string $type PHP Type to get the XSD type for
     * @return string
     */
    public function getType($type)
    {
        switch ($type) {
            case 'string':
            case 'str':
                return 'xsd:string';
                break;
            case 'int':
            case 'integer':
                return 'xsd:int';
                break;
            case 'float':
            case 'double':
                return 'xsd:float';
                break;
            case 'boolean':
            case 'bool':
                return 'xsd:boolean';
                break;
            case 'array':
                return 'soap-enc:Array';
                break;
            case 'object':
                return 'xsd:struct';
                break;
            case 'mixed':
                return 'xsd:anyType';
                break;
            case 'void':
                return '';
            default:
                if (class_exists($type) && $this->_extractComplexTypes)
                    return $this->addComplexType($type);
                else
                    return 'xsd:anyType';
            }
    }

    /**
     * Add a {@link http://www.w3.org/TR/wsdl#_types types} data type definition
     *
     * @param string $type Name of the class to be specified
     * @return string XSD Type for the given PHP type
     */
    public function addComplexType($type)
    {
        if (in_array($type, $this->_includedTypes)) {
            return "tns:$type";
        }

        if ($this->_schema === null) {
            $this->_schema = $this->_dom->createElement('xsd:schema');
            $this->_schema->setAttribute('targetNamespace', $this->_uri);
            $types = $this->_dom->createElement('types');
            $types->appendChild($this->_schema);
            $this->_wsdl->appendChild($types);
        }

        $class = new ReflectionClass($type);

        $complexType = $this->_dom->createElement('xsd:complexType');
        $complexType->setAttribute('name', $type);

        $all = $this->_dom->createElement('xsd:all');

        foreach ($class->getProperties() as $property) {
            if (preg_match_all('/@var\s+([^\s]+)/m', $property->getDocComment(), $matches)) {

            	/**
            	 * @todo check if 'xsd:element' must be used here (it may not be compatible with using 'complexType'
            	 * node for describing other classes used as attribute types for current class
            	 */
                $element = $this->_dom->createElement('xsd:element');
                $element->setAttribute('name', $property->getName());
                $element->setAttribute('type', $this->getType(trim($matches[1][0])));
                $all->appendChild($element);
            }
        }

        $complexType->appendChild($all);
        $this->_schema->appendChild($complexType);

        $this->_includedTypes[] = $type;

        return "tns:$type";
    }
}
