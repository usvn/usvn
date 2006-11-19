<?php
/**
* WebDav Server
*
* @link http://www.ietf.org/rfc/rfc2518.txt RFC 2518
* @link http://xmlfr.org/ietf/rfc2518.html RFC 2518 French translation
* @author Julien Duponchelle
* @package webdav
* @subpackage server
* @since 0.1
*/

class Server
{
    /**
    * @var str
    */
    private $request_content;
    /**
    * @var str
    */
    private $request_method;
    /**
    * @var str
    */
    private $request_url;

    /**
    * @var stream Stream with request data, you should never change this.
    */
    public function __construct($data_stream = 'php://input')
    {
        $f = fopen($data_stream, 'r');
        $this->request_content =stream_get_contents($f);
        fclose($f);
        $this->request_method =  $_SERVER['REQUEST_METHOD'];
        $this->request_url = $_SERVER['SCRIPT_NAME'];
    }

    /**
    * Return HTTP method of the request
    *
    * @return str method
    */
    public function getRequestMethod()
    {
        return $this->request_method;
    }

    /**
    * Return the body of the request
    *
    * @return str content
    */
    public function getRequestContent()
    {
        return $this->request_content;
    }

    /**
    * Return the request url for example /toto/tutu
    *
    * @return str url
    */
    public function getRequestUrl()
    {
        return $this->request_url;
    }
}
