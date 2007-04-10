<?php
/**
 * Base class for test of a command
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package client
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'www/USVN/autoload.php';

abstract class USVN_Client_CommandTest extends USVN_Test_DB {
    protected $httpAdapter;
	protected $httpClient;

	public function setUp()
    {
		parent::setUp();
		$data = array(
			"projects_id" => 2,
			"projects_name" => 'project-love',
			"projects_auth" => 'auth007'
		);
		$this->db->insert("usvn_projects", $data);

		$this->clean();
        USVN_Client_SVNUtils::createSvnDirectoryStruct("tests/tmp/testrepository");
        @mkdir('tests/tmp/fakerepository');
        $this->httpAdapter = new Zend_Http_Client_Adapter_Test();
        $this->httpClient = new Zend_Http_Client('http://example.com',
                                    array('adapter' => $this->httpAdapter));
		$this->setServerResponseTo(0);
    }

    public function tearDown()
    {
        $this->clean();
		parent::tearDown();
    }

    private function clean()
    {
        USVN_DirectoryUtils::removeDirectory("tests/tmp/testrepository");
        USVN_DirectoryUtils::removeDirectory('tests/tmp/fakerepository');
    }

	 // Helpers from Zend Framework

    protected function setServerResponseTo($nativeVars)
    {
        $response = $this->getServerResponseFor($nativeVars);
        $this->httpAdapter->setResponse($response);
    }

	protected function getServerResponseFor($nativeVars)
    {
        $response = new Zend_XmlRpc_Response();
        $response->setReturnValue($nativeVars);
        $xml = $response->saveXML();

        $response = $this->makeHttpResponseFrom($xml);
        return $response;
    }

    protected function makeHttpResponseFrom($data, $status=200, $message='OK')
    {
        $headers = array("HTTP/1.1 $status $message",
                         "Status: $status",
                         'Content_Type: text/xml; charset=utf-8',
                         'Content-Length: ' . strlen($data)
                         );
        return implode("\r\n", $headers) . "\r\n\r\n$data\r\n\r\n";
    }
}
?>
