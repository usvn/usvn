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
    function __construct($server)
    {
        parent::__construct($server);

        @$xml = new SimpleXMLElement($server->getContent());
        foreach ($xml->children('DAV:')->children('http://www.foo.bar/boxschema/') as $prop) {
            $this->getProperty($prop->getName());
        }
    }

    /**
    * Return value of a property
    *
    * @return str
    */
    protected function getProperty($name)
    {
    }
}