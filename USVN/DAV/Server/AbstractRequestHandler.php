<?php
/**
* @author Julien Duponchelle
* @package webdav
* @subpackage server
* @since 0.1
*/


/**
* WebDav Server, Abstract request handler
*
* Never inherit directly of this class! Use method specific request handler.
*
*/
abstract class AbstractRequestHandler
{
    /**
    * @var Server WebDav Server
    */
    protected $server;

    /**
    * @param Server WebDav server
    */
    function __construct($server)
    {
        $this->server = $server;
    }


    /**
    * Get boby response
    *
    * @return str
    */
    public function getResponse()
    {
    }
}