<?php
/**
* WebDav Server, Abstract request handler
*
* Never inherit directly of this file! Use method specific request handler.
*
* @author Julien Duponchelle
* @package webdav
* @subpackage server
* @since 0.1
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
    }
}