<?php
/**
* A WebDav Server where we can change his content for test purpose
*
* @package webdav
* @subpackage server
*/

require_once 'USVN/DAV/Server/Server.php';

class StubServer extends Server
{
    /**
    * @var str
    */
    public $fake_content;

    public function getContent()
    {
        return $this->fake_content;
    }
}