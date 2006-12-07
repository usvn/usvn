<?php
/**
* A WebDav Server where we can change his content for test purpose
*
* @package test
*/

require_once 'USVN/DAV/Server/Server.php';

class StubServer extends USVN_DAV_Server_Server
{
    /**
    * @var str
    */
    public $fake_content;

    public function getRequestContent()
    {
        return $this->fake_content;
    }
}