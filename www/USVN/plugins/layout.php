<?php
/**
 * Add header and footer to USVN pages
 *
 * @author Team USVN <contact@usvn.info>
 * @link http://www.usvn.info
 * @license http://www.cecill.info/licences/Licence_CeCILL_V2-en.txt CeCILL V2
 * @copyright Copyright 2007, Team USVN
 * @since 0.5
 * @package usvn
 *
 * This software has been written at EPITECH <http://www.epitech.net>
 * EPITECH, European Institute of Technology, Paris - FRANCE -
 * This project has been realised as part of
 * end of studies project.
 *
 * $Id$
 */
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
		$url = Zend_Registry::get('url');
		$site = Zend_Registry::get('site');
		$base_url = $this->getRequest()->getBaseUrl();
		$homepage = T_("Homepage");
		$menu = new USVN_Menu("USVN/modules", $this->getRequest());
		$header = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	    <title>{$url['title']}</title>
		<meta http-equiv="Content-Type"	content="text/html; charset=utf-8" />
		<meta name="description" content="{$url['description']}" />
		<meta name="keywords" content="{$url['keywords']}" />
		<link rel="icon" href="{$base_url}/{$site['ico']}" type="image/x-icon" />
		<link type="text/css" rel="stylesheet" media="screen" href="{$base_url}/css/screen" />
		<link type="text/css" rel="stylesheet" media="print" href="{$base_url}/css/print" />
		<script type="text/javascript" src="{$base_url}/js/"></script>
	</head>
	<body>
		<div id="usvn_header">
			<a id="usvn_logo" href="{$base_url}/">
				<img src="{$base_url}/{$site['logo']}" alt="{$homepage}" />
			</a>
		</div>
		<ul id="usvn_menu">
EOF;
		foreach ($menu->getTopMenu() as $elem) {
			$header .= '<li><a href="' . $base_url . "/" . $elem["link"] . '">'. $elem["title"] . '</a></li>';
		}
		$header .= <<<EOF
		</ul>
EOF;
		if (count($menu->getSubMenu())) {
			$header .= '<ul id="usvn_submenu">';
			foreach ($menu->getSubMenu() as $elem) {
				$header .= '<li><a href="' . $base_url . "/" . $elem["link"] . '">'. $elem["title"] . '</a></li>';
			}
			$header .= "</ul>";
		}
		$header .= <<<EOF
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
		$base_url = $this->getRequest()->getBaseUrl();
		$footer = "";
		//HTMLTableTools
		$footer = "
		<script type='text/javascript'>
			lookForHTMLTableTools(HTMLTableToolsOptions);
		</script>";
		$profiler = Zend_Db_Table::getDefaultAdapter()->getProfiler();
		if (is_array($profiler->getQueryProfiles())) {
			$footer .= "	</div>
								<div id=\"usvn_debug\">
									<div>View debug
									<div id=\"usvn_debug_view\">
							";
			foreach ($profiler->getQueryProfiles() as $query) {
				/* @var $query Zend_Db_Profiler_Query */
				$footer .= "<dl>";
				$footer .= "<dt>" . $query->getElapsedSecs() . "</dt>";
				$footer .= "<dd>" . $query->getQuery() . "</dd>";
				$footer .= "</dl>";
			}
		}
		$footer .= <<<EOF
				</div>
			</div>
		</div>
		<div id="usvn_footer">Powered by USVN</div>
	</body>
</html>
EOF;
		$response->appendBody($footer);
	}
}
