<?php
/**
* @author Julien Duponchelle
* @package webdav
* @subpackage server
* @since 0.1
*/

require_once 'USVN/DAV/Server/AbstractRequestHandler.php';

/**
* WebDav Server, Abstract request handler for method PROPFIND
*
* You need to inherit of this request handler for handle PROPFIND request
*
* @todo HTTP header Depth:
* @todo propname
* @todo access control
*/
abstract class AbstractPropfindRequestHandler extends AbstractRequestHandler
{
    /**
    * List of available properties.
    *
    * @var array
    */
    protected $properties = array();

    /**
    * Deep into parsed xml document
    *
    * @var int
    */
    private $xml_deep = 0;

    /**
    * @var str
    */
    private $xml_response = '';

    function __construct($server)
    {
        parent::__construct($server);

        $xml = xml_parser_create_ns("UTF-8", " ");
        xml_set_element_handler($xml, array(&$this, "_startTag"), array(&$this, "_endTag"));
        xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, false); // otherwise xml_parser return tag name in upper case
        xml_parse($xml, $server->getRequestContent(), false);
        xml_parser_free($xml);
    }

    /**
    * Return value of a property
    *
    * @return str
    */
    protected function getProperty($name)
    {
    }

    /**
    * Add a property to the response
    *
    * @param str Property name
    * @todo namespace
    */
    private function _addProperty($prop)
    {
        $this->xml_response .= "<$prop>".$this->getProperty($prop)."</$prop>";
    }

    /**
     * Start tag XML handler
     *
     * @param  resource  XML parser
     * @param  string    tag name
     * @param  array     tag attributes
     */
    private function _startTag($parser, $name, $attrs)
    {
        if (strstr($name, " ")) {
            list($namespace, $tag) = explode(" ", $name);
        } else {
            $namespace  = "";
            $tag = $name;
        }
        if ($this->xml_deep == 1) {
            if ($tag  == "allprop") {
                foreach ($this->properties as $prop) {
                    $this->_addProperty($prop);
                }
            }
            elseif ($tag  == "propname") {
                foreach ($this->properties as $prop) {
                }
            }
        }
        elseif ($this->xml_deep == 2) {
            $this->_addProperty($tag);
        }
        $this->xml_deep++;
    }

    /**
     * End tag XML handler
     *
     * @param  resource  XML parser
     * @param  string    tag name
     * @param  array     tag attributes
     */
    private function _endTag($parser, $name)
    {
        $this->xml_deep--;
    }

    public function getResponse()
    {
        return '<?xml version="1.0" encoding="utf-8" ?><D:multistatus xmlns:D="DAV:"><D:response>'.$this->xml_response.' </D:response></D:multistatus>';
    }
}