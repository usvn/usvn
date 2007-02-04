<?php
/**
* @package client
*/

require_once 'USVN/Client/Client.php';

class TestClient extends PHPUnit2_Framework_TestCase
{
    public function test_noArgs()
    {
        try
        {
            $client = new USVN_Client_Client(array());
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }

    public function test_commandInvalid()
    {
        try
        {
            $client = new USVN_Client_Client(array("tutu"));
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }

    public function test_InvalidArgsMax()
    {
        try
        {
            $client = new USVN_Client_Client(array("version", "tutu"));
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }

    public function test_InvalidArgsMin()
    {
        try
        {
            $client = new USVN_Client_Client(array("install", "tutu"));
        }
        catch (Exception $e)
        {
            return;
        }
        $this->fail();
    }
}