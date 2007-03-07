<?php
/**
* @package client
* @subpackage hook
* @since 0.5
*/

require_once 'Zend/Http/Client/Adapter/Test.php';
require_once 'USVN/Client/SVNUtils.php';

class HookTest extends PHPUnit2_Framework_TestCase
{
    public function setUp()
    {
        @mkdir('tests/tmp/testrepository');
        @mkdir('tests/tmp/testrepository/usvn');
        $xml = new SimpleXMLElement("<usvn><url>http://example.com</url><auth>007</auth></usvn>");
        file_put_contents('tests/tmp/testrepository/usvn/config.xml', $xml->asXml());

    }

	protected function setHttp()
	{
        $this->httpAdapter = new Zend_Http_Client_Adapter_Test();
        $this->httpClient = new Zend_Http_Client('http://example.com',
                                    array('adapter' => $this->httpAdapter));
		$this->hook->setHttpClient($this->httpClient);
	}

	/**
	* Replacement for USVN_Client_SVNUtils::svnLookTransaction
	*
	* @param string svnlook command (see svnlook help)
	* @param string repository path
	* @param string transaction (call TXN into svn hooks samples)
	* @return string Output of svnlook
	* @see http://svnbook.red-bean.com/en/1.1/ch09s03.html
	*/
	protected function svnLook($command, $repository, $transaction)
	{
		return USVN_Client_SVNUtils::svnLookTransaction($command, $repository, $transaction);
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