<?php

class layout extends Zend_Controller_Plugin_Abstract
{
	/**
	 * Doit gerer le layout de la page HTML
	 *
	 */
	public function dispatchLoopShutdown()
	{
		$response = $this->getResponse();
		$headers = $response->getHeaders();
		foreach ($headers as $header) {
			if (strcasecmp($header['name'], "Content-Type") == 0) {
				if (strcasecmp($header['value'], "text/html") == 0) {
					$this->addHeader($response);
					$this->addFooter($response);
				}
				break;
			}
		}
	}

	/**
	 * Add header text
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 */
	protected function addHeader($response)
	{
		$header = <<<EOF
<html>
	<head>
	</head>
	<body>
		<div id="usvn_header"></div>
		<div id="usvn_menu"></div>
		<div id="usvn_content">

EOF;
		$body = $response->getBody(true);
		$response->setBody($header);
		foreach ($body as $text) {
			$response->appendBody($text);
		}
	}

	/**
	 * Add footer text
	 *
	 * @param Zend_Controller_Response_Abstract $response
	 */
	protected function addFooter($response)
	{
		$footer = <<<EOF

		</div>
		<div id="usvn_footer">Powered by USVN</div>
	</body>
</html>

EOF;
		$response->appendBody($footer);
	}
}
