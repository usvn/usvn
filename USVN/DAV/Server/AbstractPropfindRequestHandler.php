<?php
/**
* WebDav Server, Abstract request handler for method PROPFIND
*
* You need to inherit of this request handler for handle PROPFIND request
*
* @author Julien Duponchelle
* @package webdav
* @subpackage server
* @since 0.1
*/

require_once 'USVN/DAV/Server/AbstractRequestHandler.php';

abstract class AbstractPropfindRequestHandler extends AbstractRequestHandler
{
    /**
    * Deep into xml document
    *
    * @var int
    */
    private $xml_deep = 0;

    function __construct($server)
    {
        parent::__construct($server);

        $xml = xml_parser_create_ns("UTF-8", " ");
        xml_set_element_handler($xml, array(&$this, "_startTag"), array(&$this, "_endTag"));
        xml_parser_set_option($xml, XML_OPTION_CASE_FOLDING, false); // otherwise xml_parser return tag name in upper case
        xml_parse($xml, $server->getContent(), false);
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
        if ($this->xml_deep == 2) {
            $this->getProperty($tag);
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
}